<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;
class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patients = Patient::where('medic_id',Auth::user()->id)->with('user','prescriptions.medicament')->get();
        return $patients;

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
            'alias' => ['nullable','min:2','max:50'],
        ]);
        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 400);
        if(trim($request->alias) == '')
            $request->alias = User::where('code',$request['patient'])->pluck('name')[0];
        if($patient = Patient::create([
            'alias' => $request->alias,
            'patient_id' => User::where('code',$request['patient'])->pluck('id')[0],
            'medic_id' => Auth::user()->id]))
            return response()->json([
                'message' => 'Se ha añadido un nuevo paciente',
                'data' => $patient
            ], 200);
            
        return response()->json([
            'message' => 'No se pudo añadir al paciente, intente nuevamente'
        ], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param   $patient
     * @return \Illuminate\Http\Response
     */
    public function show($patient)
    {
        $patient = Patient::where('id',$patient)->with('user','prescriptions.medicament')->first();
        return response()->json(['data' => $patient], 200);   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required','string','exists:patients,id'],
            'alias' => ['required','min:2','max:50'],
        ]);
        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 400);
        $patient = Patient::find($request->id);
        if($patient->update(['alias' => $request->alias]))  
            return response()->json([
                'message' => 'Se ha actualizado el paciente',
                'data' => $patient
            ], 200);   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient)
    {
        try {
            DB::beginTransaction();
            if($patient->medic_id != Auth::user()->id)
                return response()->json(['message' => 'Acción no autorizada'], 403);
            $prescriptions = Prescription::where('patient_id',$patient->user()->first()->id)->where('medic_id',Auth::user()->id)->get();
            foreach ($prescriptions as $prescription) {
                $prescription->delete();
            }
            if($patient->delete()){
                DB::commit();
                return response()->json(['message' => "Se ha eliminado a {$patient->alias} de su lista de pacientes"], 200);
            }
       
        } catch (Exception $e) {
            DB::rollback();
        }
        return response()->json(['message' => 'No se ha podido eliminar el paciente, intente nuevamente'], 500);


    }
}
