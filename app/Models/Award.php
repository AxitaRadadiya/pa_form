<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Award extends Model
{
    protected $fillable = [
        'registration_id',
        'section3_description',
        'surname',             
        'first_name',          
        'gender',
        'department',           
        'award_category',       
        'award_type',           
        'photo_attached',
        'food_id',
        'amount',               
        'special_comment',      
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];


    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }
}