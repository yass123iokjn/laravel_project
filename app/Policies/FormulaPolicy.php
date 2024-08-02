<?php

namespace App\Policies;

use App\Models\Formula;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FormulaPolicy
{
    public function update(User $user, Formula $formula)
{
    return $user->id === $formula->user_id;
}

public function delete(User $user, Formula $formula)
{
    return $user->id === $formula->user_id;
}
}
