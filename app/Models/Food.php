<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'images',
        'price',
        'price_afterdiscount',
        'percent',
        'is_promo',
        'category_id'
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
