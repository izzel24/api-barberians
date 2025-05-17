<?php

namespace App\Http\Controllers;

use App\Models\merchants;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function fetch(){
        $users = User::all();
        return response()->json($users);
    }

    public function fetchUser(){
        $user = auth()->user();

        return response()->json([
            'user' => $user
        ]);
    }

    public function destroy($id){
        $merchant = User::find($id);

        if(!$merchant){
            return response()->json(['message' => 'User not found'], 404);
        }

        $merchant->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

   
    public function update(Request $request, $id){
        
        $data= User::find($id);
        

        if (!$data) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $data->update($request->all());

        return response()->json(['message' => 'Data updated successfully', 'data' => $data]);
    }

    // public function create()

}
