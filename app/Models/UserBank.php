<?php

namespace App\Models;

use WendellAdriel\Lift\Lift;
use Illuminate\Database\Eloquent\Model;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;

#[BelongsTo(User::class)]
class UserBank extends Model
{
    use Lift;
}
