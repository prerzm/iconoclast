<?php

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "modules.php";

# process
switch(aglobal('cmd', 20)) {

	case 'add':
	
        if($global_perms['ADD']) {

            # vars
            $error = false;
            $module['moduloKey'] = apost('moduloKey', 25);
            $module['menuParentKey'] = apost('menuParentKey', 25);
            $module['menuParentName'] = apost('menuParentName', 25);
            $module['menuFile'] = apost('menuFile', 25);
            $module['modulo'] = apost('name', 25);
            $module['moduloFiles'] = apost('archivos');
            $module['orden'] = (int)apost('orden');

            # query
            $updated = query_insert(TABLE_MODULES, $module);
                
            if($updated>0) {
                system_log($updated, TABLE_MODULES, "Add", json_encode($module));
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
            $moduleId = (int)apost('id');
            $module['moduloKey'] = apost('moduloKey', 25);
            $module['menuParentKey'] = apost('menuParentKey', 25);
            $module['menuParentName'] = apost('menuParentName', 25);
            $module['menuFile'] = apost('menuFile', 25);
            $module['modulo'] = apost('name', 25);
            $module['moduloFiles'] = apost('archivos');
            $module['orden'] = (int)apost('orden');

            $updated = query_update(TABLE_MODULES, $module, "moduloId = $moduleId");
                
            if($updated>0) {
                system_log($moduleId, TABLE_MODULES, "Update", json_encode($module));
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
            $moduleId = (int)aget('id');

            # query
            $module = get_module($moduleId);
            $updated = query_delete(TABLE_MODULES, "moduloId = $moduleId");
                
            if($updated>0) {
                system_log($moduleId, TABLE_MODULES, "Delete", json_encode($module));
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
header("Location: ../$return");

?>