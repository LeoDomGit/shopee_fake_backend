<?php

namespace App\Http\Controllers;

use App\Mail\createUser;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_seller(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
        ]);
        if ($validator->fails()) {
            return response()->json(['check' => false, 'msg' => $validator->errors()->first()]);
        }
        $data = $request->all();
        $password = random_int(10000, 99999);
        $data['password'] = Hash::make($password);
        $role = Roles::where('name', 'like', '%Sellers%')->first();

        if(!$role){
            return response()->json(['check'=>false,'msg'=>'Role not found']);
        }
        $data['role_id'] = $role ?$role->id : null;
        User::create($data);
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
        ];
        Mail::to($request->email)->send(new createUser($data));
        return response()->json(['check'=>true]);
    }

    /**
     * Display the specified resource.
     */
    public function login_sellers(Request $request,User $user)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['check' => false, 'msg' => $validator->errors()->first()]);
        }
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password,'status'=>1],true)){
            $user=User::where('email',$request->email)->first();
            $token = $user->createToken('customer_token')->plainTextToken;
            $user->update([
                'remember_token' => $token,
            ]);
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
            ]);
        }
        return response()->json(['check'=>false,'msg'=>'Sai email hoặc mật khẩu']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
