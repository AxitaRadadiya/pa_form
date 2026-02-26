<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelActivity;

class Relation extends Model
{
    use HasFactory, LogsModelActivity;

    protected $fillable = ['name'];

    protected $table = 'relations';
}
