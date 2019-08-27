<?php

namespace GeoLV\Http\Controllers;

use GeoLV\Address;
use GeoLV\AddressCollection;
use GeoLV\Geocode\Clusters\ClusterWithScipy;
use GeoLV\Geocode\GeocodingFileReader;
use GeoLV\GeocodingFile;
use GeoLV\Http\Requests\UploadRequest;
use GeoLV\Jobs\GenerateFileResultsCache;
use GeoLV\Jobs\GeocodeNextFile;
use GeoLV\Search;
use GeoLV\User;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GeocodingFileController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();
        $query = $user->can('view', User::class) ? GeocodingFile::with('user') : $user->files();
        $files = $query
            ->orderBy('done', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('files.index', compact('files'));
    }

    public function create()
    {
        $fields = ['street_name', 'street_number', 'locality', 'state', 'postal_code', 'sub_locality', 'country_code',
            'country_name', 'provider', 'latitude', 'longitude', 'dispersion', 'clusters_count', 'providers_count',
            'levenshtein_match_street_name'];
        $default = ['latitude', 'longitude', 'dispersion'];
        $providers = ['google_maps', 'here_geocoder', 'bing_maps', 'arcgis_online'];
        $defaultProviders = ['google_maps', 'here_geocoder'];

        return view('files.create', compact('fields', 'providers', 'default', 'defaultProviders'));
    }

    public function store(UploadRequest $request)
    {
        $indexes = json_decode($request->get('indexes'), true);
        $file = $request->file('geocode_file');
        $user = $request->user();

        $user->files()->create([
            'path' => $file->store('pre-processing', ['disk' => 's3']),
            'name' => $file->getClientOriginalName(),
            'header' => $request->has('header'),
            'fields' => $request->get('fields'),
            'count' => $request->get('count'),
            'delimiter' => $request->get('delimiter'),
            'providers' => $request->get('providers'),
            'indexes' => $indexes
        ]);

        GeocodeNextFile::dispatch();

        return redirect()->route('files.index')->with('upload', true);
    }

    /**
     * @param Request $request
     * @param GeocodingFile $file
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function prioritize(Request $request, GeocodingFile $file)
    {
        $this->validate($request, ['priority' => 'integer|min:0']);
        $this->authorize($file);

        $file->priority = $request->get('priority', 0);
        $file->save();

        GeocodeNextFile::dispatch();

        return redirect()->back();
    }

    /**
     * @param GeocodingFile $file
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancel(GeocodingFile $file)
    {
        $this->authorize($file);

        if (!$file->toggleCancel()) {
            GeocodeNextFile::dispatch();
        }

        return redirect()->back();
    }

    /**
     * @param GeocodingFile $file
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function download(GeocodingFile $file)
    {
        $this->authorize('view', $file);

        try {
            return Storage::disk('s3')->download($file->output_path, $file->name);
        } catch (FileNotFoundException $exception) {
            throw new NotFoundHttpException('file not found');
        }
    }

    /**
     * @param Request $request
     * @param GeocodingFile $file
     * @param ClusterWithScipy $cluster
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function map(Request $request, GeocodingFile $file, ClusterWithScipy $cluster)
    {
        $this->authorize('view', $file);

        //Resolve file
        $results_key = "files.{$file->id}.results";
        $callbackResults = function () use ($file) {
            return $this->getFileResults($file);
        };
        $results = $file->done ? Cache::rememberForever($results_key, $callbackResults)
            : Cache::remember($results_key, 300, $callbackResults);

        //Resolve clusters
        $max_d = $request->get('max_d', Search::DEFAULT_MAX_D);
        $clusters_key = "files.{$file->id}.{$max_d}.clusters";
        $callbackClusters = function () use ($results, $max_d) {
            return $this->getResultsClusters($results, $max_d);
        };
        $clusters = $file->done ? Cache::rememberForever($clusters_key, $callbackClusters)
            : Cache::remember($clusters_key, 300, $callbackClusters);

        return view('files.map', compact('file', 'results', 'max_d', 'clusters'));
    }

    /**
     * @param GeocodingFile $file
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function downloadErrors(GeocodingFile $file)
    {
        $this->authorize('view', $file);

        try {
            return Storage::disk('s3')->download($file->error_output_path, $file->error_name);
        } catch (FileNotFoundException $exception) {
            throw new NotFoundHttpException('file not found');
        }
    }

    /**
     * @param GeocodingFile $file
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function destroy(GeocodingFile $file)
    {
        $this->authorize('delete', $file);

        Storage::disk('s3')->delete([
            $file->output_path,
            $file->error_output_path
        ]);

        $file->delete();
        return redirect()->back();
    }

    private function getFileResults(GeocodingFile $file)
    {
        $results = collect();
        $reader = new GeocodingFileReader($file);

        try {
            $data = $reader->read(GeocodingFileReader::POST_PROCESSED_FILE);
            $n_fields = count($file->fields);
            $lat_idx = array_search('latitude', $file->fields);
            $lng_idx = array_search('longitude', $file->fields);

            foreach ($data as $row) {
                $n_cols = count($row) - $n_fields;

                $results->add([
                    'text' => $reader->getField($row, 'text'),
                    'latitude' => $row[$n_cols + $lat_idx],
                    'longitude' => $row[$n_cols + $lng_idx],
                ]);
            }
        } catch (\Exception $e) {
            report($e);
        }

        return $results;
    }

    /**
     * @param $results
     * @param $max_d
     * @return mixed
     */
    private function getResultsClusters($results, $max_d)
    {
        $cluster = new ClusterWithScipy();
        $cluster->apply($results, $max_d);
        return $results->groupBy('cluster')->map(function ($results, $cluster) {
            $count = count($results);
            return compact('cluster', 'count');
        })->values()->sortByDesc('count');
    }

}
