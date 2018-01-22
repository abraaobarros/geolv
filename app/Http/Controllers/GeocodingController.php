<?php

namespace GeoLV\Http\Controllers;

use GeoLV\Address;
use GeoLV\Geocode\Dictionary;
use GeoLV\GeocodingFile;
use GeoLV\Http\Requests\GeocodingRequest;
use GeoLV\Http\Requests\UploadRequest;
use GeoLV\Jobs\ProcessGeocodingFile;
use GeoLV\Locality;
use GeoLV\Search;
use Illuminate\Http\Request;

class GeocodingController extends Controller
{
    private $geocoder;

    /**
     * GeocodingController constructor.
     */
    public function __construct()
    {
        $this->geocoder = app('geocoder');
    }

    public function index()
    {
        $results = collect();
        $match = null;
        $localities = Locality::all();

        return view('geocode', compact('results', 'match', 'localities'));
    }

    public function geocode(GeocodingRequest $request)
    {
        $text = Dictionary::address($request->get('text'));
        $locality = $request->get('locality');
        $postalCode = $request->get('postal_code');
        $localities = Locality::all();

        $results = $this->geocoder->geocode($text, $locality, $postalCode);

        return view('geocode', compact('results', 'text', 'locality', 'postalCode', 'localities'));
    }

    public function map(Request $request)
    {
        $search = Search::findOrFail($request->get('search_id'));
        $selected = Address::findOrFail($request->get('selected_id'));
        $results = $this->geocoder->get($search);

        return view('map', compact('results', 'selected'));
    }

    public function preload()
    {
        return view('preload');
    }

    public function upload(UploadRequest $request)
    {
        $path = $request->file('geocode_file')->store('pre-processing');
        $file = GeocodingFile::create([
            'path' => $path,
            'email' => $request->get('email'),
            'indexes' => json_decode($request->get('indexes'), true)
        ]);

        $this->dispatch(new ProcessGeocodingFile($file));

        return redirect()->back()->with('upload', true);
    }

}
