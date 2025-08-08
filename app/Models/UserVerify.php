<?php

namespace App\Models;

use WendellAdriel\Lift\Lift;
use Illuminate\Database\Eloquent\Model;

class UserVerify extends Model
{
    use Lift;
    protected $table = 'user_verify';
}
