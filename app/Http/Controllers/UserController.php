<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Key;
use Auth;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string','min:8' ,'max:32'],
            'phone' => ['required','regex:/^\d{10}$/','unique:users'],
            'key' => ['nullable','exists:keys,key']
        ]);

        if ($validator->fails())
            return response()->json(['data' => $validator->errors()], 401);

        if($user = User::create($request->all())){
            $key = Key::where('key',$request['key'])->first();
            if($key){
                $user['hospital'] = $key['hospital'];
                $user->assignRole('Medic');
            }
            else{
                $user->assignRole('Patient');
                $user['code'] = strtoupper(substr(hash('sha256',$user['id']),0,5));
            }

            $user->password =  Hash::make($request['password']);
            $user->save();
            return response()->json([
                'message' => "Usuario creado correctamente",
                'data' => $user
            ], 200);
        }
        return response()->json(['data' => "No se pudo crear la cuenta, intente nuevamente"], 500);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:50'],
            'lastname' => ['sometimes', 'string', 'max:50'],
            'email' => ['sometimes', 'string', 'email', 'max:32', 'unique:users'],
            'password' => ['sometimes','required','string','min:8' ,'max:32'],
            'phone' => ['sometimes','required','regex:/^\d{10}$/','unique:users'],
            'speciality' => ['sometimes', 'string','min:4','max:50'],
            'professional_id' => ['sometimes','string','min:3','max:20','unique:users'],
        ]);
        if($request->has('password'))
            $request['password'] = Hash::make($request['password']); 

        if(Auth::user()->getRoleNames()[0] == 'Patient')
            $request->only(['name', 'lastname', 'email','password','phone']);

        if ($validator->fails())
            return response()->json(['data' => $validator->errors()], 401);

        if(User::find(Auth::user()->id)->update($request->all()))
            return response()->json(['message' => 'Ha actualizado su información.'], 200);

        return response()->json(['message' => 'No se ha actualizado la información, intente nuevamente.'], 401);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = User::find(Auth::user()->id);
        if($user->delete())
            return response()->json(['message' => 'Ha eliminado su cuenta'], 200);
        
        return response()->json(['message' => 'No se ha podido eliminar la cuenta, intente nuevamente'], 200);
        
    }
}
