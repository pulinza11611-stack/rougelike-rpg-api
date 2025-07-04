<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Items extends Model
{
    use HasFactory;
    protected $table = 'items'; 
    protected $primaryKey = 'id'; 
    protected $guarded = [];
}
