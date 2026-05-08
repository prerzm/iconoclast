<?php

# include configuration file
include_once ('../includes/inc.init.php');
include_once ('../includes/lib.numbers.php');
include_once ('../includes/class.cfdi.php');

# return
$return = "pos.php";

# process
switch(aglobal('cmd', 25)) {

    case 'add':
    
        if($global_perms['ADD']) {

            # vars
            $error = false;
            $invoice_exists = false;
            $pos['proyectoId'] = (int)apost('pId');
            $pos['concepto'] = apost('concepto',100);
            $pos['proveedorId'] = (int)apost('proveedorId');
            $pos['fechaDePago'] = apost('fechaDePago', 10);
            $pos['moneda'] = apost('moneda', 3);
            $pos['monto'] = number_float(apost('monto'));
            $pos['tipoDeCambio'] = $global_currencies[$pos['moneda']];
            $pos['notas'] = apost('notas', 200);
            $extranjero = vendor_is_foreign($pos['proveedorId']);
            $notify = (int)apost('notify_vendor');
            $contract = (int)apost('add_contract');

            $project = get_project($pos['proyectoId']);
            $vendor = get_vendor($pos['proveedorId']);

            # invoice
            if($extranjero==0) {
                $pos['pagoFormaId'] = (int)apost('pagoFormaId');
                $pos['pagoMetodoId'] = (int)apost('pagoMetodoId');
                $pos['usoCfdiId'] = (int)apost('usoCfdiId');
                if((bool)$global_company['extranjera']) {
                    $pos['iva'] = 0;
                    $pos['retIVA'] = 0;
                    $pos['retISR'] = 0;
                    $pos['total'] = $pos['monto'];
                } else {
                    $pos['iva'] = number_float(apost('iva'));
                    $pos['retIVA'] = number_float(apost('retIVA'));
                    $pos['retISR'] = number_float(apost('retISR'));
                    $pos['total'] = number_float(apost('total'));
                }
            } else {
                $pos['pagoFormaId'] = $global_company['comprobacionPagoFormaId'];
                $pos['pagoMetodoId'] = $global_company['comprobacionPagoMetodoId'];
                $pos['usoCfdiId'] = $global_company['comprobacionUsoCfdiId'];
                $pos['iva'] = 0;
                $pos['retIVA'] = 0;
                $pos['retISR'] = 0;
                $pos['total'] = $pos['monto'];
            }
            $pos['totalMXN'] = $pos['total'] * $pos['tipoDeCambio'];
            $pos['facturaInfo'] = "";

            # query
            if($project && $vendor) {
                $updated = query_insert(TABLE_POS, $pos);
            
                # enviar correo al proveedor
                if($updated>0 && $notify===1) {
                    $mail = new NEWMailer();
                    $mail->vendors_notify_pos(array($vendor['email']), $project['titulo']);
                }

                # contract
                if((bool)$global_company['generarContrato']) {
                    if($vendor['extranjero']==0 || (bool)VENDOR_CONTRACT_TO_FOREIGN==true) {
                        $contract = vendor_has_contract_for_project($pos['proveedorId'], $pos['proyectoId']);
                        if($contract===false) {
                            $fields_values = array_to_db(array("Servicios_Proporcionados_o_Personaje" => $pos['concepto'], "Monto_de_Pago" => number_amount_to_text($pos['totalMXN'])." MXN"));
                            vendor_add_contract($vendor, $project, "vendor", $fields_values);
                        }
                    }
                }

                if($updated>0) {
                    system_log($updated, TABLE_POS, "Add", json_encode($pos));
                    add_po_log($updated, "Creado por ".session_get_data("name"));
                    set_alert("success", "La información ha sido actualizada.");
                } else {
                    set_alert("error", "Hubo un problema, favor de intentar nuevamente");
                }

            } else {
                set_alert("error", "Hubo un problema con el proyecto o proveedor, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

	break;

	case 'update':

        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $posId = (int)apost('id');
            $return = "pos.view.php";
            params_add("id", $posId);
            $posInfo = get_po_info($posId);
            $pos['proyectoId'] = (int)apost('proyectoId');
            $pos['concepto'] = apost('concepto',100);
            $pos['proveedorId'] = (int)apost('proveedorId');
            $update_fecha_pago = apost('fechaDePago', 10);
            $pos['fechaDePago'] = (strtotime($update_fecha_pago)!==false) ? $update_fecha_pago : null;
            $pos['moneda'] = apost('moneda', 3);
            $pos['monto'] = number_float(apost('monto'));
            $pos['tipoDeCambio'] = $global_currencies[$pos['moneda']];
            $pos['pagoFormaId'] = (int)apost('pagoFormaId');
            $pos['pagoMetodoId'] = (int)apost('pagoMetodoId');
            $pos['usoCfdiId'] = (int)apost('usoCfdiId');
            $pos['referencia'] = apost('referencia', 100);
            $pos['notas'] = apost('notas', 200);
            $pos['pagoStatusId'] = (int)apost('pagoStatusId');

            # company (can be new) that will be used for generating contracts
            $new_project = get_project($pos['proyectoId']);
            $company = get_company_info($new_project['companyId']);

            # project or company changed
            if($posInfo['proyectoId']!=$pos['proyectoId']) {

                $return = "pos.php";
                $posInfo['pathFacturas'] = $new_project['pathFacturas'];
                $posInfo['pathTransfers'] = $new_project['pathTransfers'];
                $posInfo['pathComprobantes'] = $new_project['pathComprobantes'];

                # check & delete contract in old project for vendor (new)
                if(vendor_last_pos($posInfo['proveedorId'], $posInfo['proyectoId'])) {
                    vendor_remove_contract($posInfo['proveedorId'], $posInfo['proyectoId']);
                }

            }

            # vendor changed
            if($posInfo['proveedorId']!=$pos['proveedorId']) {
                
                # check & delete contract for old vendor if this was the only pos
                if(vendor_last_pos($posInfo['proveedorId'], $pos['proyectoId'])) {
                    vendor_remove_contract($posInfo['proveedorId'], $pos['proyectoId']);
                }
                
            }

            # info
            $proveedor = get_vendor($pos['proveedorId']);

            # foreign
            if($proveedor['extranjero']==0) {
                $pos['pagoFormaId'] = (int)apost('pagoFormaId');
                $pos['pagoMetodoId'] = (int)apost('pagoMetodoId');
                $pos['usoCfdiId'] = (int)apost('usoCfdiId');
                $pos['iva'] = number_float(apost('iva'));
                $pos['retIVA'] = number_float(apost('retIVA'));
                $pos['retISR'] = number_float(apost('retISR'));
                $pos['total'] = number_float(apost('total'));
            } else {
                $pos['pagoFormaId'] = $global_company['comprobacionPagoFormaId'];
                $pos['pagoMetodoId'] = $global_company['comprobacionPagoMetodoId'];
                $pos['usoCfdiId'] = $global_company['comprobacionUsoCfdiId'];
                $pos['iva'] = 0;
                $pos['retIVA'] = 0;
                $pos['retISR'] = 0;
                $pos['total'] = $pos['monto'];
            }
            $pos['totalMXN'] = $pos['total'] * $pos['tipoDeCambio'];

            # invoices
            if($proveedor['extranjero']==0) {

                $invoice_xml = (isset($_FILES) && isset($_FILES['facturaXML']) && $_FILES['facturaXML']['size']>0 && $_FILES['facturaXML']['error']==0) ? $_FILES['facturaXML'] : false;
                $invoice_pdf = (isset($_FILES) && isset($_FILES['facturaPDF']) && $_FILES['facturaPDF']['size']>0 && $_FILES['facturaPDF']['error']==0) ? $_FILES['facturaPDF'] : false;
                
                if($invoice_xml!==false && $invoice_pdf!==false) {

                    $cfdi_info = cfdi_get_info($invoice_xml);

                    if($cfdi_info!==false) {

                        if((bool)VALIDA_CFDI==true) {
                            $invalid = cfdi_valida_full($posInfo, $cfdi_info);
                        } else {
                            $invalid = false;
                        }

                        if($invalid===false) {

                            # upload
                            $new_file_name = get_invoice_filename($cfdi_info);
                            $uploaded_xml = document_upload($invoice_xml['tmp_name'], $posInfo['pathFacturas'], $new_file_name.".xml");
                            $uploaded_pdf = document_upload($invoice_pdf['tmp_name'], $posInfo['pathFacturas'], $new_file_name.".pdf");

                            if($uploaded_xml===true && $uploaded_pdf===true) {

                                $pos['facturaUuid'] = $cfdi_info['UUID'];
                                $pos['facturaInfo'] = str_replace("'", "", json_encode($cfdi_info));
                                $pos['facturaNombre'] = $new_file_name;

                            }

                        }

                    }

                }

            } else {

                $invoice_pdf = (isset($_FILES) && isset($_FILES['facturaPDF']) && $_FILES['facturaPDF']['size']>0 && $_FILES['facturaPDF']['error']==0) ? $_FILES['facturaPDF'] : false;
                
                if($invoice_pdf!==false) {

                    # info
                    $cfdi_info['UUID'] = "F".$posInfo['gastoId']."-".uniqid();

                    # upload
                    $new_file_name = get_invoice_filename($cfdi_info);
                    $uploaded_pdf = document_upload($invoice_pdf['tmp_name'], $posInfo['pathFacturas'], $new_file_name.".pdf");

                    if($uploaded_pdf===true) {

                        $pos['facturaUuid'] = $cfdi_info['UUID'];
                        $pos['facturaInfo'] = str_replace("'", "", json_encode($cfdi_info));
                        $pos['facturaNombre'] = $new_file_name;

                    }

                }

            }

            # transfers
            $transfer = (isset($_FILES) && isset($_FILES['transfer']) && $_FILES['transfer']['size']>0 && $_FILES['transfer']['error']==0) ? $_FILES['transfer'] : false;
            if($transfer!==false) {
                $transfer_filename = uniqid()."_".file_filter_filename(str_replace(",", ".", $transfer['name']), false);
                $transfer_upload = document_upload($transfer['tmp_name'], $posInfo['pathTransfers'], $transfer_filename);
                if($transfer_upload===true) {
                    $pos['transfer'] = $transfer_filename;
                }
            }
            $transfer2 = (isset($_FILES) && isset($_FILES['transfer2']) && $_FILES['transfer2']['size']>0 && $_FILES['transfer2']['error']==0) ? $_FILES['transfer2'] : false;
            if($transfer2!==false) {
                $transfer_filename = uniqid()."_".file_filter_filename(str_replace(",", ".", $transfer2['name']), false);
                $transfer2_upload = document_upload($transfer2['tmp_name'], $posInfo['pathTransfers'], $transfer_filename);
                if($transfer2_upload===true) {
                    $pos['transfer2'] = $transfer_filename;
                }
            }
            $transfer3 = (isset($_FILES) && isset($_FILES['transfer3']) && $_FILES['transfer3']['size']>0 && $_FILES['transfer3']['error']==0) ? $_FILES['transfer3'] : false;
            if($transfer3!==false) {
                $transfer_filename = uniqid()."_".file_filter_filename(str_replace(",", ".", $transfer3['name']), false);
                $transfer3_upload = document_upload($transfer3['tmp_name'], $posInfo['pathTransfers'], $transfer_filename);
                if($transfer3_upload===true) {
                    $pos['transfer3'] = $transfer_filename;
                }
            }

            # comprobante

            # files set
            $comprobante_xml = (isset($_FILES) && isset($_FILES['comprobanteXML']) && $_FILES['comprobanteXML']['size']>0 && $_FILES['comprobanteXML']['error']==0) ? $_FILES['comprobanteXML'] : false;
            $comprobante_pdf = (isset($_FILES) && isset($_FILES['comprobantePDF']) && $_FILES['comprobantePDF']['size']>0 && $_FILES['comprobantePDF']['error']==0) ? $_FILES['comprobantePDF'] : false;

            if($comprobante_xml!==false && $comprobante_pdf!==false) {

                # validate xml
                libxml_use_internal_errors(TRUE);
                $dom = new DOMDocument();
                $dom->load($comprobante_xml['tmp_name']);
                $errors = libxml_get_errors();

                if(empty($errors) || (is_array($errors) && count($errors)==1 && $errors[0]->code==99) ) {
                    
                } else {
                    $error = true;
                    set_alert("error", "Hubo un error al procesar el archivo xml o el archivo es inválido.");
                }

                # file upload
                if($error==false) {
                    $new_file_name = get_comprobante_filename($comprobante_xml['name']);
                    $uploaded_xml = document_upload($comprobante_xml['tmp_name'], $posInfo['pathComprobantes'], $new_file_name.".xml");
                    $uploaded_pdf = document_upload($comprobante_pdf['tmp_name'], $posInfo['pathComprobantes'], $new_file_name.".pdf");

                    if($uploaded_pdf!==true || $uploaded_xml!==true) {
                        $error = true;
                        set_alert("error", "La factura no se pudo subir correctamente al servidor.");
                    }
                }

                # update
                if($error==false) {

                    $pos['comprobante'] = $new_file_name;

                }

            }

            # query
            $updated = query_update(TABLE_POS, $pos, "gastoId = $posId");

            if($updated>0) {
                system_log($posId, TABLE_POS, "Update", json_encode($pos));
                add_po_log($posId, "Modificado por ".session_get_data("name"));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'auth':

        if($global_perms['AUTHORIZE']) {

            # vars
            $error = false;
            $posId = (int)aget('id');
            $pos['pagoStatusId'] = PAYMENT_STATUS_AUTHORIZED;

            # query
            $updated = query_update(TABLE_POS, $pos, "gastoId = $posId");

            # return
            $return = "pos.view.php";
            params_add("id", $posId);

            if($updated>0) {
                system_log($posId, TABLE_POS, "Update", json_encode($pos));
                add_po_log($posId, "Autorizado por ".session_get_data("name"));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;
    
    case 'pay':

        if($global_perms['PAY']) {

            # vars
            $error = false;
            $posId = (int)apost('id');
            $pos = get_po_info($posId);
            $notify = (int)apost('notify_vendor');

            $values['pagoStatusId'] = PAYMENT_STATUS_PAYED;
            $values['referencia'] = apost('referencia');

            # fecha pago
            $update_fecha_pago = (int)apost('update_fecha_pago');
            if($update_fecha_pago==1) {
                $values['fechaDePago'] = date("Y-m-d");
            } elseif($update_fecha_pago==2) {
                $values['fechaDePago'] = apost('fechaDePago', 10);
            }

            # transfer
            $transfer = ($_FILES['transfer']['size']>0 && $_FILES['transfer']['error']==0) ? $_FILES['transfer'] : false;
            if($transfer!==false) {
                $transfer_filename = file_filter_filename(str_replace(",", ".", $transfer['name']), false);
                $transfer_upload = document_upload($transfer['tmp_name'], $pos['pathTransfers'], $transfer_filename);
                if($transfer_upload===true) {
                    $values['transfer'] = $transfer_filename;
                }
            }

            # moneda
            if($pos['moneda']!="MXN") {
                $values['totalMXN'] = (float)apost('monto');
                $values['tipoDeCambio'] = $values['totalMXN'] / $pos['monto'];
            }

            # query
            $updated = query_update(TABLE_POS, $values, "gastoId = $posId");

            # enviar correo al proveedor
            if($updated>0 && $notify===1) {
                $mail = new NEWMailer();
                $mail->vendors_notify_payed($pos['email'], $pos['titulo']);
            }

            # return
            $return = "pos.view.php";
            params_add("id", $posId);

            if($updated>0) {
                system_log($posId, TABLE_POS, "Update", json_encode($values));
                add_po_log($posId, "Pagado por ".session_get_data("name"));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;
    
    case 'mass_auth':

        # vars
        $updated_total = 0;
        $pos = (isset($_POST['pos']) && is_array($_POST['pos'])) ? $_POST['pos'] : false;

        # action
        if($pos!==false && $global_perms['AUTHORIZE']) {

            foreach($pos as $posId => $value) {
                if(get_po_status($posId)==PAYMENT_STATUS_PENDING) {
                    $updated = query_update(TABLE_POS, array("pagoStatusId" => PAYMENT_STATUS_AUTHORIZED), "gastoId = $posId");
                    if($updated>0) {
                        $updated_total++;
                        add_po_log($posId, "Autorizado por ".session_get_data("name"));
                        system_log($posId, TABLE_POS, "Update", json_encode(array("pagoStatusId" => PAYMENT_STATUS_AUTHORIZED)));
                    }
                } else {
                    $posInfo = get_po_info($posId);
                    set_alert("warning", "El pago a la cuenta ".$posInfo['concepto']." ya fue Autorizado o Pagado anteriormente");
                }
            }

        }

        if($updated_total>0) {
            set_alert("success", "La información ha sido actualizada.");
        } else {
            set_alert("error", "Hubo un problema, favor de intentar nuevamente");
        }

    break;

    case 'delinv':

        if($global_perms['EDIT']) {

            # vars
            $posId = (int)aget('id');
            $pos = get_po_info($posId);
            
            $values['facturaUuid'] = "";
            $values['facturaInfo'] = "";
            $values['facturaNombre'] = "";

            # queries
            file_delete($pos['facturaPDF']);
            file_delete($pos['facturaXML']);
            $updated = query_update(TABLE_POS, $values, "gastoId = $posId");
            
            # return
            $return = "pos.view.php";
            params_add("id", $posId);

            if($updated>0) {
                add_po_log($posId, "Factura eliminada por ".session_get_data("name"));
                system_log($posId, TABLE_POS, "Update", json_encode($values));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'deltransfer':

        if($global_perms['EDIT']) {

            # vars
            $posId = (int)aget('id');
            $pos = get_po_info($posId);
            $transfer = (int)aget('t');
            $t = ($transfer==0 || $transfer>3) ? "" : $transfer;

            # queries
            $values['transfer'.$t] = "";
            file_delete($pos['transfer'.$t]);
            $updated = query_update(TABLE_POS, $values, "gastoId = $posId");
            
            # return
            $return = "pos.view.php";
            params_add("id", $posId);

            if($updated>0) {
                system_log($posId, TABLE_POS, "Update", json_encode($values));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'delcomp':

        if($global_perms['EDIT']) {

            # vars
            $posId = (int)aget('id');
            $pos = get_po_info($posId);

            # queries
            $values['comprobante'] = "";
            file_delete($pos['comprobantePDF']);
            file_delete($pos['comprobanteXML']);
            $updated = query_update(TABLE_POS, $values, "gastoId = $posId");
            
            # return
            $return = "pos.view.php";
            params_add("id", $posId);

            if($updated>0) {
                system_log($posId, TABLE_POS, "Update", json_encode($values));
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
            $posId = (int)aget('id');
            $pos = get_po_info($posId);

            # check contracts
            $last_pos = vendor_last_pos($pos['proveedorId'], $pos['proyectoId']);
            if($last_pos) {
                vendor_remove_contract($pos['proveedorId'], $pos['proyectoId']);
            }
            
            # queries
            file_delete($pos['facturaPDF']);
            file_delete($pos['facturaXML']);
            file_delete($pos['transfer']);
            file_delete($pos['comprobante']);
            $updated = query_delete(TABLE_POS, "gastoId = $posId");
            
            if($updated>0) {
                system_log($posId, TABLE_POS, "Delete", json_encode($pos));
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