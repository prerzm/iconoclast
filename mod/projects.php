<?php

# include configuration file
include_once ('../includes/inc.init.php');
require_once ("../includes/PHPExcel.php");

# return
$return = "projects.php";

# process
switch(aglobal('cmd', 20)) {

    case 'add':
        
        if($global_perms['ADD']) {

            # vars
            $error = false;
            $project['uniqId'] = uniqid();
            $project['companyId'] = session_get_data("companyId");
            $project['clave'] = apost('clave', 10);
            $project['ano'] = date("Y");
            $project['titulo'] = apost('titulo', 150);
            $project['cliente'] = apost('cliente', 150);
            $project['directorId'] = (int)apost('directorId', 150);
            $project['director'] = get_director_name($project['directorId']);
            $project['fechaInicio'] = apost('fechaInicio', 10);
            $project['fechaFin'] = apost('fechaFin', 10);
            $project['lugar'] = apost('lugar', 100);
            $project['productor'] = apost('productor', 150);
            $project['productorLinea'] = apost('productorLinea', 150);

            # create project
            $projectId = query_insert(TABLE_PROJECTS, $project);

            if($projectId>0) {

                # folders
                project_create_paths($project['uniqId']);

                # log
                system_log($projectId, TABLE_PROJECTS, "Add", json_encode($project));

                set_alert("success", "La información ha sido actualizada");

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
            $updated = 0;
            $projectId = (int)apost('id');
            $project = get_project($projectId);
            $values['clave'] = apost('clave', 10);
            $values['ano'] = (int)apost('ano');
            $values['titulo'] = apost('titulo', 150);
            $values['cliente'] = apost('cliente', 150);
            $values['lugar'] = apost('lugar', 100);
            $values['directorId'] = (int)apost('directorId', 150);
            $values['director'] = get_director_name($values['directorId']);
            $values['productor'] = apost('productor', 150);
            $values['productorLinea'] = apost('productorLinea', 150);

            # dates
            $start = strtotime(apost('fechaInicio', 10));
            if($start>0 && $start!==false) {
                $values['fechaInicio'] = apost('fechaInicio', 10);
            } else {
                $values['fechaInicio'] = $values['ano']."-01-01";
            }
            $end = strtotime(apost('fechaFin', 10));
            if($end>0 && $end!==false) {
                $values['fechaFin'] = apost('fechaFin', 10);
            } else {
                $values['fechaFin'] = $values['ano']."-01-05";
            }

            # company
            $company_id = (int)apost('companyId');
            if($company_id>0 && $company_id!=(int)$project['companyId']) {
                $values['companyId'] = $company_id;
            }

            # update
            $updated += query_update(TABLE_PROJECTS, $values, "proyectoId = $projectId");

            if($updated>0) {
                $return = "projects.view.php";
                params_add("id", $projectId);
                system_log($projectId, TABLE_PROJECTS, "Update", json_encode($values));
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
            $projectId = (int)aget('id');
            $project['deleted'] = 1;

            # query
            $updated = query_update(TABLE_PROJECTS, $project, "proyectoId = $projectId");
                
            if($updated>0) {
                system_log($projectId, TABLE_PROJECTS, "Delete", json_encode($project));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'on':
    
        if($global_perms['EDIT']) {

            # vars
            $projectId = (int)aget('id');
            $project['activo'] = 1;

            # query
            $updated = query_update(TABLE_PROJECTS, $project, "proyectoId = $projectId");
                
            if($updated>0) {
                system_log($projectId, TABLE_PROJECTS, "Show", json_encode($project));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'off':
    
        if($global_perms['EDIT']) {

            # vars
            $projectId = (int)aget('id');
            $project['activo'] = 0;

            # query
            $updated = query_update(TABLE_PROJECTS, $project, "proyectoId = $projectId");
                
            if($updated>0) {
                system_log($projectId, TABLE_PROJECTS, "Hide", json_encode($project));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'load_crew_file':

        # vars
        $error = false;
        $contracts_added = array();
        $header = 0;
        $vendors_emails = array();
        $file_array = false;
        $proyectoId = (int)aglobal('id');

        if($global_perms['EDIT']) {

            # get project & contracts
            $project = get_project($proyectoId);

            if($project===false) {
                $error = true;
                set_alert("error", "No se pudo encontrar el proyecto.");
            }

            # upload file
            if($error==false) {

                $upload = new FileUpload($_FILES['crewfile'], $project['pathCrews'], ["xlsx"]);

                if($upload->file_submitted()) {
                    if($upload->upload()) {
                        $file_array = $upload->get_array_for_db();
                    } else {
                        $error = true;
                        set_alert("error", $upload->get_error());
                    }
                    
                }
                
            }

            # read file
            $filename = (is_array($file_array)) ? $file_array['path'].$file_array['saved'] : false;
            if($error==false && file_exists($filename) && is_file($filename)) {

                $excelReader = PHPExcel_IOFactory::createReaderForFile($filename);
                $excelObj = $excelReader->load($filename);
                $worksheet = $excelObj->getSheet(0);
                $lastRow = $worksheet->getHighestRow();
                $lastColumn = $worksheet->getHighestColumn();

                # get header
                $header = 0;
                for ($row = 1; $row <= $lastRow; $row++) {
                    $cell = trim($worksheet->getCell('A'.$row)->getValue());
                    if($cell=="CREW") { $header = $row + 1; }
                }
                
            }

            # CREW
            if($error==false && $header>0) {

                # get headers
                $col_rfc = 0; $col_razon = 0; $col_email = 0;
                foreach(range('A', 'Z') as $column) {
                    $cell = trim(mb_strtolower($worksheet->getCell($column.$header)->getValue(), mb_detect_encoding($worksheet->getCell($column.$header)->getValue())));
                    switch($cell) {
                        case 'rfc': $col_rfc = $column; break;
                        case 'razon social': case 'razón social': $col_razon = $column; break;
                        case 'email': case 'mail': $col_email = $column; break;
                        case 'puesto': $col_puesto = $column; break;
                        case 'tipo de contrato': $col_contrato = $column; break;
                    }
                }

                if( $col_email===0 ) {

                    $error = true;
                    set_alert("error", "El encabezado del listado no contiene las columnas necesarias (RFC, Razón Social, Email, etc.).");

                } else {

                    # get data
                    $row = $header + 1;
                    $email = "-";
                    while(trim($email)!="" && $row<500) {

                        $rfc = strtoupper(trim($worksheet->getCell($col_rfc.$row)->getValue()));
                        $razonSocial = trim($worksheet->getCell($col_razon.$row)->getValue());
                        $email = trim($worksheet->getCell($col_email.$row)->getValue());
                        $puesto = trim($worksheet->getCell($col_puesto.$row)->getValue());
                        $contrato = strtolower(trim($worksheet->getCell($col_contrato.$row)->getValue()));

                        if(var_is_email($email) && $rfc!="" && $razonSocial!="") {

                            # get/add vendor
                            $vendor = add_get_vendor($rfc, $razonSocial, $email, $contrato);

                            # add contract
                            if(is_array($vendor)) {

                                $vendorId = (int)$vendor['proveedorId'];
                                $extranjero = (bool)$vendor['extranjero'];
    
                                if((bool)$global_company['generarContrato'] && ($extranjero==false || VENDOR_CONTRACT_TO_FOREIGN==true) ) {
                                    if(vendor_has_contract_for_project($vendorId, $proyectoId)===false) {
                                        $fields_values = array_to_db(array("Servicios_Proporcionados_o_Personaje" => $puesto));
                                        $contract_id = vendor_add_contract($vendor, $project, $contrato, $fields_values);
                                        if($contract_id>0) {
                                            $contracts_added[] = $contract_id;
                                            $vendors_emails[] = $email;
                                        }
                                    }
                                } else {
                                    if( (bool)$global_company['generarContrato'] && $extranjero==true && VENDOR_CONTRACT_TO_FOREIGN==false ) {
                                        set_alert("warning", "El proveedor en la línea $row es extranjero y el sistema está configurado para no agregar contratos a extranjeros.");
                                    }
                                }

                            } else {

                                set_alert("error", "No se encontró o no se pudo agregar al proveedor del renglón $row.");

                            }

                        } else {

                            if( $razonSocial!="" || $email!="" ) {

                                if(!var_is_email($email)) {
                                    set_alert("error", "El contrato del proveedor $razonSocial (renglón $row) no se agregó ya que el correo $email es inválido.");
                                }
                                if($razonSocial=="") {
                                    set_alert("error", "El contrato del proveedor $email (renglón $row) no se agregó ya que la razón social $razonSocial es inválida.");
                                }

                            }

                        }

                        $row++;

                    }

                }

            }

            # email vendors
            if(count($vendors_emails)>0) {
                $mail = new NEWMailer();
                $mail->vendors_notify_contract($vendors_emails, $project['titulo']);
            }
            
            # notice
            if(is_array($contracts_added) && count($contracts_added)>0) {
                set_alert("success", "Se agregaron ".count($contracts_added)." contratos.");
            } else {
                set_alert("error", "No se agregó ningún contrato.");
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
if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_DISPLAY) {
    redirect($return);
} else {
    redirect($return);
}

?>