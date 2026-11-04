<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory, TracksSoftDeletion;
    
    protected $fillable = ['first_name', 'email', 'phone', 'subject', 'message'];
}
