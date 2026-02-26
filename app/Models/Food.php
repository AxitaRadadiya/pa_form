<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelActivity;

class Food extends Model
{
    use HasFactory, LogsModelActivity;
    
    protected $fillable = [
        'name',
    ];
    
    protected $table = 'foods';
}
