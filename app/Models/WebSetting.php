<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'slogan',
        'contact_number_1',
        'contact_number_2',
        'address',
        'email',
        'facebook',
        'twitter',
        'instagram',
        'youtube',
        'linkedin',
        'whatsapp',
        'tiktok',
        'pinterest',
        'office_hour',
        'currency_symbol',
        'inside_dhaka',
        'outside_dhaka',
        'subcity',
        'body_part_price',
        'finishing_part_price',
        'dealer_profit_percent',
        'special_dealer_profit_percent',
    ];
}
