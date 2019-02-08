<?php

namespace GeoLV\Http\Controllers;

use GeoLV\GeocodingFile;
use GeoLV\Http\Requests\UploadRequest;
use GeoLV\Jobs\GeocodeNextFile;
use GeoLV\User;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('files.index', compact('files'));
    }

    public function create()
    {
        $fields = ['street_name', 'street_number', 'locality', 'state', 'postal_code', 'sub_locality', 'country_code',
            'country_name', 'provider', 'latitude', 'longitude', 'dispersion', 'clusters_count', 'providers_count',
            'levenshtein_match_street_name', 'precision', 'confidence'];
        $default = ['latitude', 'longitude', 'dispersion', 'precision', 'confidence'];
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

        $this->dispatch(new GeocodeNextFile());

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

        $this->dispatch(new GeocodeNextFile());

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
            $this->dispatch(new GeocodeNextFile());
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
            return Storage::disk('s3')->download($file->output_path);
        } catch (FileNotFoundException $exception) {
            throw new NotFoundHttpException('file not found');
        }
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
            return Storage::disk('s3')->download($file->error_output_path);
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

}
