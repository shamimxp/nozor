<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Dealer extends Authenticatable
{
    use HasFactory, TracksSoftDeletion, HasRoles;
    protected $guarded = ['id'];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'wallet_balance' => 'decimal:2',
        'is_special'     => 'boolean',
        'status'         => 'integer',
    ];

    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function orders()
    {
        return $this->hasMany(DealerOrder::class, 'dealer_id');
    }

    public function walletTransactions()
    {
        return $this->hasMany(DealerWalletTransaction::class, 'dealer_id');
    }

    public function collections()
    {
        return $this->morphMany(Collection::class, 'party');
    }

    public function draftOrders()
    {
        return $this->hasMany(DraftOrder::class);
    }

    public function manufactures()
    {
        return $this->hasMany(Manufacture::class);
    }

    public function orderRequests()
    {
        return $this->hasMany(OrderRequest::class);
    }
}
