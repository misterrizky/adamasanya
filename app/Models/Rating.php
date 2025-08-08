<?php

namespace App\Models;

use WendellAdriel\Lift\Lift;
use Illuminate\Database\Eloquent\Model;
use WendellAdriel\Lift\Attributes\Relations\HasMany;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;

#[BelongsTo(User::class)]
class Rating extends Model
{
    use Lift;
    public function media(){
        return $this->hasMany(RatingMedia::class);
    }
}
