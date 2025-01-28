<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class itemDes extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', // ฟิลด์นี้เชื่อมโยงกับคอร์ส
        'detail',    // เพิ่มฟิลด์ detail ที่เกิดข้อผิดพลาด
    ];
}
