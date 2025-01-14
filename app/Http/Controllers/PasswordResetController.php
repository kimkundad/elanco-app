<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PasswordResetController extends Controller
{

    public function __construct()
    {
        $this->middleware('api'); // บังคับให้ใช้ API middleware
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $token = Str::random(60);

        // บันทึก token ใน database
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        // ส่งอีเมล
        Mail::to($request->email)->send(new ResetPasswordMail($token));

        return response()->json(['message' => 'อีเมลรีเซ็ตรหัสผ่านถูกส่งไปแล้ว']);
    }

    public function PostresetPasswordx(Request $request)
    {


        dd($request->all());

    }


    public function PostresetPasswords(Request $request)
    {

      //  dd($request->all());

      $validator = \Validator::make($request->all(), [
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    //    dd($request->all());

        // ตรวจสอบ token
        $record = DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->token,
        ])->first();

        if (!$record || Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['message' => 'Token ไม่ถูกต้องหรือหมดอายุ'], 400);
        }

        // อัปเดตรหัสผ่าน
        $user = \App\Models\User::where('email', $request->email)->first();
        $user->update(['password' => bcrypt($request->password)]);

        // ลบ token
        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return response()->json(['message' => 'รีเซ็ตรหัสผ่านสำเร็จ']);
    }
}
