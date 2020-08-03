<?php

namespace App\Observers;

use App\Models\User;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class UserObserver
{
    public function creating(User $user)
    {
        //
    }

    public function updating(User $user)
    {
        //
    }

    public function saving(User $user)
    {
        if (!$user->avatar) {
            $user->avatar = 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1593527892738&di=c0196e15545bffbce5a974de017c6e41&imgtype=0&src=http%3A%2F%2Fdmimg.5054399.com%2Fallimg%2Fpkm%2Fpk%2F13.jpg';
        }
    }
}
