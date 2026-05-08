<?php

# include configuration file
include_once ('../includes/inc.init.php');
include_once ('../includes/lib.numbers.php');
include_once ('../includes/lib.abp.reports.php');
require_once ("../includes/PHPExcel.php");

# return
$return = "budgets.php";

# process
switch(aglobal('cmd', 20)) {

    case 'budget_add':

        if($global_perms['ADD']) {

            # vars
            $error = false;
            $budget['proyectoId'] = apost('proyectoId');
            $budget['directorId'] = apost('directorId');
            $budget['diasFilmacion'] = apost('diasFilmacion');
            $budget['fechaDeRodaje'] = apost('fechaDeRodaje', 10);

            # add budget
            $id = add_budget($budget['proyectoId'], $budget['directorId'], $budget['diasFilmacion'], $budget['fechaDeRodaje']);
            
            if($id>0) {

                system_log($id, TABLE_PROJECTS_BUDGETS, "Add", json_encode($budget));
                params_add("id", $id);
                $return = "budgets.detail.php";
                
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'item_add':

        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $budgetId = (int)apost('id');
            $budget_info = get_budget($budgetId);
            $project_info = get_project($budget_info['proyectoId']);
            $concepto['presupuestoId'] = $budgetId;
            $concepto['proyectoId'] = $budget_info['proyectoId'];
            $concepto['conceptoId'] = get_next_conceptoId($budgetId);
            $concepto['parentId'] = (int)apost('parentId');
            $concepto['cuenta'] = (int)apost('cuenta');
            $concepto['nombre'] = apost('nombre', 50);
            $concepto['monto'] = number_float(apost('monto'));
            $moneda = apost('moneda', 3);
            $concepto['moneda'] = (isset($global_currencies[$moneda])) ? $moneda : "MXN";
            $rate = (isset($global_currencies[$concepto['moneda']])) ? $global_currencies[$concepto['moneda']] : 1;
            $concepto['total'] = $concepto['monto'] * $rate;

            $parent = sql_select_row("  SELECT nivel FROM ".TABLE_PROJECTS_BUDGETS_ITEMS." 
                                        WHERE presupuestoId = $budgetId AND conceptoId = ".$concepto['parentId']);
            $concepto['nivel'] = $parent['nivel'] + 1;
            query_update(TABLE_PROJECTS_BUDGETS_ITEMS, array("categoria" => 1, "monto" => 0, "total" => 0), "presupuestoId = $budgetId AND conceptoId = ".$concepto['parentId']);

            # query
            $updated = query_insert(TABLE_PROJECTS_BUDGETS_ITEMS, $concepto);

            if($updated>0) {

                system_log($updated, TABLE_PROJECTS_BUDGETS_ITEMS, "Add", json_encode($concepto));
                params_add("id", $budgetId);
                params_add("parentId", $concepto['parentId']);
                set_alert("success", "La información ha sido actualizada.");
                $return = "budgets.detail.php";

            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'item_del':

        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $budgetId = (int)aget('id');
            $itemId = (int)aget('iid');
            $item = get_concept_info($itemId);

            $updated = query_delete(TABLE_PROJECTS_BUDGETS_ITEMS, "itemId = $itemId");

            if($updated>0) {

                system_log($itemId, TABLE_PROJECTS_BUDGETS_ITEMS, "Delete", json_encode($item));
                params_add("id", $budgetId);
                set_alert("success", "La información ha sido actualizada.");
                $return = "budgets.detail.php";

                # unset parent as category if last child
                $children = get_concept_children($budgetId, $item['parentId']);

                if($children===false) {
                    set_concept_as_child($budgetId, $item['parentId']);
                }

            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'budget_update':

        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $budgetId = (int)apost('id');
            $budget = get_budget($budgetId);
            $project_info = get_project($budget['proyectoId']);
            $updated = 0;

            $monedas = $_POST['monedas'];
            $montos = $_POST['montos'];

            if(var_is_valid_array($montos)) {
                foreach($montos as $itemId => $item) {
                    $moneda = (isset($monedas[$itemId])) ? $monedas[$itemId] : "MXN";
                    $rate = (isset($global_currencies[$moneda])) ? number_float($global_currencies[$moneda]) : 1;
                    $monto = (isset($montos[$itemId])) ? number_float($montos[$itemId]) : 0;
                    $total = $monto * $rate;
                    $updated += concept_update($itemId, $moneda, $monto, $total);
                }
            }

            if($updated>0) {

                system_log(0, TABLE_PROJECTS_BUDGETS_ITEMS, "Updated", json_encode(array("Updated records:" => $updated)));
                params_add("id", $budgetId);
                set_alert("success", "La información ha sido actualizada.");
                $return = "budgets.detail.php";

            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'budget_delete':

        if($global_perms['DELETE']) {

            # vars
            $error = false;
            $budgetId = (int)aget('id');
            $budget = get_budget($budgetId);
            $updated = 0;

            # delete
            $updated += query_delete(TABLE_PROJECTS_BUDGETS, "presupuestoId = $budgetId");
            $updated += query_delete(TABLE_PROJECTS_BUDGETS_ITEMS, "presupuestoId = $budgetId");

            if($updated>0) {

                system_log($budgetId, TABLE_PROJECTS_BUDGETS, "Delete", json_encode($budget));
                set_alert("success", "La información ha sido actualizada.");

            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'download_cierre':

        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $budgetId = (int)aget('id');

            if($budgetId>0) {

                # create file
                budget_create_cierre_file($budgetId);
                die();

            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'load_cierre':

        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $vendors_added = 0;
            $pos_added = 0;
            $vendors_emails = array();
            $budgetId = (int)apost('id');
            

            # get budget, project info & invoice reqs
            if($budgetId>0) {

                $budget = get_budget($budgetId);

                if($budget) {

                    $project = get_project($budget['proyectoId']);

                    if($project) {

                        $company_info = get_company_info(session_get_data("companyId"));

                        if($company_info) {

                            $usoCfdiId = (int)$company_info['revision']['usoCfdiId'];
                            $pagoFormaId = (int)$company_info['revision']['pagoFormaId'];
                            $pagoMetodoId = (int)$company_info['revision']['pagoMetodoId'];

                        } else {

                            $error = true;
                            set_alert("error", "No se pudo encontrar la información de la empresa.");

                        }

                    } else {

                        $error = true;
                        set_alert("error", "No se pudo encontrar el proyecto.");

                    }

                } else {
    
                    $error = true;
                    set_alert("error", "No se pudo encontrar el presupuesto.");
    
                }

            } else {

                $error = true;
                set_alert("error", "Hubo un problema con el identificador del presupuesto.");
            }

            # file upload and read
            if($error==false) {

                $csv_file = (isset($_FILES) && isset($_FILES['cierre']) && $_FILES['cierre']['size']>0 && $_FILES['cierre']['error']==0) ? $_FILES['cierre'] : false;

                if($csv_file!==false) {

                    $new_file_name = file_filter_filename($csv_file['name']);
                    $uploaded = file_upload($csv_file['tmp_name'], $project['pathCierres'], $new_file_name);

                    if($uploaded===true) {

                        ini_set("auto_detect_line_endings", "1");
                        $data = array_map('str_getcsv', file($project['pathCierres'].$new_file_name));

                        if(!is_array($data)) {
                            $error = true;
                            set_alert("error", "El archivo no pudo ser leído correctamente.");
                        }

                    } else {
                        $error = true;
                        set_alert("error", $uploaded);
                    }

                } else {

                    $error = true;
                    set_alert("error", "Hubo un error al subir el archivo.");

                }

            }

            # get header
            if($error==false) {

                $header = 0;
                $start = 0;
                foreach($data as $row => $info) {
                    if(isset($info[0]) && strtolower(trim($info[0]))=="rfc") {
                        $header = $row;
                        $start = $header + 1;
                        $col_rfc = 0;
                        break;
                    }
                }

                if($header==0) {
                    $error = true;
                    set_alert("error", "No se pudo encontrar el encabezado requerido del archivo (RFC, Razón Social, etc.).");
                }

            }

            # get columns
            if($error==false) {

                $header = $data[$header];
                $col_razon = 0; $col_concepto = 0; $col_mail = 0; $col_monto = 0;
                $col_iva = 0; $col_retiva = 0; $col_retisr = 0; $col_total = 0;
                for($i=0; $i<count($header); $i++) {
                    $col = trim(mb_strtolower($header[$i], mb_detect_encoding($header[$i])));
                    switch($col) {
                        case 'razon social': case 'razón social': $col_razon = $i; break;
                        case 'concepto': $col_concepto = $i; break;
                        case 'mail': $col_mail = $i; break;
                        case 'monto': $col_monto = $i; break;
                        case 'iva': $col_iva = $i; break;
                        case 'ret iva': $col_retiva = $i; break;
                        case 'ret isr': $col_retisr = $i; break;
                        case 'total': $col_total = $i; break;
                    }
                }

                if( $col_razon==0 || $col_concepto==0 || $col_mail==0 || $col_monto==0 || $col_iva==0 || $col_retiva==0 || $col_retisr==0 || $col_total==0 ) {
                    $error = true;
                    set_alert("error", "El encabezado requerido del archivo (RFC, Razón Social, etc.) no contiene las columnas necesarias.");
                }

            }

            # process rows
            if($error==false) {

                for($i=$start;$i<count($data); $i++) {

                    # get cols
                    $row = $data[$i];
                    $rfc = strtoupper(trim($row[$col_rfc]));
                    $razonSocial = trim($row[$col_razon]);
                    $itemId = get_budget_item_id($budgetId, trim($row[$col_concepto]));
                    $email = strtolower(trim($row[$col_mail]));
                    $monto = number_float($row[$col_monto]);
                    $iva = number_float($row[$col_iva]);
                    $retIVA = number_float($row[$col_retiva]);
                    $retISR = number_float($row[$col_retisr]);
                    $total = number_float($row[$col_total]);

                    # verify info (rfc, razonSocial, email, monto, total)
                    if($rfc!="" && $razonSocial!="" && var_is_valid_rfc($rfc) && var_is_email($email) && $monto>0 && $total>0) {

                        # get vendor id
                        $vendorId = get_vendor_id($rfc);
                        if($vendorId==0) {
                            $vendorId= query_insert(TABLE_VENDORS, array("rfc" => $rfc, "razonSocial" => $razonSocial, "email" => $email, "editar" => 1));
                            $vendors_added++;
                        }

                        # add pos
                        if($vendorId>0) {
                            $values['proyectoId'] = $budget['proyectoId'];
                            $values['presupuestoId'] = $budgetId;
                            $values['itemId'] = $itemId;
                            $values['proveedorId'] = $vendorId;
                            $values['pagoStatusId'] = PAYMENT_STATUS_PENDING;
                            $values['monto'] = $monto;
                            $values['iva'] = $iva;
                            $values['retIVA'] = $retIVA;
                            $values['retISR'] = $retISR;
                            $values['total'] = $total;
                            $values['totalMXN'] = $total;
                            $values['pagoFormaId'] = $pagoFormaId;
                            $values['pagoMetodoId'] = $pagoMetodoId;
                            $values['usoCfdiId'] = $usoCfdiId;

                            if(query_insert(TABLE_POS, $values)>0) {
                                $vendors_emails[] = $email;
                                $pos_added++;
                            }
                        }

                    } else {

                        # info alerts
                        if($rfc!="") {

                            $row_error = false;

                            if(!var_is_valid_rfc($rfc)) {
                                $row_error = true;
                                set_alert("warning", "La cuenta del proveedor $razonSocial no se agregó ya que el RFC es incorrecto.");
                            }
                            if($row_error==false && $razonSocial=="") {
                                $row_error = true;
                                set_alert("warning", "La cuenta del proveedor $rfc no se agregó ya que falta la Razón Social.");
                            }
                            if($row_error==false && !var_is_email($email)) {
                                $row_error = true;
                                set_alert("warning", "La cuenta del proveedor $razonSocial no se agregó ya que el correo es inválido.");
                            }
                            if($row_error==false && ($monto==0 || $total==0)) {
                                $row_error = true;
                                set_alert("warning", "La cuenta del proveedor $razonSocial no se agregó ya los montos son inválidos.");
                            }

                        }

                    }

                }

                # email vendors
                if(count($vendors_emails)>0) {
                    #$mail = new NEWMailer();
                    #$mail->vendors_notify_pos($vendors_emails, $project['titulo']);
                }

            }

            # notice
            if($vendors_added>0) {
                set_alert("warning", "Se agregaron $vendors_added nuevos proveedores.");
            }
            if($pos_added>0) {
                set_alert("success", "Se agregaron $pos_added cuentas por pagar.");
            } else {
                set_alert("error", "No se agregó ninguna cuenta por pagar.");
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