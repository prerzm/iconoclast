<?php

/** RZM PHP Framework **/

# include configuration file
include_once ('../includes/inc.init.php');

# vars
$cmd = aglobal('cmd', 20);
$return	= "contracts.php";
$params	= "";

# process
switch($cmd) {

	case 'update':

		# vars
		$error = false;
        $contractId = (int)apost('id');
        $contract = get_contract($contractId);

        $values['lastUpdated'] = date("Y-m-d H:i:s");
		$values['contrato'] = base64_encode($_POST['contrato']);
        
        # update contract
        $updated = query_update(TABLE_CONTRACTS, $values, "contratoId = $contractId");

		# update
        if($updated>0) {
            system_log($updated, TABLE_CONTRACTS, "Update", json_encode($values));
            set_alert("success", "La información ha sido actualizada.");
        } else {
            set_alert("error", "Hubo un problema, favor de intentar nuevamente");
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