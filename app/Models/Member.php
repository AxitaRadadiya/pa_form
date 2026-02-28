<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    protected $fillable = [
        'registration_id',
        'surname',          // ← ADDED (blade sends member_surname[])
        'name',
        'mobile',
        'dob',
        'age',
        'amount',
        'relation_id',
        'food_id',
        'section_description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'age'    => 'integer',
    ];

    // ---------- Relationships ----------

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function relation(): BelongsTo
    {
        return $this->belongsTo(Relation::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }
}