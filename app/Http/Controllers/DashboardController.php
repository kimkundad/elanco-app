<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\course;

class DashboardController extends Controller
{
    //

    public function index(){

        $objs = course::limit(10)->get();

        $data = [
            'objs' => $objs
        ];

        return view('admin2.dashboard.index', $data);
    }

    public function systemlogs(){

        return view('admin.dashboard.systemlogs');
    }

}
