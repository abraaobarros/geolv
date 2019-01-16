<?php

namespace GeoLV\Http\Controllers;

use GeoLV\GeocodingFile;
use GeoLV\Http\Requests\UploadRequest;
use GeoLV\Jobs\ProcessGeocodingFile;
use GeoLV\Mail\DoneGeocodingFile;
use GeoLV\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeocodingFileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = $user->can('view', User::class) ? GeocodingFile::with('user') : $user->files();
        $files = $query
            ->orderBy('priority', 'desc')
            ->orderBy('done', 'asc')
            ->orderBy('updated_at', 'desc')
            ->paginate();

        return view('files.index', compact('files'));
    }

    public function create()
    {
        $fields = [
            'street_name',
            'street_number',
            'locality',
            'postal_code',
            'sub_locality',
            'country_code',
            'country_name',
            'provider',
            'latitude',
            'longitude',
            'dispersion',
            'clusters_count',
            'providers_count',
            'levenshtein_match_street_name'
        ];

        $default = [
            'latitude',
            'longitude',
            'dispersion'
        ];

        return view('files.preload', compact('fields', 'default'));
    }

    public function store(UploadRequest $request)
    {
        $indexes = json_decode($request->get('indexes'), true);
        $path = $request
            ->file('geocode_file')
            ->store('pre-processing', ['disk' => 's3']);

        /** @var GeocodingFile $file */
        auth()->user()->files()->create([
            'path' => $path,
            'header' => $request->has('header'),
            'fields' => $request->get('fields'),
            'count' => $request->get('count'),
            'delimiter' => $request->get('delimiter'),
            'indexes' => $indexes
        ]);

        $this->dispatch(new ProcessGeocodingFile());

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
        $this->authorize('prioritize', $file);

        $file->priority = $request->get('priority', 0);
        $file->save();

        return redirect()->route('files.index');
    }

    /**
     * @param GeocodingFile $file
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(GeocodingFile $file)
    {
        $this->authorize('view', $file);

        return Storage::disk('s3')->download($file->output_path);
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

    public function email(GeocodingFile $file)
    {
        return new DoneGeocodingFile($file);
    }
}
