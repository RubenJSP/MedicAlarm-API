<?php

namespace App\Jobs;

use App\Models\Alarm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class NotifyContact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $toleranceInMinutes = 3;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $alarms = $this->getAlarms();
        foreach ($alarms as $alarm) {
            if($this->checkHour($alarm)){
                $at = Carbon::parse($alarm->next_alarm)->format("H:i A");
                echo "Notify to {$alarm->contact->name} Alarm at: {$at}\n";
                $message = "Hola {$alarm->contact->name}, parece que {$alarm->contact->patient->name} ha olvidado la alarma de las {$at} con asunto {$alarm->description}.
                -El equipo de MedicAlarm.";
                //$this->sendSMS($alarm->contact->phone,$message);
                $alarm->next_alarm = Carbon::now()->addMinutes($alarm->frecuency);
                $alarm->save();
            }
        }
    }
    public function getAlarms(){
        $this->clear();
        return Alarm::where('notify',1)->whereDate('end_date','>=',Carbon::now()->format('Y-m-d'))->with('contact')->get();
    }

    public function checkHour($alarm){
        $fromAlarm = Carbon::parse($alarm->next_alarm)->addMinutes($this->toleranceInMinutes);
        return Carbon::now()->greaterThanOrEqualTo($fromAlarm);
    }
    public function sendSMS($to,$msg){
        try {
            $basic  = new \Nexmo\Client\Credentials\Basic(env("NEXMO_KEY",''), env("NEXMO_SECRET",''));
            $client = new \Nexmo\Client($basic);
            $response = $client->message()->send([
                'to' => "+52{$to}",
                'from' => 'MedicAlarm',
                'text' => $msg
            ]);   
            if($response->status == 0)
                echo "Message sent to +52{$to}\n";
        } catch (Exception $e) {
            echo "Error: ".$e->getMessage();
        }
    }
    public function clear(){
        $alarms = Alarm::all();
        foreach ($alarms as $alarm) {
            if(Carbon::now()->gt($alarm->end_date))
                $alarm->delete();
        }
    }
}
