<?php

/** RZM PHP Framework **/

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "customers.php";

# process
switch(aglobal('cmd', 20)) {

	case 'add':
	
        if($global_perms['ADD']) {

            # vars
            $error = false;
            $customer['razonSocial'] = apost('razonSocial', 150);
            $customer['rfc'] = apost('rfc', 14);
            $customer['telefono'] = apost('telefono', 25);
            $customer['email'] = apost('email', 200);
            $customer['revision'] = apost('revision', 100);
            $customer['pago'] = apost('pago', 100);
            $customer['condiciones'] = apost('condiciones', 100);
            $customer['notas'] = apost('notas', 200);

            # query
            $updated = query_insert(TABLE_CUSTOMERS, $customer);
                
            if($updated>0) {
                system_log($updated, TABLE_CUSTOMERS, "Add", json_encode($customer));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }
    
	break;

	case 'update':
	
        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $customerId = (int)apost('id');
            $customer['razonSocial'] = apost('razonSocial', 150);
            $customer['rfc'] = apost('rfc', 14);
            $customer['telefono'] = apost('telefono', 25);
            $customer['email'] = apost('email', 200);
            $customer['revision'] = apost('revision', 100);
            $customer['pago'] = apost('pago', 100);
            $customer['condiciones'] = apost('condiciones', 100);
            $customer['notas'] = apost('notas', 200);

            $updated = query_update(TABLE_CUSTOMERS, $customer, "cuentaId = $customerId");

            if($updated>0) {
                system_log($customerId, TABLE_CUSTOMERS, "Update", json_encode($customer));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

	break;

	case 'del':
    
        if($global_perms['DELETE']) {

            # vars
            $customerId = (int)aget('id');
            $customer['deleted'] = 1;

            # query
            $updated = query_update(TABLE_CUSTOMERS, $customer, "cuentaId = $customerId");
                
            if($updated>0) {
                system_log($customerId, TABLE_CUSTOMERS, "Delete", json_encode($customer));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
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