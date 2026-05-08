<?php

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "master.php";

# process
switch(aglobal('cmd', 20)) {

	case 'add':
	
        if($global_perms['ADD']) {

            # vars
            $error = false;
            $concepto['parentId'] = (int)apost('parentId');
            $concepto['cuenta'] = (int)apost('cuenta');
            $concepto['nombre'] = apost('nombre', 50);

            if($concepto['parentId']>0) {
                $parent = sql_select_row("SELECT nivel FROM ".TABLE_MASTER." WHERE conceptoId = ".$concepto['parentId']);
                $concepto['nivel'] = $parent['nivel'] + 1;
                query_update(TABLE_MASTER, array("categoria" => 1), "conceptoId = ".$concepto['parentId']);
            }

            # query
            $updated = query_insert(TABLE_MASTER, $concepto);

            if($updated>0) {
                system_log($updated, TABLE_MASTER, "Add", json_encode($concepto));
                params_add("parentId", $concepto['parentId']);
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
            $conceptoId = (int)apost('id');
            $concepto['cuenta'] = (int)apost('cuenta');
            $concepto['nombre'] = apost('nombre', 50);

            # query
            $updated = query_update(TABLE_MASTER, $concepto, "conceptoId = $conceptoId");

            if($updated>0) {
                system_log($conceptoId, TABLE_MASTER, "Update", json_encode($concepto));
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
            $conceptoId = (int)aget('id');
            $record = sql_select_row("SELECT * FROM ".TABLE_MASTER." WHERE conceptoId = $conceptoId");
            
            # query
            $updated = query_delete(TABLE_MASTER, "conceptoId = $conceptoId AND categoria = 0");

            if($record['parentId']>0) {
                $parent = sql_select_row("SELECT conceptoId FROM ".TABLE_MASTER." WHERE conceptoId = ".$record['parentId']);
                $children = sql_select("SELECT conceptoId FROM ".TABLE_MASTER." WHERE parentId = ".$parent['conceptoId']);
                if($children===false) {
                    query_update(TABLE_MASTER, array("categoria" => 0), "conceptoId = ".$parent['conceptoId']);
                }
            }

            if($updated>0) {
                system_log($conceptoId, TABLE_MASTER, "Delete", json_encode($record));
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
#header("Location: ../$return");

?>