<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Award extends Model
{
    protected $fillable = [
        'registration_id',
        'section3_description',
        'award_name',
        'first_name',
        'last_name',
        'other_award_name',
        'award_type',
        'photo_attached',
        'food_id',
        'relation_id',
        'amount_section3',
        'amount',
    ];

    protected $casts = [
        'amount_section3' => 'decimal:2',
    ];

    // ---------- Relationships ----------

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    public function relation(): BelongsTo
    {
        return $this->belongsTo(Relation::class);
    }
}