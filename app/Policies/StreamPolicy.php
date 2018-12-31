<?php

namespace App\Policies;

use App\User;
use App\Stream;
use Illuminate\Auth\Access\HandlesAuthorization;

class StreamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the stream.
     *
     * @param  \App\User  $user
     * @param  \App\Stream  $stream
     * @return mixed
     */
    public function view(?User $user, Stream $stream)
    {
        return true;
    }

    /**
     * Determine whether the user can create streams.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can update the stream.
     *
     * @param  \App\User  $user
     * @param  \App\Stream  $stream
     * @return mixed
     */
    public function update(User $user, Stream $stream)
    {
        return $user->hasVerifiedEmail() 
            && $user->id == $stream->user_id;
    }

    /**
     * Determine whether the user can delete the stream.
     *
     * @param  \App\User  $user
     * @param  \App\Stream  $stream
     * @return mixed
     */
    public function delete(User $user, Stream $stream)
    {
        //
    }

    /**
     * Determine whether the user can restore the stream.
     *
     * @param  \App\User  $user
     * @param  \App\Stream  $stream
     * @return mixed
     */
    public function restore(User $user, Stream $stream)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the stream.
     *
     * @param  \App\User  $user
     * @param  \App\Stream  $stream
     * @return mixed
     */
    public function forceDelete(User $user, Stream $stream)
    {
        //
    }
}
