<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;
class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = Contact::where('patient_id',Auth::user()->id)->get();
        return response()->json([
            'data' => $contacts
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
            'name' => ['required','min:2','max:100','string'],
            'phone' => ['required','regex:/^\d{10}$/'],
        ]);
        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 400);
        if($contact = Contact::create($request->all() + ['patient_id' => Auth::user()->id]))
            return response()->json([
                'message' => "Has añadido a {$contact->name}",
                'data' => $contact
            ], 200);
        
        return response()->json([
            'message' => 'No se pudo agregar el contacto, intente nuevamente',
        ], 500);
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
            'id' => ['required','exists:contacts,id'],
            'name' => ['required','min:2','max:100','string'],
            'phone' => ['required','regex:/^\d{10}$/'],
        ]);
        if ($validator->fails())
            return response()->json(['data' => array_values(json_decode($validator->errors(),true))], 400);
        $contact = Contact::find($request->id);

        if($contact->patient_id != Auth::user()->id)
            return response()->json([
                'message' => 'Alto ahí, esta acción no está autorizada!',
            ], 403);

        if($contact->update($request->except('id')))
            return response()->json([
                'message' => "Se ha actualizado el contacto.",
                'data' => $contact
            ], 200);

        return response()->json([
            'message' => 'No se pudo actualizar el contacto',
        ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        if($contact->patient_id != Auth::user()->id)
            return response()->json([
                'message' => 'Alto ahí, esta acción no está autorizada!',
            ], 403);

        $name = $contact->name;
        if($contact->delete())
            return response()->json([
                'message' => "Adiós {$name}!.",
            ], 200);     
        return response()->json([
            'message' => 'No se pudo eliminar el contacto solicitado, intente nuevamente',
        ], 500);
    }
}
