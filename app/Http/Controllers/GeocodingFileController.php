<?php

namespace GeoLV\Http\Controllers;

use GeoLV\Geocode\Clusters\ClusterWithScipy;
use GeoLV\Geocode\GeoLVPythonService;
use GeoLV\GeocodingFile;
use GeoLV\Http\Requests\UploadRequest;
use GeoLV\Jobs\GeocodeNextFile;
use GeoLV\Jobs\ProcessFilePoints;
use GeoLV\Search;
use GeoLV\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GeocodingFileController extends Controller
{
    private $storage;

    /**
     * GeocodingFileController constructor.
     */
    public function __construct()
    {
        $this->storage = Storage::disk('s3');
    }

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
            'levenshtein_match_street_name', 'match_locality', 'formatted_address'];
        $default = ['latitude', 'longitude', 'dispersion'];
        $providers = ['google_maps', 'here_geocoder', 'bing_maps', 'arcgis_online'];
        $defaultProviders = session('geocode.default_providers', ['google_maps', 'here_geocoder']);

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
            return $this->storage->download($file->output_path, $file->name);
        } catch (FileNotFoundException $exception) {
            throw new NotFoundHttpException('file not found');
        }
    }

    /**
     * @param Request $request
     * @param GeocodingFile $file
     * @param GeoLVPythonService $cluster
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function map(Request $request, GeocodingFile $file, GeoLVPythonService $cluster)
    {
        $this->authorize('view', $file);

        //Resolve file
        $results_key = "files.{$file->id}.results";
        $max_d = $request->get('max_d', Search::DEFAULT_MAX_D);
        $results = Cache::get($results_key);

        if ($results instanceof Collection) {
            $emptyResults = $results->isEmpty();
        } else {
            $emptyResults = empty($results);
        }

        if ($emptyResults) {
            $results = collect();
            $clusters = collect();
            $processing = true;

            $this->dispatch(new ProcessFilePoints($file));
        } else {
            $clusters = $this->getResultsClusters($results, $max_d);
            $processing = false;
        }

        return view('files.map', compact('file', 'results', 'max_d', 'clusters', 'processing'));
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
            return $this->storage->download($file->error_output_path, $file->error_name);
        } catch (FileNotFoundException $exception) {
            throw new NotFoundHttpException('file not found');
        }
    }

    public function downloadOriginal(GeocodingFile $file)
    {
        $this->authorize('view', $file);

        try {
            return $this->storage->download($file->path, $file->name);
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

        $file->delete();
        return redirect()->back();
    }

    /**
     * @param $results
     * @param float $maxDistance
     * @return mixed
     */
    private function getResultsClusters(Collection $results, $maxDistance)
    {
        $cluster = new ClusterWithScipy();
        $cluster->apply($results, $maxDistance);

        return $results->groupBy('cluster')->map(function ($results, $cluster) {
            $count = $results->count();
            return compact('cluster', 'count');
        })->values()->sortByDesc('count');
    }

}
