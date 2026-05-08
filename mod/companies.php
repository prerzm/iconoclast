<?php

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "companies.php";

# process
switch(aglobal('cmd', 20)) {

    case 'update':

        if($global_perms['EDIT']) {

            # vars
            $updated = 0;
            $companyId = (int)apost('id');
            $fields = (isset($_POST['field'])) ? $_POST['field'] : false;

            # regimen
            $values['regimenId'] = (int)apost('regimenId');

            # firma
            $file_signature = (isset($_FILES) && isset($_FILES['firmaContratos']) && $_FILES['firmaContratos']['size']>0 && $_FILES['firmaContratos']['error']==0) ? $_FILES['firmaContratos'] : false;

            if($file_signature!==false) {
                $new_file_name = file_filter_filename("Sign-".substr(uniqid(), -4)."-".$file_signature['name']);
                $uploaded = file_upload($file_signature['tmp_name'], PATH_SIGNATURES, $new_file_name);
                if($uploaded) {
                    $values['firmaContratos'] = $new_file_name;
                } else {
                    set_alert("error", $uploaded);
                }
            }

            # query
            if(is_array($fields)) {
                $values['info'] = json_encode($fields, JSON_UNESCAPED_UNICODE);
                $updated = query_update(TABLE_COMPANIES, $values, "companyId = $companyId");
            }

            if($updated>0) {
                system_log($companyId, TABLE_COMPANIES, "Update", json_encode($values));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'set':

        # vars
        $filename = basename($_SERVER['HTTP_REFERER']);
        $return = (file_exists("../$filename") && is_file("../$filename")) ? $filename : "index.php";
        $company_id = (int)aget('id');
        $company = get_company_info($company_id);
        if($company) {
            session_set_data(array("companyId" => $company_id));
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