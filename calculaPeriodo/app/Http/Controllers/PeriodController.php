<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response;
use DateTime;

class PeriodController extends Controller
{
    public function calculateHours(Request $request){

        $result = 0;
        $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date',
        ]);
    
        $startDateTime = new DateTime($request->input('start_datetime'));
        $endDateTime = new DateTime($request->input('end_datetime'));

        if($startDateTime >= $endDateTime) {
            $message = "ATENÇÃO: A data final deve vir depois da data inicial! Tente uma nova busca.";
            return view("buscaValor")->with('message', $message);
        }


        //verify if the period is equal or minor the specified maximun value
        if(!$this->verifyMaxPeriod($startDateTime, $endDateTime)){
            $message = "Período superior a 24h, tente uma nova busca!";
            return view("buscaValor")->with('message', $message);
        }


        //Check if period is of the day, the night or both        h
        $result = $this->calculateDayAndNight($startDateTime, $endDateTime);
        
        return view("buscaValor")->with('result', $result);
    }

    public function verifyMaxPeriod(DateTime $start, DateTime $end){

        //verify if the period is equal or minor the specified maximun value
        
        $timeDifference = $end->diff($start);
        $days = $timeDifference->days;


        if($days > 0){
            return false;
        }

        return true;
    }

    public function isDayTime(DateTime $dateTime){
        
        $startDayTime = new DateTime('05:00:00');
        $endDayTime = new DateTime('22:00:00');

        $time = $dateTime->format('H:i:s');

        if ($time >= $startDayTime->format('H:i:s') && $time < $endDayTime->format('H:i:s')){
            return true;
        }

        return false;
    }

    public function calculateDayAndNight(DateTime $startDateTime, DateTime $endDateTime){
        $startDayTime = new DateTime($endDateTime->format('Y-m-d') . ' 05:00:00');
        $startNightTime = new DateTime($startDateTime->format('Y-m-d') . ' 22:00:00');

        $startIsDayTime = $this->isDayTime($startDateTime);
        $endIsDayTime = $this->isDayTime($endDateTime);

        if($startIsDayTime && $endIsDayTime){
            if($startDateTime->diff($endDateTime)->h < 17){
                $dayHours = $startDateTime->diff($endDateTime);
                $dayHours = $dayHours->h;
                $nightHours = 0;
            }
            else {
                $dayHours = $startDateTime->diff($startNightTime)->h + $startDayTime->diff($endDateTime)->h;
                $nightHours = 7;
            }
        } else if($startIsDayTime && !$endIsDayTime) {
            $dayHours = $startDateTime->diff($startNightTime)->h;
            $nightHours = $startNightTime->diff($endDateTime)->h;
        } else if(!$startIsDayTime && !$endIsDayTime) {
            if($endDateTime->diff($startDateTime)->h > 7) {
                $dayHours = 17;
                $nightHours = $startDayTime->diff($startDateTime)->h + $endDateTime->diff($startNightTime)->h;
                
            }
            else {
                $dayHours = 0;
                $nightHours = $endDateTime->diff($startDateTime)->h;
            }
        } else if (!$startIsDayTime && $endIsDayTime){
            $dayHours = $endDateTime->diff($startDayTime)->h;
            $nightHours = $startDayTime->diff($startDateTime)->h;
        }


        return [
            'dayHours' => $dayHours . "h\n",
            'nightHours' => $nightHours . "h\n",
        ];

        if ($startHour >= $startDayTime->format('H:i:s')){
            $dayHours = $startDateTime->diff($endDateTime);
            $dayHours = $dayHours->h;
            $nightHours = 0;
            return [
                'dayHours' => $dayHours,
                'nightHours' => $nightHours,
            ];
            
        }

        if ($startHour >= $startNightTime->format('H:i:s') && $endHour <= $endNightTime->format('H:i:s')){
            var_dump("SEGUNDO IF");
            $dayHours = 0;
            $nightHours = $startDateTime->diff($endDateTime);
            $nightHours = $nightHours->h;
            return [
                'dayHours' => $dayHours,
                'nightHours' => $nightHours,
            ];
            
        }

        if($startHour >= $startDayTime->format('H:i:s') && $startHour < $startNightTime->format('H:i:s')){
            var_dump("terceiro IF");
            $dayHours = $startHour->diff($startNightTime);
            $nightHours = $startNightTime->diff($endHour);
            return [
                'dayHours' => $dayHours,
                'nightHours' => $nightHours,
            ];
            
        }
        var_dump("quarto IF");
        $dayHours = $startDayTime->diff($endHour);
        $nightHours = $startHour->diff($endNightTime);
        return [
            'dayHours' => $dayHours,
            'nightHours' => $nightHours,
        ];
        

    }
}
