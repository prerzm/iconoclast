<?php

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "currencies.php";

# process
switch(aglobal('cmd', 20)) {

	case 'update':
	
		# vars
        $error = false;
        $currencyCode = 'USD';
        $values['exchangeRate'] = (float)apost('exchangeRate');

		# query
		if($error==false) {
			
			$updated = query_update(TABLE_CURRENCIES, $values, "currencyCode = '$currencyCode'");
			
            if($updated>0) {
                system_log($currencyCode, TABLE_CURRENCIES, "Update", json_encode($values));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
    
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