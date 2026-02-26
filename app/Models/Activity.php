<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    // Use the activity_log table (Spatie / tenant-aware table)
    protected $table = 'activity_log';

    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'tenant_id',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function causer()
    {
        return $this->morphTo(null, 'causer_type', 'causer_id');
    }

    public function subject()
    {
        return $this->morphTo(null, 'subject_type', 'subject_id');
    }
}
