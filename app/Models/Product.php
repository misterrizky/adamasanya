<?php

namespace App\Models;

use Laravolt\Avatar\Avatar;
use App\Models\Master\Brand;
use WendellAdriel\Lift\Lift;
use App\Models\Master\Category;
use Illuminate\Database\Eloquent\Model;
use Milwad\LaravelAttributes\Traits\Attributable;
use WendellAdriel\Lift\Attributes\Relations\HasMany;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;

#[BelongsTo(Brand::class)]
#[BelongsTo(Category::class)]
#[HasMany(ProductBranch::class)]
#[HasMany(Rating::class)]
class Product extends Model
{
    use Lift, Attributable;
    public function getImageAttribute()
    {
        if(!$this->thumbnail){
            $avatar = new Avatar();
            return $avatar->create($this->name)->toBase64();
        }else{
            if(file_exists(public_path('storage/'.$this->thumbnail))){
                return asset('storage/'.$this->thumbnail);
            }else{
                return asset('storage/'.$this->thumbnail);
            }
        }
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function averageRating()
    {
        return $this->ratings()->avg('rating') ?? 0; // Default 0 jika belum ada rating
    }

    public function ratingsCount()
    {
        return $this->ratings()->count();
    }
}
