<?php

namespace GeoLV\Http\Controllers;

use GeoLV\Address;
use GeoLV\AddressCollection;
use GeoLV\Geocode\Clusters\ClusterWithScipy;
use GeoLV\Geocode\GeocodingFileReader;
use GeoLV\GeocodingFile;
use GeoLV\Http\Requests\UploadRequest;
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

        $max_d = $request->get('max_d', Search::DEFAULT_MAX_D);
        $key = "files.{$file->id}.results";
        $callback = function () use ($file, $cluster, $max_d) {
            $results = $this->getFileResults($file);
            $cluster->apply($results, $max_d);
            return $results;
        };

        if ($file->done) {
            $results = Cache::rememberForever($key, $callback);
        } else {
            $results = Cache::remember($key, 300, $callback); //lives for 5 minutes
        }

        return view('files.map', compact('file', 'results', 'max_d'));
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

    /**
     * @param GeocodingFile $file
     * @return AddressCollection
     */
    private function getFileResults(GeocodingFile $file): AddressCollection
    {
        $results = new AddressCollection();
        $reader = new GeocodingFileReader($file);

        try {
            $data = $reader->read(GeocodingFileReader::POST_PROCESSED_FILE);
            $n_fields = count($file->fields);
            $lat_idx = array_search('latitude', $file->fields);
            $lng_idx = array_search('longitude', $file->fields);

            foreach ($data as $row) {
                $n_cols = count($row) - $n_fields;

                $results->add(new Address([
                    'street_name' => $reader->getField($row, 'text'),
                    'latitude' => $row[$n_cols + $lat_idx],
                    'longitude' => $row[$n_cols + $lng_idx],
                    'provider' => 'geolv'
                ]));
            }
        } catch (\League\Csv\Exception $e) {
            report($e);
        }

        return $results;
    }

}
