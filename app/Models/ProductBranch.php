<?php

namespace App\Models;

use WendellAdriel\Lift\Lift;
use App\Models\Master\Branch;
use App\Models\Master\Attribute;
use App\Models\Transaction\RentItem;
use Illuminate\Database\Eloquent\Model;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;

#[BelongsTo(Branch::class)]
#[BelongsTo(Product::class)]
class ProductBranch extends Model
{
    use Lift;
    public function color()
    {
        return $this->belongsTo(Attribute::class, 'color_id');
    }

    public function storage()
    {
        return $this->belongsTo(Attribute::class, 'storage_id');
    }
    public function warna($value){
        $color = match($value) {
            // iPhone 7 Series
            "Black" => '#000000',
            "Silver" => '#C0C0C0',
            "Gold" => '#FFD700',
            "Rose Gold" => '#B76E79',
            "Jet Black" => '#0A0A0A',
            "Red" => '#FF0000',
            
            // iPhone 8/X Series
            "Space Gray" => '#535150',
            
            // iPhone XR
            "White" => '#FFFFFF',
            "Blue" => '#007AFF',
            "Yellow" => '#FFD60A',
            "Coral" => '#FF7E79',
            
            // iPhone 11 Series
            "Green" => '#A7D3A6',
            "Purple" => '#D1B3FF',
            "Midnight Green" => '#475C4D',
            
            // iPhone 12 Series
            "Pacific Blue" => '#2D5F7A',
            "Graphite" => '#4F4F4F',
            
            // iPhone 13 Series
            "Midnight" => '#000000',
            "Starlight" => '#F8F9F0',
            "Pink" => '#FFB6C1',
            "Sierra Blue" => '#9BB5CE',
            
            // iPhone 14 Series
            "Deep Purple" => '#4A3C5F',
            "Space Black" => '#333333',
            
            // iPhone 15 Series
            "Black Titanium" => '#2D2D2D',
            "White Titanium" => '#E5E4E2',
            "Blue Titanium" => '#5E7E9B',
            "Natural Titanium" => '#8B8B8B',
            
            // iPhone 16 Series (Prediksi)
            "Deep Blue" => '#003366',
            // Samsung A50s
            "Prism Crush Black" => '#000000',
            "Prism Crush White" => '#FFFFFF',
            "Prism Crush Green" => '#C5E3B1',
            "Prism Crush Violet" => '#C6B5D6',
            
            // Samsung S22 Ultra
            "Phantom Black" => '#2D2926',
            "Phantom White" => '#EAE7DE',
            "Green" => '#A7D3A6',
            "Burgundy" => '#800020',
            "Graphite" => '#4F4F4F',
            "Sky Blue" => '#87CEEB',
            "Red" => '#FF0000',
            
            // Samsung S23 Ultra
            "Cream" => '#F5F5DC',
            "Lavender" => '#E6E6FA',
            "Lime" => '#BFFF00',
            
            // Samsung S24 Ultra
            "Titanium Black" => '#2B2B2B',
            "Titanium Gray" => '#8E8E8E',
            "Titanium Violet" => '#B399D4',
            "Titanium Yellow" => '#FFD700',
            "Titanium Blue" => '#4682B4',
            "Titanium Green" => '#2E8B57',
            "Titanium Orange" => '#FF8C00',
            
            // Samsung S25 Ultra (Prediksi)
            "Onyx Black" => '#0F0F0F',
            "Marble White" => '#F2F0EB',
            "Cobalt Violet" => '#8A2BE2',
            "Amber Yellow" => '#FFBF00',
            "Sapphire Blue" => '#0F52BA',
            "Emerald Green" => '#50C878',
            
            // Google Pixel
            "Obsidian" => '#0B1215',
            "Hazel" => '#8E7618',
            "Rose" => '#FF007F',
            "Snow" => '#FFFAFA',
            default => $value
        };
        return $color;
    }
    public function isAvailable($startDate, $endDate, $quantity, $exceptRentId = null)
    {
        $rented = RentItem::where('product_branch_id', $this->id)
            ->whereHas('rent', function($query) use ($startDate, $endDate, $exceptRentId) {
                $query->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<', $startDate)
                            ->where('end_date', '>', $endDate);
                    });
                })
                ->whereIn('status', ['confirmed', 'active', 'overdue']);
                
                if ($exceptRentId) {
                    $query->where('id', '!=', $exceptRentId);
                }
            })
            ->sum('quantity');
        $stok = ProductBranch::where('branch_id', $this->branch_id)->where('product_id', $this->product_id)->where('color_id', $this->color_id)->count();
        // Stock tersedia = stock total - booked
        return ($stok - $rented) >= $quantity;
    }
}
