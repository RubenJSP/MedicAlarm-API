<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Key;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return $validator = Validator::make($data, [
            'name' => ['required', 'string', 'min:3','max:255','regex:/[A-Za-z]+/'],
            'lastname' => ['required', 'string', 'min:3','max:255','regex:/[A-Za-z]+/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string','min:8' ,'max:32'],
            'phone' => ['required','min:10','max:10','regex:/^\d{10}$/','unique:users'],
            'key' => ['nullable','exists:keys,key']
        ]);

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $data['name'],
                'lastname' => $data['lastname'],
                'phone' =>  $data['phone'],
                'email' => $data['email'],
                'key' => $data['key'],
                'password' => Hash::make($data['password']),
            ]);
            if($user){
                if($data['key']!=''){
                    $key = Key::where('key',$array['key'])->first();
                    if($key){
                        $user['hospital'] = $key['hospital'];
                        $user->assignRole('Medic');
                    }
                }else{                   
                    $user->assignRole('Patient');
                    $user['code'] = strtoupper(substr($user['name'],0,3).substr($user['lastname'],0,2)."-".substr(hash('sha256',$user['id']),0,5));    
                }
                $user->save();
            }
            $user->sendEmailVerificationNotification();
            Session::put('status', "Se ha enviado un enlace de activación a {$data['email']}");
            DB::commit();
            return $user;
        } catch (\Exception $e) {
           DB::rollback();
        }
       
        return null;
    }
}
