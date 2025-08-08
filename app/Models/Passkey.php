<?php

namespace App\Models;

use WendellAdriel\Lift\Lift;
use Illuminate\Database\Eloquent\Model;

class Passkey extends Model
{
    use Lift;
    protected $fillable = [
        'user_id',
        'credential_id',
        'public_key'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
