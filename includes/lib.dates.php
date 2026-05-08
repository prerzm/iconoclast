<?php

/** RZM PHP Framework **/

function get_date_es($format, $date) {
	
	$months_en = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December", 
						"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
					);
	$months_es = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre",
						"Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"
					);
	$days_en = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
	$days_es = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

    $new_date = date($format, strtotime($date));
	$new_date = str_replace($days_en, $days_es, $new_date);
	$new_date = str_replace($months_en, $months_es, $new_date);
	
	return $new_date;
	
}


function get_month_es($month) {
	
	switch((int)$month) {
		case 1: return "Enero"; break;
		case 2: return "Febrero"; break;
		case 3: return "Marzo"; break;
		case 4: return "Abril"; break;
		case 5: return "Mayo"; break;
		case 6: return "Junio"; break;
		case 7: return "Julio"; break;
		case 8: return "Agosto"; break;
		case 9: return "Septiembre"; break;
		case 10: return "Octubre"; break;
		case 11: return "Noviembre"; break;
		case 12: return "Diciembre"; break;
	}
	
}


function get_date_mod($format, $date, $mod) {
	if(strtotime($date)==false) {
        $date = date($format, strtotime(date("Y-m-d")." $mod"));
    }
    return $date;

}

?>