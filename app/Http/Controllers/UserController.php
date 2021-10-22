<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        return Auth::user();
    }
    /**
     * Display the specified resource.
     *
     * @param   id  $patient
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $patient = User::where('code',$code)->firts();
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
            'name' => ['required', 'string', 'min:3','max:255','regex:/[A-Za-z]+/'],
            'lastname' => ['required', 'string', 'min:3','max:255','regex:/[A-Za-z]+/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string','min:8' ,'max:32'],
            'phone' => ['required','regex:/^\d{10}$/','unique:users'],
            'key' => ['nullable','exists:keys,key']
        ]);

        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 401);
        $user = [];
        DB::beginTransaction();
        try {
            if($user = User::create($request->all())){
                $key = Key::where('key',$request['key'])->first();
                if($key){
                    $user['hospital'] = $key['hospital'];
                    $user->assignRole('Medic');
                }
                else{
                    $user->assignRole('Patient');
                    $user['code'] = strtoupper(substr($user['name'],0,3).substr($user['lastname'],0,2)."-".substr(hash('sha256',$user['id']),0,5));
                }
    
                $user->password =  Hash::make($request['password']);
                $user->save();
                $user->sendEmailVerificationNotification();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            //return response()->json(['data' => "No se pudo crear la cuenta, intente nuevamente"], 500);
            return $e;
        }
        return response()->json([
            'message' => "Te damos la bienvenida a MedicAlarm, en breve se le enviará un correo de activación.
            Si no ha recibido ningún correo verifique su bandeja de SPAM",
            'data' => $user
        ], 200);
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
            'name' => ['sometimes', 'string', 'min:3','max:50', 'regex:/[A-Za-z]+/'],
            'lastname' => ['sometimes', 'string', 'min:3','max:50','regex:/[A-Za-z]+/'],
            'email' => ['sometimes', 'string', 'email', 'max:32', 'unique:users'],
            'password' => ['sometimes','required','string','min:8' ,'max:32'],
            'phone' => ['sometimes','required','regex:/^\d{10}$/','unique:users'],
            'speciality' => ['sometimes', 'string','min:4','max:50','regex:/[A-Za-z]+/'],
            'professional_id' => ['sometimes','string','min:3','max:20','unique:users'],
        ]);
        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 401);

        if($request->has('password'))
            $request['password'] = Hash::make($request['password']); 

        if(Auth::user()->getRoleNames()[0] == 'Patient')
            $request->only(['name', 'lastname', 'email','password','phone']);

        if(User::find(Auth::user()->id)->update($request->all())){
            $updated = User::find(Auth::user()->id);
            return response()->json([
                'message' => 'Ha actualizado su información.',
                'data' => $updated
            ], 200);

        }

        return response()->json(['message' => 'No se ha actualizado la información, intente nuevamente.'], 401);
        
    }

    public function recoveryAccount(){
        
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
        
        return response()->json(['message' => 'No se ha podido eliminar la cuenta, intente nuevamente'], 401);
        
    }
}
