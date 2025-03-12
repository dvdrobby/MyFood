<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function transactions()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function food()
    {
        return $this->belongsTo(Food::class, 'food_id');
    }
}
