<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Country;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $objs = DB::table('users')->select(
            'users.*',
            'users.id as id_q',
            'users.name as names',
            'users.status as status1',
            'users.updated_at as updated_ats',
            'role_user.*',
            'roles.*',
            'roles.name as name1',
        )
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->orderby('role_user.id', 'asc')
            ->paginate(15);

        $objs->setPath('');

        return view('admin.admin.index', compact('objs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $Role = Role::all();
        $data['Role'] = $Role;
        $countries = Country::all();
        $data['countries'] = $countries;
        $data['method'] = "post";
        $data['url'] = url('admin/adminUser');
        return view('admin.admin.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate ข้อมูลที่ส่งมา
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|same:retype_password',
            'retype_password' => 'required|string|min:8',
            'role' => 'required|integer|exists:roles,id',
            'country' => 'required|integer|exists:countries,id',
            'avatar_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4048', // ไม่บังคับอัปโหลดรูป
        ]);

        DB::beginTransaction();

        try {
            // จัดการรูปภาพ (ถ้ามี)
            $filename = null;
            if ($request->hasFile('avatar_img')) {
                $image = $request->file('avatar_img');
                $img = Image::make($image->getRealPath());
                $img->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->stream(); // Prepare image for upload

                // Generate a unique filename
                $filename = time() . '_' . $image->getClientOriginalName();

                // Upload the image to DigitalOcean Spaces
                Storage::disk('do_spaces')->put(
                    'elanco/avatar/' . $filename,
                    $img->__toString(),
                    'public'
                );
            }

            // สร้าง User ใหม่
            $user = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
                //  'phone' => $request->phone ?? null,
                'avatar' => $filename
                    ? 'https://kimspace2.sgp1.cdn.digitaloceanspaces.com/elanco/avatar/' . $filename
                    : null,
                'provider' => 'email',
                'email_verified_at' => now(),
                'country' => $request->country,
                'is_admin' => 0, // ค่าเริ่มต้นสำหรับผู้ใช้ทั่วไป
                'password' => Hash::make($request->password),
            ]);

            // เชื่อมโยง Role กับ User
            $role = Role::find($request->role);
            $user->roles()->attach($role);

            DB::commit();

            return redirect(url('admin/adminUser'))->with('add_success', 'เพิ่มผู้ใช้งานสำเร็จ!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['database' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
