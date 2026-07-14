<?php

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "vendors.php";

# process
switch(aglobal('cmd', 20)) {

	case 'add':
	
        if($global_perms['ADD']) {

            # vars
            $error = false;
            $vendor['rfc'] = trim(apost('rfc', 15));
            $vendor['razonSocial'] = trim(apost('razonSocial', 150));
            $vendor['extranjero'] = (int)apost('extranjero');
            $vendor['director'] = (int)apost('director');
            $vendor['repseReq'] = (int)apost('repseReq');
            $vendor['repseNumero'] = trim(apost('repseNumero', 100));
            $vendor['repseAviso'] = trim(apost('repseAviso', 100));
            $vendor['email'] = trim(apost('email', 150));
            $vendor['banco'] = trim(apost('banco', 100));
            $vendor['cuenta'] = trim(apost('cuenta', 20));
            $vendor['clabe'] = trim(apost('clabe', 20));
            $vendor['swift'] = trim(apost('swift', 30));
            $vendor['aba'] = trim(apost('aba', 30));
            $password = trim(apost('pswd',20));

            # password
            if($password!="") {
                $vendor['password'] = sec_hash_password($password);
            }

            # validate
            if(strlen(trim($vendor['rfc']))>0 && strlen(trim($vendor['razonSocial']))>0) {

                # query
                $id = query_insert(TABLE_VENDORS, $vendor);

                if($id>0) {
                    if($vendor['repseReq']==-1 && !vendor_has_carta_repse($id)) {
                        vendor_add_carta_repse($vendor);
                    }
                    system_log($id, TABLE_VENDORS, "Add", json_encode($vendor));
                    set_alert("success", "La información ha sido actualizada.");
                } else {
                    set_alert("error", "Hubo un problema, favor de intentar nuevamente");
                }

            } else {
                set_alert("error", "Hubo un problema, tanto el RFC como la Razón Social son campos obligatorios.");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'update':
	
        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $vendorId = (int)apost('id');
            $vendor['rfc'] = trim(apost('rfc', 15));
            $vendor['razonSocial'] = trim(apost('razonSocial', 150));
            $vendor['extranjero'] = (int)apost('extranjero');
            $vendor['director'] = (int)apost('director');
            $vendor['email'] = trim(apost('email', 150));
            $vendor['repseReq'] = (int)apost('repseReq');
            $vendor['repseNumero'] = trim(apost('repseNumero', 100));
            $vendor['repseAviso'] = trim(apost('repseAviso', 100));
            $vendor['banco'] = trim(apost('banco', 100));
            $vendor['cuenta'] = trim(apost('cuenta', 20));
            $vendor['clabe'] = trim(apost('clabe', 20));
            $vendor['swift'] = trim(apost('swift', 30));
            $vendor['aba'] = trim(apost('aba', 30));
            $vendor['editar'] = (int)apost('editar');
            $password = trim(apost('pswd',20));

            # password
            if($password!="") {
                $vendor['token'] = "";
                $vendor['password'] = sec_hash_password($password);
                query_delete(TABLE_USERS_ATTEMPTS, "usuarioId = $vendorId AND admin = 0");
            }

            # repse
            if((int)$vendor['repseReq']==-1 && !vendor_has_carta_repse($vendorId)) {
                $vendor['repseNumero'] = "";
                $vendor['repseAviso'] = "";
                vendor_add_carta_repse($vendorId);
            }

            # files
            $acta = vendor_document_upload($vendorId, "acta");
            if($acta!==false) {
                $vendor['acta'] = $acta;
            }
            $constancia = vendor_document_upload($vendorId, "constancia");
            if($constancia!==false) {
                $vendor['constancia'] = $constancia;
                $vendor['constancia_fecha'] = date("Y-m-d");
            }
            $opinion = vendor_document_upload($vendorId, "opinionCumplimiento");
            if($opinion!==false) {
                $vendor['opinionCumplimiento'] = $opinion;
                $vendor['opinionCumplimiento_fecha'] = date("Y-m-d");
            }
            $estado = vendor_document_upload($vendorId, "estadoDeCuenta");
            if($estado!==false) {
                $vendor['estadoDeCuenta'] = $estado;
            }
            $identificacion = vendor_document_upload($vendorId, "identificacion");
            if($identificacion!==false) {
                $vendor['identificacion'] = $identificacion;
            }
            $residencia = vendor_document_upload($vendorId, "residencia");
            if($residencia!==false) {
                $vendor['residencia'] = $residencia;
                $vendor['residencia_fecha'] = date("Y-m-d");
            }
            $repse = vendor_document_upload($vendorId, "repse");
            if($repse!==false) {
                $vendor['repse'] = $repse;
                $vendor['repse_fecha'] = date("Y-m-d");
            }
            
            # update
            $updated = query_update(TABLE_VENDORS, $vendor, "proveedorId = $vendorId");
                
            if($updated>0) {
                $return = "vendors.view.php?id=$vendorId";
                system_log($vendorId, TABLE_VENDORS, "Update", json_encode($vendor));
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
            $vendorId = (int)aget('id');
            $vendor['deleted'] = 1;

            # query
            $updated = query_update(TABLE_VENDORS, $vendor, "proveedorId = $vendorId");
                
            if($updated>0) {
                system_log($vendorId, TABLE_VENDORS, "Delete", json_encode($vendor));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'tmpset':

        # vars
        $vendorId = (int)aget('id');
        $return = "vendors.view.php";
        params_add("id", $vendorId);

        # check & change
        if((int)session_get_data("roleId")==1 && (int)session_get_data("userId")==1 && $vendorId!=1) {
            if(query_update(TABLE_VENDORS, array("tmp" => "556406b07d85ac8df12bdd0747970411"), "proveedorId = $vendorId")) {
                set_alert("success", "La información ha sido actualizada");
            }
        }

    break;

    case 'tmpunset':

        # vars
        $vendorId = (int)aget('id');
        $return = "vendors.view.php";
        params_add("id", $vendorId);

        # check & change
        if((int)session_get_data("roleId")==1 && (int)session_get_data("userId")==1 && $vendorId!=1) {
            if(query_update(TABLE_VENDORS, array("tmp" => ""), "proveedorId = $vendorId")) {
                set_alert("success", "La información ha sido actualizada");
            }
        }

    break;

    case 'delfile':

        # vars
        $vendorId = (int)aget('id');
        $return = "vendors.view.php";
        params_add("id", $vendorId);

        # process
        $vendor = get_vendor($vendorId);
        switch(aget('f')) {
            case 'ac': $field = "acta"; break;
            case 'csf': $field = "constancia"; break;
            case 'oc': $field = "opinionCumplimiento"; break;
            case 'ide': $field = "identificacion"; break;
            case 'dom': $field = "residencia"; break;
            case 'edo': $field = "estadoDeCuenta"; break;
            case 'rep': $field = "repse"; break;
        }

        # delete
        if(file_is_valid(PATH_VENDORS.$vendor[$field])) {
            @unlink(PATH_VENDORS.$vendor[$field]);
        }
        $updated = query_update(TABLE_VENDORS, array($field => ""), "proveedorId = $vendorId");
        
        # alerts
        if($updated>0) {
            set_alert("success", "La información ha sido actualizada.");
        } else {
            set_alert("error", "Hubo un problema, favor de intentar nuevamente");
        }

    break;

    /*
    case 'mailmessage':

        # get emails
        $results = sql_select("SELECT DISTINCT email FROM mail ORDER BY email ASC");
        $vendors_emails = array();

        foreach($results as $m) {
            $email = strtolower($m['email']);
            if(var_is_email($email)) {
                $vendors_emails[] = $email;
            }
        }

        # prepare mail
        if(count($vendors_emails)>0) {
            $mail = new NEWMailer();
            $sent = $mail->vendors_message($vendors_emails);
            if($sent) {
                die("Message sent successfully!!");
            }
        }

        # finish
        die("FAILED - Message not sent!!");

                    
    break;
    */

	default:

        # set error & error message on session
		set_alert("error", "Hubo un problema en la información, por favor intenta nuevamente.");
	
	break;
	
}

# redirect
redirect($return);

?>