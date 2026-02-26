<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'chapter_id',
        'chain_name',
        'company_name',
        'about_company',
        'company_logo',
        
        'qr_code',
        'screenshot_payment',
        'grand_total',
        'transaction_id',
    ];

    protected $casts = [
        'grand_total' => 'decimal:2',
    ];

    // ---------- Relationships ----------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function award(): HasOne
    {
        return $this->hasOne(Award::class);
    }
}