<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Events\PrescriptionEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Auth;
class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $appointment = [];
        if(Auth::user()->getRoleNames()[0] == 'Patient')
            $appointment = Appointment::where('patient_id',Auth::user()->id)->with('medic')->get();
        else
            $appointment = Appointment::where('medic_id',Auth::user()->id)->with('patient')->get();
        //$appointment = Appointment::where();
        return response()->json(['data' => $appointment], 200);
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
            'patient' => ['required','string','exists:users,code'],
            'day' => ['required','date','after_or_equal:'.Carbon::now()],
            //'clinic' => ['required', 'string']
        ]);
        //return Carbon::parse($request->day)->format('H:i A');
        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 400);
        //Validar que no se agenden citas a la misma hora y día
        if($this->compareDates($request->day))
            return response()->json([ 'message' => "Ya existe una cita agendada a esta fecha y hora"], 400);
        $appointment = Appointment::where('medic_id',Auth::user()->id);
        $request->patient = User::where('code',$request->patient)->pluck('id')[0];
        if($appointment = Appointment::create([
            'patient_id' => $request->patient,
            'medic_id' => Auth::user()->id,
            'day' => $request->day])){
                //event(new PrescriptionEvent());
                return response()->json(['data' => $appointment->with('medic')->get], 200);
            }

            return response()->json([
                'message' => "Algio saió mal, intente nuevamente.",
            ], 500);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' =>  ['required','numeric','exists:appointments,id'],
            'patient' => ['sometimes','required','string','exists:users,code'],
            'day' => ['sometimes','required','date','after_or_equal:'.Carbon::now()],
            //'clinic' => ['required', 'string']
        ]);
        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 400);
                //Validar que no se agenden citas a la misma hora y día
                if($this->compareDates($request->day))
                return response()->json([ 'message' => "Ya existe una cita agendada a esta fecha y hora"], 400);
        if($request->has('patient')){
            $request->merge(['patient_id' => User::where('code',$request->patient)->pluck('id')[0]]);
        }

        $appointment = Appointment::find($request->id);
        if($appointment->update($request->except('id')))
            return response()->json(['data' => $appointment->with('patient')->get()], 200);
        

        return response()->json([
            'message' => "Algio saió mal, intente nuevamente.",
        ], 500);

    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        if(!$appointment->patient_id == Auth::user()->id || !$appointment->medic_id == Auth::user()->id)
            return response()->json(['message' => 'Acción no autorizada'], 403);

        if($appointment->delete())
            return response()->json(['message' => 'Se ha eliminado la cita'], 200);    

        return response()->json(['message' => 'No se ha podido eliminar la receta, intente nuevamente'], 500);
        
    }

    public function compareDates($date)
    {
        $appointments = Appointment::select('day')->where('medic_id',Auth::user()->id)->get();
        foreach ($appointments as $appointment) {
            $firstDate =  Carbon::parse($appointment['day'])->format('d-m-y H:i');
            $secondDate =  Carbon::parse($date)->format('d-m-y H:i');
            if($firstDate == $secondDate)
                return true;
        }
        return false;
    }
}
