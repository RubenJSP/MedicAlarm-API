<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;
class AlarmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         if(Auth::user()->getRoleNames()[0] == 'Patient'){
             $alarms = Alarm::where('patient_id',Auth::user()->id)->get();
             return response()->json(['data' => $alarms], 200);
         }
         return response()->json(['message' => 'No hay alarmas que mostrar'], 404);
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
            'days' => ['required','numeric','integer','gte:0'],
            'next_alarm' => ['nullable','date','after_or_equal:'.Carbon::now()->format('d-m-Y H:i')],
            'description' => ['required','string','min:2','max:255'],
            'frecuency' => ['required','numeric','gt:0'],
            'contact_id' => ['nullable','integer','exists:contacts,id'],
            'notify' => ['nullable','nullable','boolean']
        ]);
        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 400);

        if($request->has('contact_id'))
            if(!Contact::where('patient_id',Auth::user()->id)->where('id',$request->contact_id)->first())
                return response()->json(['message' => 'Este contacto no se encuentra en su lista'], 404); 

        if(!$request->next_alarm)
            $request->next_alarm = Carbon::parse($request->next_alarm);

        if($alarm = Alarm::create([
            'description'=> $request->description,
            'days'=> $request->days,
            'frecuency'=> $request->frecuency,
            'next_alarm'=> Carbon::parse($request->next_alarm),
            'end_date' => Carbon::parse($request->next_alarm)->addDays($request->days),
            'patient_id'=> Auth::user()->id,
            'contact_id'=> $request->contact_id,
            'notify'=> $request->notify??0,
        ])){
            return response()->json([
                'message' => "La alarma sonará a las {$alarm->next_alarm->format('H:i A')}",
                'data' => $alarm
            ], 200);
        }
    }

       /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required','integer','exists:alarms,id'],
            'days' => ['nullable','numeric','integer','gt:0'],
            'next_alarm' => ['nullable','date','after_or_equal:'.Carbon::now()->format('d-m-Y H:i')],
            'description' => ['nullable','string','min:2','max:255'],
            'frecuency' => ['nullable','numeric','gt:0'],
            'contact_id' => ['nullable','integer','exists:contacts,id'],
            'notify' => ['nullable','boolean']
        ]);
        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 400);
            $alarm = Alarm::find($request->id);   
            $message = 'Se ha actualizado la alarma';
                
            try {
                DB::beginTransaction();
                    if($alarm->update(array_filter($request->except('id')))){
                    if($request->has('days') )
                        $alarm->end_date = Carbon::parse($alarm->end_date)->addDays($request->days);

                    if($request->has('frecuency')){
                        $alarm->next_alarm = Carbon::now()->addMinutes($request->frecuency);
                        $message = "La alarma sonará a las {$alarm->next_alarm->format('H:i A')}";
                    }

                    if(!$alarm->save()){
                        DB::rollback();
                        return response()->json([
                            'message' => "La alarma no pudo ser actualizada, intente nuevamente",
                        ], 500);  
                    }
                    DB::commit();
                    return response()->json([
                        'message' => $message,
                        'data' => $alarm
                    ], 200);
                }
            } catch (Exception $e) {
                DB::rollback();
            } 
        return response()->json([
            'message' => "La alarma no pudo ser actualizada, intente nuevamente",
        ], 500);
    }

    /**
     * Turn off alarm and add hours to next notification
     *
     * @param  \App\Models\Alarm  $alarm
     * @return \Illuminate\Http\Response
     */
    public function turnOff(Alarm $alarm){
        if($alarm->patient_id == Auth::user()->id){
                $alarm->next_alarm = Carbon::parse($alarm->next_alarm)->addMinutes($alarm->frecuency);
                if($alarm->save()){
                    return response()->json([
                        'message' => "La alarma sonará a las {$alarm->next_alarm->format('H:i A')}",
                        'data' => $alarm
                    ], 200,);
                }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Alarm  $alarm
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alarm $alarm)
    {
        if($alarm->patient_id != Auth::user()->id)
            return response()->json(['message' => 'Acción no autorizada'], 403);
        if($alarm->delete())
            return response()->json(['message' => 'Ha eliminado la alarma'], 200);

        return response()->json(['message' => 'No se ha podido eliminar la alarma, intente nuevamente'], 500);
    }
}
