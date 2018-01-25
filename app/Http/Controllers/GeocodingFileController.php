<?php

namespace GeoLV\Http\Controllers;

use GeoLV\GeocodingFile;
use GeoLV\Http\Requests\UploadRequest;
use GeoLV\Jobs\ProcessGeocodingFile;
use GeoLV\Mail\DoneGeocodingFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeocodingFileController extends Controller
{
    public function create()
    {
        return view('files.preload');
    }

    public function store(UploadRequest $request)
    {
        $path = $request->file('geocode_file')->store('pre-processing', ['disk' => 's3']);
        $file = GeocodingFile::create([
            'path' => $path,
            'email' => $request->get('email'),
            'indexes' => json_decode($request->get('indexes'), true)
        ]);

        $this->dispatch(new ProcessGeocodingFile($file));

        return redirect()->back()->with('upload', true);
    }

    public function show(GeocodingFile $file)
    {
        return Storage::disk('s3')->download($file->output_path);
    }

    public function email(GeocodingFile $file)
    {
        return new DoneGeocodingFile($file);
    }
}
