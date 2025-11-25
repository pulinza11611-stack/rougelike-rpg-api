<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Character extends Model
{
    use HasFactory;
    protected $table = 'characters'; 
    protected $primaryKey = 'id'; 
    protected $guarded = [];

    //public $timestamps = false; // ถ้าไม่ต้องการใช้ timestamps ให้ตั้งเป็น false
}