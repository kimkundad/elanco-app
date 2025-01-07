<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      //  $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function generateCertificate()
    {
        // ข้อมูลที่ต้องการแสดงบน PDF
        $data = [
            'recipientName' => 'John Doe',
            'programTitle' => 'EP.3 Program',
            'codeNumber' => 'CE123456',
            'points' => '10',
        ];

        // เรียกดู template สำหรับ PDF
        $pdf = Pdf::loadView('certificate-template', $data)
        ->setPaper('a4', 'landscape'); // ตั้งค่า A4 แนวนอน

        // ดาวน์โหลดไฟล์ PDF
        return $pdf->stream('certificate.pdf');
    }
}
