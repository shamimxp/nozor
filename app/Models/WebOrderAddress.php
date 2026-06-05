<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebOrderAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'web_order_id',
        'name',
        'phone',
        'address',
        'note',
    ];

    public function order()
    {
        return $this->belongsTo(WebOrder::class);
    }
}
