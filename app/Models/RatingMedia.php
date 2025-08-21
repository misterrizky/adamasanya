<?php

namespace App\Models;

use WendellAdriel\Lift\Lift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class RatingMedia extends Model
{
    use Lift;
    protected $fillable = ['rating_id', 'media_path', 'mime_type', 'order'];

    public function rating()
    {
        return $this->belongsTo(Rating::class);
    }
    public function getImageAttribute()
    {
        if(file_exists(public_path('storage/'.$this->media_path))){
            return asset('storage/'.$this->media_path);
        }else{
            return asset('storage/'.$this->media_path);
        }
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->media_path);
    }
}
