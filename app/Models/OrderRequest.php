<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRequest extends Model
{
    use HasFactory;

    protected $fillable = ['request_number', 'dealer_id', 'status', 'note'];

    public static function generateRequestNumber(): string
    {
        $year   = date('Y');
        $last   = self::whereYear('created_at', $year)->latest('id')->first();
        $nextId = $last ? ($last->id + 1) : 1;
        return 'REQ-' . $year . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
    public function items()
    {
        return $this->hasMany(OrderRequestItem::class);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id'); 
    }
}