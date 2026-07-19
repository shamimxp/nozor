<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'phone', 'email', 'address', 'profile_image',
        'nid', 'description', 'type', 'password', 'status'
    ];
}
