<?php

namespace App\Http\Controllers;

use App\Models\merchants;
use Illuminate\Http\Request;

class merchantsController extends Controller
{
    public function getAll(){
        $merchants = merchants::all();
        return response()->json($merchants);
    }

}
