<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\User;
use App\Events\PrescriptionEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Auth;
class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prescriptions = [];
        if(Auth::user()->getRoleNames()[0] == 'Patient')
            $prescriptions = Prescription::where('patient_id',Auth::user()->id)
                ->with('medic','medicament')->get();
        else
            $prescriptions = Prescription::where('medic_id',Auth::user()->id)
            ->with('patient','medicament')->get();
        if(count($prescriptions) != 0)
            return response()->json([
                'data' => $prescriptions
            ], 200);

        return response()->json([
                'message' => "No se encontraron recetas"
            ], 200);
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
            'description' => ['sometimes','required','min:4','max:1000'],
            'medicament' => ['required','numeric','exists:medicaments,id'],
            'patient' => ['required','string','exists:users,code'],
            'interval' => ['required','numeric'],
            'duration' =>  ['required','date',"after_or_equal:".Carbon::now()->format('d-m-Y')],
        ]);

        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 400);

        if($prescription = Prescription::create([
            'description' => $request['description'],
            'medicament_id' => $request['medicament'],
            'patient_id' => User::where('code',$request['patient'])->pluck('id')[0],
            'medic_id' => Auth::user()->id,
            'interval' => $request['interval'],
            'duration' => Carbon::parse($request['duration']),
        ])){
            $relationships = Prescription::where('id',$prescription['id'])->with('patient','medic','medicament')->get();
            //Notificar al paciente de su receta
            event(new PrescriptionEvent($relationships[0]->patient_id,$relationships->toJson(),'Se ha añadido una nueva receta'));
            return response()->json([
                'message' => "Se ha creado la receta.",
                'data' => $relationships
            ], 200);
        }

        return response()->json([
            'message' => "Algio saió mal, intente nuevamente.",
        ], 500);

    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Prescription  $prescription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' =>  ['required','numeric','exists:prescriptions,id'],
            'description' => ['sometimes','required','min:4','max:1000'],
            'medicament_id' => ['sometimes','required','numeric','exists:medicaments,id'],
            'patient' => ['sometimes','required','exists:users,code'],
            'interval' => ['sometimes','required','numeric'],
            'duration' =>  ['required','date',"after_or_equal:".Carbon::now()->format('d-m-Y')],
        ]);
        
        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 400);
   
        $prescription = Prescription::find($request['id']);

        if($prescription['medic_id'] != Auth::user()->id)
            return response()->json([
                'message' => 'Acción no autorizada',
            ], 403);

        if($prescription->update($request->except('duration'))){
            if($request->has('duration')){
                $prescription['duration'] = Carbon::parse($request['duration']);
                $prescription->save();
            }
            if($request->has('patient')){
                $prescription['patient_id'] = User::where('code',$request['patient'])->pluck('id')[0];
                $prescription->save();
            }
            $relationships = Prescription::where('id',$request['id'])->with('patient','medic','medicament')->get();
            return response()->json([
                'message' => "Se ha actualizado la receta.",
                'data' => $relationships
            ], 200);
        }
        return response()->json([
            'message' => "Ha ocurrido un error, no se ha actualizado la receta.",
        ], 500);
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Prescription  $prescription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Prescription $prescription)
    {
        if($prescription['medic_id'] != Auth::user()->id)
            return response()->json(['message' => 'Acción no autorizada'], 403);
        
        if($prescription->delete())
            return response()->json(['message' => 'Ha eliminado la receta'], 200);


        return response()->json(['message' => 'No se ha podido eliminar la receta, intente nuevamente'], 500);
    }
}
