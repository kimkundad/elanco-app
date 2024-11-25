<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //

    public function index(){

        return view('admin.dashboard.index');
    }

    public function systemlogs(){

        return view('admin.dashboard.systemlogs');
    }

}
