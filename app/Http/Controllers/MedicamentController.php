<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Jsonable;
use Carbon\Carbon;
use Auth;
class MedicamentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $medicaments = Medicament::paginate();
        return response()->json(['data' => $medicaments], 200);
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
            'name' => ['required','string','max:1000'],
            'via' => ['required','string'],
        ]);
        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 400);
        
        if($medicament = Medicament::create($request->all()))
            return response()->json([
                'message' => "Se ha añadido {$medicament->name} a la lista de medicamentos",
                'data' => $medicament], 200);

        return response()->json([
            'message' => "Ha ocurrido un error, intente nuevamente.",
        ], 500);        
    }

    /**
     * Display the specified resource.
     *
     * @param   $query
     * @return \Illuminate\Http\Response
     */
    public function show($query)
    {
        $medicaments =  Medicament::where('name', 'like', '%' . $query . '%')->get();
        return response()->json(['data' => $medicaments], 200);
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
            'id' => ['required','numeric','exists:medicaments,id'],
            'name' => ['sometimes','required','string','max:1000'],
            'via' => ['sometimes','required','string'],
        ]);
        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 400);
        $medicament = Medicament::find($request->id);
        if($medicament->update($request->except('id')))
            return response()->json([
                'message' => "Ha actualizado el medicamento",
                'data' => $medicament], 200);

        return response()->json([
            'message' => "Ha ocurrido un error, intente nuevamente.",
        ], 500); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Medicament  $medicament
     * @return \Illuminate\Http\Response
     */
    public function destroy(Medicament $medicament)
    {
        if($medicament->delete())
            return response()->json(['message' => "Se ha eliminado {$medicament->name}"], 200);
    }
}
