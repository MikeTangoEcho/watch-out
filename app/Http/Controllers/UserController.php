<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\User;
use App\Http\Requests\EditUser;
use App\Http\Requests\EditUserPassword;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct() {
        $this->authorizeResource(User::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::whereNotNull('email_verified_at')
            ->withCount(['streams'  => function($query) {
                return $query->whereHas('firstChunk');
            }]);
        return view('users', ['users' => $users->paginate()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the user.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('user_edit', ['user' => $user]);
    }

    /**
     * Update the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(EditUser $request, User $user)
    {
        $userValidated = $request->validated();
        $user->name = $userValidated['name'];
        $user->save();
        Log::info('User [' . $user->id . '] updated');

        return redirect()
            ->route('users.edit', ['user' => $user->id])
            ->with('success_message', __('User updated!'));
    }

    /**
     * Update password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(EditUserPassword $request, User $user)
    {
        $this->authorize('update', $user);

        // TODO Check if locked or banned
        $userValidated = $request->validated();
        $this->validate($request, [
            'password' => [
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Invalid :attribute. Hint: forget-password can help you.');
                    }
                }
            ]
        ]);
        $user->password = Hash::make($userValidated['new_password']);
        $user->save();
        Log::info('User [' . $user->id . '] updated password');

        return redirect()->route('users.edit', ['user' => $user->id])
            ->with('success_message', __('Password updated!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
