<?php

# include configuration file
include_once ('../includes/inc.init.php');
include_once ('../includes/lib.numbers.php');
include_once ('../includes/class.cfdi.php');

# vars
$posId = (int)aglobal('id');
$statusId = (int)aglobal('sId');
$dateFrom = aglobal('dateFrom');
$dateTo = aglobal('dateTo');

# return
$return = "vendors.pos.edit.php";
params_add("id", $posId);
params_add("sId", $statusId);
params_add("dateFrom", $dateFrom);
params_add("dateTo", $dateTo);

# process
switch(aglobal('cmd', 25)) {

	case 'invoice':

        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $updated = 0;
            $posId = (int)apost('id');
            $posInfo = get_po_info($posId);
            $global_company = get_company_info($posInfo['companyId']);

            # poll
            $poll = vendor_verify_poll_submitted(session_get_data("userId"), $posInfo['proyectoId']);

            if($poll===false) {
                $res1 = (int)apost('res1');
                $res2 = (int)apost('res2');
                $res3 = (int)apost('res3');
                $res4 = apost('res4');
                if($res1==-1 || $res2==-1 || $res3==-1) {
                    $error = true;
                    set_alert("error", "Favor de contestar la encuesta de satisfacción antes de enviar su factura.");
                } else {
                    query_insert(TABLE_POLLS_ANSWERS, array("proveedorId" => session_get_data("userId"), "proyectoId" => $posInfo['proyectoId'], "res1" => $res1, "res2" => $res2, "res3" => $res3, "res4" => $res4));
                }
            }

            if($error==false) {

                if($posInfo['extranjero']==0) {

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

                                    if($global_company['pagoPagoAPartirDe']==VENDOR_PAYMENT_INVOICE || is_null($posInfo['fechaDePago'])) {
                                        $pos['fechaDePago'] = pos_get_payment_date((int)$posInfo['companyId']);
                                    }
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

                            if($global_company['pagoPagoAPartirDe']==VENDOR_PAYMENT_INVOICE || is_null($posInfo['fechaDePago'])) {
                                $pos['fechaDePago'] = pos_get_payment_date((int)$posInfo['companyId']);
                            }
                            $pos['facturaUuid'] = $cfdi_info['UUID'];
                            $pos['facturaInfo'] = str_replace("'", "", json_encode($cfdi_info));
                            $pos['facturaNombre'] = $new_file_name;

                        }

                    }

                }

            }

            if($error==false && isset($pos) && var_is_valid_array($pos)) {
                $updated = query_update(TABLE_POS, $pos, "gastoId = $posId");
            }

            if($updated>0) {
                system_log($posId, TABLE_POS, "Update", json_encode($pos));
                add_po_log($posId, "Factura agregada por ".session_get_data("name"));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema al actualizar la información.");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'comprobante':
	
        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $updated = 0;
            $posId = (int)apost('id');
            $posInfo = get_po_info($posId);
            
            # files set
            $comprobante_xml = (isset($_FILES) && isset($_FILES['comprobanteXML']) && $_FILES['comprobanteXML']['size']>0 && $_FILES['comprobanteXML']['error']==0) ? $_FILES['comprobanteXML'] : false;
            $comprobante_pdf = (isset($_FILES) && isset($_FILES['comprobantePDF']) && $_FILES['comprobantePDF']['size']>0 && $_FILES['comprobantePDF']['error']==0) ? $_FILES['comprobantePDF'] : false;

            if($comprobante_xml===false || $comprobante_pdf===false) {
                $error = true;
                set_alert("error", "Hubo un error al subir los archivos.");
            }

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

                $values['comprobante'] = $new_file_name;

                $updated = query_update(TABLE_POS, $values, "gastoId = $posId");

                if($updated>0) {
                    system_log($posId, TABLE_POS, "Update", json_encode($values));
                    add_po_log($posId, "Comprobante de pago agregado por ".session_get_data("name"));
                    set_alert("success", "La información ha sido actualizada.");
                } else {
                    set_alert("error", "Hubo un problema al actualizar la información.");
                }

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