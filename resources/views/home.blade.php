@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
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

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
