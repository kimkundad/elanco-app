<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speaker extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',      // เชื่อมโยงกับคอร์ส
        'name',           // ชื่อผู้พูด
        'avatar',         // รูปภาพ
        'job_position',   // ตำแหน่งงาน
        'country',        // ประเทศ
        'file',           // ไฟล์
        'description',    // คำอธิบายเพิ่มเติม
    ];

    public function course()
    {
        return $this->belongsTo(course::class, 'course_id');
    }

    public function countryDetails()
    {
        return $this->belongsTo(Country::class, 'country', 'id'); // 'country' คือ foreign key
    }
}
