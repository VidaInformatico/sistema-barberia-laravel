<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_url',
        'subtitle',
        'title',
        'text',
        'btn1_text',
        'btn1_url',
        'btn1_icon',
        'btn2_text',
        'btn2_url',
        'btn2_icon',
        'order',
        'is_active',
    ];
}