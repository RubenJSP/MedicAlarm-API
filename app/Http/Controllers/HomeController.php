<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Auth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $appointmets = [];
        if(Auth::user()->getRoleNames()[0] == 'Patient')
            $appointmets = Appointment::where('patient_id',Auth::user()->id)->with('medic')->get();
        else
            $appointmets = Appointment::where('medic_id',Auth::user()->id)->with('patient')->get();
        return view('home',compact('appointmets'));
    }
}
