<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Events\AppointmentReminderEvent;
use App\Events\test;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
class AppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $toleranceInMinutes = 30;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            event(new test());
            $appointments = $this->getAppointments();
            foreach ($appointments as $appointment) 
               if($this->checkHour($appointment->day))
                $this->notify($appointment);
            
        } catch (\Exception $e) {
            echo $e;
        }
       
    }

    private function getAppointments(){
        $appointments = Appointment::whereDate('day',Carbon::now()->format('Y-m-d'))->with('medic','patient')->get();
        return $appointments;
    }
    private function checkHour($date){
        $hour = Carbon::parse($date)->subMinutes($this->toleranceInMinutes)->format('H:i');
        $now = Carbon::now()->format('H:i');
        echo "date: {$hour} now: {$now} \n";
        return $hour === $now;
    }
    private function notify($appointment){
        $hour = Carbon::parse($appointment->day)->format('H:i A');
        $messageToPatient = "Cita hoy a las {$hour} con el Dr.{$appointment->medic->name}";
        $messageToMedic = "Cita hoy a las {$hour} con el paciente {$appointment->patient->name}";
        event(new AppointmentReminderEvent($messageToPatient,$appointment->patient_id));
        event(new AppointmentReminderEvent($messageToMedic,$appointment->medic_id));
    }
    
}
