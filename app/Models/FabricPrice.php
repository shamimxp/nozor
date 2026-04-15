<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * FabricPrice Model
 *
 * Represents a price configuration for a specific fabric, type and sleeve.
 */
class FabricPrice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fabric_id',
        'type',
        'sleeve',
        'price',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the fabric that belongs to this price entry.
     */
    public function fabric()
    {
        return $this->belongsTo(Fabric::class);
    }
}
