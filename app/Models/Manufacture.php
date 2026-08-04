<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacture extends Model
{
    protected $fillable = [
        'company_id',
        'product_id',
        'invoice_no',
        'reff_invoice',
        'dealer_id',
        'dealer_name',
        'dealer_phone',
        'dealer_address',
        'worker_id',
        'manufacture_qty',
        'body_part_price',
        'finishing_part_price',
        'body_total',
        'finishing_total',
        'grand_total',
        'note',
        'is_confirm',
        'status',
        'completed_by',
        'collected_by',
        'created_by',
        'updated_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }
}
