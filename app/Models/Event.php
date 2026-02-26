<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelActivity;

class Event extends Model
{
    use HasFactory, LogsModelActivity;

    protected $fillable = [
        'name',
        'image',
        'description',
        'qr_code',
    ];

    protected $table = 'events';
}
