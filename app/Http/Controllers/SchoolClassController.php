<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index(){
        $classes = SchoolClass::all()->groupBy('level');
        return response()->json(['classes' => $classes, 'status' => 200]);
    }
}
