<?php

namespace App\Models\Master;

use App\Models\Product;
use Laravolt\Avatar\Avatar;
use WendellAdriel\Lift\Lift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use Lift;
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    public function getImageAttribute()
    {
        if(!$this->thumbnail){
            $avatar = new Avatar();
            return $avatar->create($this->name)->toBase64();
        }else{
            if(file_exists(public_path('storage/'.$this->thumbnail))){
                return asset('storage/'.$this->thumbnail);
            }else{
                return asset('media/avatars/blank.png');
            }
        }
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
