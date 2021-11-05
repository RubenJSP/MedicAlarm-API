@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Inicio') }}</div>
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
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    window.user =  {!! json_encode(Auth::user()->id) !!};
  // Enable pusher logging - don't include this in production
  Pusher.logToConsole = true;

  var pusher = new Pusher('ddd66dfedf577300368b', {
    cluster: 'us2',
    authEndpoint: '/broadcasting/auth',
    auth: {
      headers: { "Accept": 'application/json',
                "Authorization": 'Bearer ' + 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMzA4ZjE0YzE0ODFlODg3YWEzOTEwZGRmYzZlMTYyYTVkNDRhNjE3NTU5ZGUxYTY0ZDA4NGUzMDBjYmM3MzU0NDNkOTkxNjgyMDc3MDU5ODgiLCJpYXQiOjE2MzU4NzQ5NTUuMjAxMzkxLCJuYmYiOjE2MzU4NzQ5NTUuMjAxMzk3LCJleHAiOjE2Njc0MTA5NTUuMDQyMjYzLCJzdWIiOiIzIiwic2NvcGVzIjpbXX0.PHcUEiGnXXmS8Fye68nPiFV_Tt5RPr0QPKTls-Uvsw2ecOYtEZkjCsfXUeyMqFwla7m488iSliMSGfW2e-kjnAmiwIVRr_YnOKO0yKF8AloWhag10sHgV6WBjGiUNRzx35Rra5yL0SOFpa8OvVaqbIZCY6edXyoOIiaDVQCnW0zd0B2b9B9ljqJ5sirgnwTlM22BxzJXEzcF5L0orlpjHY3eID2906_KwEGSEDil9SKBvUl9kSGxwMW8hQ0idOq3V1j2FYRI5duRoDNHVbsUbjNJcA9xJu8odjkkQKfNGqj1xK8yhzC-UEvESx-_WJzy1J6yHCrBR4n6m892evE25h8eh6e6Xih9cpXoRnpURSY0VVtxzlNXvOxiZLw-N7Vn1Xsumq5B-H35qxb-caZWWt0T3jNtwXHR0-TIH35qQGslUN1mESac6QYtEyA6hHcw88FR3y5wePuZNCKgsfDFuG2tpkjsLOhAHeyj91DpJwnUUGBLhCN1axZTvFJ8sE2sPigA2pzMY3Z4zTz2gX1CjLGZJ5GlWsQ5mE4xr0NekE-JagLXkjETRINVsYvpwimnuwLo9koqXTTG9SiLfH38Ooq4PR2LKcraJZ0PCBcdB1FjwdRhwFN5T2QjMMaA_K_LbskAjat0J7egfJ1am61boUUYirNFrFX_BOhJBM02GyQ'
       },
    },
  });

  var channel = pusher.subscribe('private-Prescription.'+window.user);
  channel.bind('prescription', function(data) {
    alert(JSON.stringify(data));
  });
  
  var channel1 = pusher.subscribe('private-Patient.'+window.user);
  channel1.bind('patient', function(data) {
    alert(data.message);
  });

  var channel2 = pusher.subscribe('private-Appointment.'+window.user);
  channel2.bind('appointment', function(data) {
    alert(data.message);
  });
</script>
@endsection
