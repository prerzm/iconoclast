<?php

class DateES {

    public static function format($format, $date="") {

        $months_en = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December", 
                            "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
        $months_es = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre",
                            "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
        $days_en = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
        $days_es = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

        $date_time = ($date=="") ? time() : strtotime($date);
        $new_date = date($format, $date_time);
        $new_date = str_replace($days_en, $days_es, $new_date);
        $new_date = str_replace($months_en, $months_es, $new_date);

        return $new_date;
        
    }

}