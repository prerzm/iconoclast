<?php

/** RZM PHP Framework **/

# get all permissions & redirect to index.php if has no access permission
function perm_get_role_permissions($filename) {

    # vars
    $access = true;
    $filename = pathinfo($filename, PATHINFO_BASENAME);
    $moduloId = (int)query_select_single_value("moduloId", TABLE_MODULES, "moduloFiles LIKE '%$filename%'");
    $perms = array();

    if($moduloId!=0) {

        # get permissions for module
        $module_perms = sql_select("SELECT permisoId, permisoKey FROM ".TABLE_MODULES_PERMS." WHERE moduloId = $moduloId");
    
        if($module_perms!=false) {

            for($i=0; $i<count($module_perms); $i++) {

                $mod_role_perm = query_select_single_value("rolPermisoId", TABLE_ROLES_PERMS, 
                                                            "rolId = ".session_get_data("roleId")." AND permisoId = ".$module_perms[$i]['permisoId']);

                $perms[$module_perms[$i]['permisoKey']] = ($mod_role_perm!=false) ? true : false;
                
            }

            if(!$perms['READ'] && !$perms['SET']) {
                $access = false;
            }

            if(strpos($filename, "edit")!==false && $perms['EDIT']==false) {
                $access = false;
            }

        }

    } else {
        $access = false;
    }

    if($access==false && $filename!="index.php" && $filename!="ajax.php" && $filename!="file.download.php" && $filename!="x.php" && $filename!="y.php") {
        # redirect if role has no access or permission to module
        set_alert("error", "No cuenta con los permisos para acceder a este módulo.");
        redirect("index.php");
    }

    # return perms
    return $perms;

}


// Redirect based on access permission
#permissions_check
function perm_get_other_role_permissions($roleId, $module) {

    # vars
    $perms = array();

    # get permissions for module
    $moduloId = (int)query_select_single_value("moduloId", TABLE_MODULES, "moduloKey = '$module'");
    $module_perms = sql_select("SELECT permisoId, permisoKey FROM ".TABLE_MODULES_PERMS." WHERE moduloId = $moduloId");

    if($module_perms) {

        for($i=0; $i<count($module_perms); $i++) {

            $mod_role_perm = query_select_single_value("rolPermisoId", TABLE_ROLES_PERMS, 
                                                            "rolId = ".session_get_data("roleId")." AND permisoId = ".$module_perms[$i]['permisoId']);

            $perms[$module_perms[$i]['permisoKey']] = ($mod_role_perm!=false) ? true : false;

        }

    }

	return $perms;

}



?>