<?php

namespace App\Modules\User\Actions;

use App\Core\Abstracts\Action;
use App\Models\User;

class UpdateUser extends Action
{
    public function execute(User $user, array $data): User
    {
        $user->update([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'language' => $data['language'],
        ]);

        return $user->refresh();
    }
}
