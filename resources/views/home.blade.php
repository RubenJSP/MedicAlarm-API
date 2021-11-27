@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Inicio') }}</div>
                  <div class="card-body">
                    @if (session('verified'))
                    <div class="alert alert-success" role="alert">
                      <strong>
                        {{ __('¡Enhorabuena '.Auth::user()->name.'! has activado tu cuenta MedicAlarm.') }}
                      </strong>
                        
                    </div>
                  @endif
                    @if(Auth::user()->getRoleNames()[0] == 'Medic') 
                        <h1>¡Hola Dr.{{Auth::user()->name}}!</h1>
                        <br>
                        <h5>Su cuenta está verificada.</h5>
                        <h5>Ya puede iniciar sesión en la App MedicAlarm</h5>   
                    @else
                        <h1>¡Hola {{Auth::user()->name}}!</h1>
                        <br>
                        <h5>Tu identificador único es: <strong>{{Auth::user()->code}}</strong></h5>
                        <h5>Úsalo para agendar citas y generar tus recetas con tu médico de confianza</h5>   
                    @endif
                    <h5><strong>Citas agendadas</strong></h5>
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Fecha y Hora</th>
                          @if(Auth::user()->getRoleNames()[0] == 'Medic') 
                            <th scope="col">Paciente</th>
                          @else
                          <th scope="col">Clínica</th>
                            <th scope="col">Médico</th>
                          @endif
                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($appointmets as $appointmet)
                          <tr>
                            <th scope="row">{{$appointmet->id}}</th>
                            <td>{{Carbon\Carbon::parse($appointmet->day)->format('d/m/Y H:i A')}}</td>
                            @if (Auth::user()->getRoleNames()[0] == 'Medic')
                              <td>{{__($appointmet->patient->name.' '. $appointmet->patient->lastname)}}</td>
                            @else     
                              <td>{{$appointmet->medic->hospital}}</td>                           
                              <td>{{__($appointmet->medic->name.' '. $appointmet->medic->lastname)}}</td>
                            @endif
                          </tr>           
                        @empty
                            <h5>No hay citas pendientes</h5>
                        @endforelse
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
