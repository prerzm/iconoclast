<?php

/** RZM PHP Framework **/

# include configuration file
include_once ('../includes/inc.init.php');

# login
login_redirect($logged = login_check($mysqli));
redirect_roles_denied(array("roleId" => ROLE_USER));

# return
$return = "reports.php";

# process
switch(aglobal('cmd', 20)) {

	case 'income':
	
        # vars
        $locationId = session_get_data("locationId");
        $return = "reports.income.php";

        # date filter
        $dateFrom = (isset($_GET['dateFrom'])) ? aget('dateFrom',10) : date("Y-m-01");
        $sql_date_from = " AND b.start >= '$dateFrom'";

        $dateTo = (isset($_GET['dateTo'])) ? aget('dateTo',10) : date("Y-m-t");
        $sql_date_to = " AND b.end <= '".date("Y-m-d", strtotime($dateTo)+(24 * 3600))."'";

        # status filter
        $paymentStatusId = (int)aget('paymentStatusId');
        if($paymentStatusId==0) {
            $sql_status = "";
        } else {
            $sql_status = " AND b.paymentStatusId = $paymentStatusId";
        }

        # queries
        $results = sql_select("SELECT b.bookingId, DATE(b.start) as dateStart, TIME(b.start) AS timeStart, DATE(b.end) AS dateEnd, TIME(b.end) AS timeEnd, b.bookingRate, b.bookingAmount, b.itemsAmount, b.bookingTotal, 
                                        o.code AS officeCode, ps.paymentStatus, c.name AS customerName 
                                FROM ".TABLE_BOOKINGS." b, ".TABLE_PAYMENTS_STATUS." ps, ".TABLE_OFFICES." o, ".TABLE_CUSTOMERS." c 
                                WHERE b.paymentStatusId = ps.paymentStatusId AND b.officeId = o.officeId AND b.customerId = c.customerId AND o.locationId = $locationId 
                                        $sql_date_from $sql_date_to $sql_status AND b.deleted = 0");
        
        # process
        if($results) {

            # file & headers
            $header = array(utf8_decode("ID Reservación"), "Consultorio", utf8_decode("Médico"), "Fecha Inicio", "Hora Inicio", "Fecha Fin", "Hora Fin", "Status", "Tarifa", "Total Renta", "Total Productos", "Total");
            $columns = array("bookingId", "officeCode", "customerName", "dateStart", "timeStart", "dateEnd", "timeEnd", "paymentStatus", "bookingRate", "bookingAmount", "itemsAmount", "bookingTotal");
            $path = "../files/reports/";
            $filename = $path."ingresos_$dateFrom"."_".$dateTo.".csv";
            $fp = fopen($filename, "w");
            fputcsv($fp, $header);

            # count columns
            for($i=0; $i<count($results); $i++) {
                
                $line = array();
                
                foreach($columns as $key) {
                    $line[] = utf8_decode($results[$i][$key]);
                }
                
                # write csv line
                fputcsv($fp, $line);

            }

            # close file
            fclose($fp);
            
        }

        # file download
        if(isset($filename) && file_exists($filename) && is_file($filename)) {
            file_download(base64_encode($filename));
            die();
        } else {
            set_alert("error", "Hubo un problema en la información, por favor intenta nuevamente.");
        }


	break;

	default:

        # set error & error message on session
		set_alert("error", "Hubo un problema en la información, por favor intenta nuevamente.");
	
	break;
	
}

# redirect
redirect($return);

?>