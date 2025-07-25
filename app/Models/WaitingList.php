<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    protected $table = 'waiting_lists';

    protected $fillable = [
        'name',
        'email',
        'signup_source',
    ];
}
