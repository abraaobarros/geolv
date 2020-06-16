<?php

namespace GeoLV\Http\Controllers;

use GeoLV\Http\Requests\ProfileRequest;
use GeoLV\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function getProfile()
    {
        $user = auth()->user();
        return view('users.profile', compact('user'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        /** @var User $user */
        $user = $request->user();
        $user->update($request->only(['name']));
        $user->provider('google_maps', $request->input('google_maps.api_key'));
        $user->provider('here_geocoder', $request->input('here_geocoder.api_key'));
        $user->provider('bing_maps', $request->input('bing_maps.api_key'));

        return redirect()->back()->with('profile.updated', true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('view', User::class);

        $users = User::paginate();
        return view('users.index', compact('users'));
    }

    /**
     * Display the specified resource.
     *
     * @param \GeoLV\User $user
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(User $user)
    {
        $this->authorize('view', User::class);

        $files = $user->files()
            ->withTrashed()
            ->orderBy('done', 'asc')
            ->orderBy('updated_at', 'desc')
            ->paginate();

        return view('users.show', compact('user', 'files'));
    }

    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);

        $user->delete();

        return redirect()->back();
    }

}
