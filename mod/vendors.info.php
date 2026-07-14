<?php

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "vendors.pos.php";

# process
switch(aglobal('cmd', 20)) {

    case 'update_info_invoice':

        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $updated = 0;
            $vendorId = (int)session_get_data("userId");
            $record = get_vendor($vendorId);

            if(vendor_allow_edit_info($vendorId)) {

                $vendor['rfc'] = apost('rfc', 15);
                $vendor['razonSocial'] = apost('razonSocial', 150);

                $repse_req = (int)apost('repseReq');
                if($repse_req==0) {
                    $error = true;
                    set_alert("error", "Es necesario que seleccione su estatus sobre el registro REPSE");
                } else {
                    $vendor['repseReq'] = $repse_req;
                    if($repse_req==-1) {
                        $vendor['repseNumero'] = "";
                        $vendor['repseAviso'] = "";
                        if(!vendor_has_carta_repse($vendorId)) {
                            vendor_add_carta_repse($vendorId);
                        }
                    } else {
                        if(vendor_has_carta_repse($vendorId)) {
                            vendor_del_carta_repse($vendorId);
                        }
                        $vendor['repseNumero'] = trim(apost('repseNumero', 30));
                        $vendor['repseAviso'] = trim(apost('repseAviso', 30));
                        if($vendor['repseNumero']=="" || $vendor['repseAviso']=="") {
                            $error = true;
                            set_alert("error", "Es necesario que ingrese la información de su registro en el REPSE");
                        }
                    }
                }

                # update
                if($error==false) {
                    $updated = query_update(TABLE_VENDORS, $vendor, "proveedorId = $vendorId");
                    # update contracts to repse contracts
                    if($repse_req==1) {
                        # es repse, update all pending contracts to repse contracts
                        $subtipo = "Contrato".get_contract_type_for_vendor($vendor['rfc'], "con repse");
                        $contratoId = query_select_single_value("contratoId", TABLE_CONTRACTS, "subtipo = '$subtipo'");
                        query_update(TABLE_CONTRACTS_VENDORS, array("contratoId" => $contratoId), "proveedorId = $vendorId AND firmaStatusId = ".CONTRACT_STATUS_PENDING." AND parentId = 0", true);
                    }
                }

            } else {
                set_alert("error", "Por el momento no tiene permitido editar su información, favor de contactar a la administración");
            }

            if($updated>0) {
                system_log($vendorId, TABLE_VENDORS, "Update", json_encode($vendor));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'update_info_bank':

        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $updated = 0;
            $vendorId = (int)session_get_data("userId");
            $record = get_vendor($vendorId);

            # allow bank data change
            if(vendor_allow_edit_info($vendorId)) {

                # validate bank
                $bank = trim(apost('banco', 100));
                if($bank!="") {
                    $vendor['banco'] = $bank;
                } else {
                    $error = true;
                    set_alert("error", "Es necesario ingresar el nombre del banco.");
                }

                # validate account
                $cuenta = trim(apost('cuenta', 20));
                if($cuenta!="") {
                    $vendor['cuenta'] = $cuenta;
                } else {
                    $error = true;
                    set_alert("error", "Es necesario ingresar el número de cuenta.");
                }

                # validate clabe
                if((bool)$record['extranjero']==false) {
                    $clabe = trim(apost('clabe', 18));
                    if($clabe!="") {
                        if(is_valid_clabe($clabe)) {
                            $vendor['clabe'] = $clabe;
                        } else {
                            $error = true;
                            set_alert("error", "La CLABE es inválida, debe ser numérica de 18 dígitos.");
                        }
                    }
                }

                # validate swift
                $swift = trim(apost('swift', 15));
                if($swift=="" || $swift=="NA") {
                    $vendor['swift'] = "";
                } else {
                    if(is_valid_swift($swift)) {
                        $vendor['swift'] = $swift;
                    }
                }

                # aba
                $vendor['aba'] = trim(apost('aba', 35));

            }

            # update
            if($error==false) {
                $updated = query_update(TABLE_VENDORS, $vendor, "proveedorId = $vendorId");
            }

            if($updated>0) {
                system_log($vendorId, TABLE_VENDORS, "Update", json_encode($vendor));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'update_info_docs':
	
        if($global_perms['EDIT']) {

            # vars
            $updated = 0;
            $vendorId = (int)session_get_data("userId");
            $record = get_vendor($vendorId);

            if(vendor_allow_edit_info($vendorId)) {

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

            }

            if($updated>0) {
                system_log($vendorId, TABLE_VENDORS, "Update", json_encode($vendor));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'delacta':

        if($global_perms['EDIT']) {

            # vars
            $vendorId = (int)session_get_data("userId");
            $vendor = get_vendor($vendorId);

            # queries
            $values['acta'] = "";
            file_delete($vendor['acta']);
            $updated = query_update(TABLE_VENDORS, $values, "proveedorId = $vendorId");
            
            if($updated>0) {
                system_log($vendorId, TABLE_VENDORS, "Update", json_encode($values));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'delcon':

        if($global_perms['EDIT']) {

            # vars
            $vendorId = (int)session_get_data("userId");
            $vendor = get_vendor($vendorId);

            # queries
            $values['constancia'] = "";
            file_delete($vendor['constancia']);
            $updated = query_update(TABLE_VENDORS, $values, "proveedorId = $vendorId");
            
            if($updated>0) {
                system_log($vendorId, TABLE_VENDORS, "Update", json_encode($values));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'delopn':

        if($global_perms['EDIT']) {

            # vars
            $vendorId = (int)session_get_data("userId");
            $vendor = get_vendor($vendorId);

            # queries
            $values['opinionCumplimiento'] = "";
            file_delete($vendor['opinionCumplimiento']);
            $updated = query_update(TABLE_VENDORS, $values, "proveedorId = $vendorId");
            
            if($updated>0) {
                system_log($vendorId, TABLE_VENDORS, "Update", json_encode($values));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'delres':

        if($global_perms['EDIT']) {

            # vars
            $vendorId = (int)session_get_data("userId");
            $vendor = get_vendor($vendorId);

            # queries
            $values['residencia'] = "";
            file_delete($vendor['residencia']);
            $updated = query_update(TABLE_VENDORS, $values, "proveedorId = $vendorId");
            
            if($updated>0) {
                system_log($vendorId, TABLE_VENDORS, "Update", json_encode($values));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'delid':

        if($global_perms['EDIT']) {

            # vars
            $vendorId = (int)session_get_data("userId");
            $vendor = get_vendor($vendorId);

            # queries
            $values['identificacion'] = "";
            file_delete($vendor['identificacion']);
            $updated = query_update(TABLE_VENDORS, $values, "proveedorId = $vendorId");
            
            if($updated>0) {
                system_log($vendorId, TABLE_VENDORS, "Update", json_encode($values));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'delrep':

        if($global_perms['EDIT']) {

            # vars
            $vendorId = (int)session_get_data("userId");
            $vendor = get_vendor($vendorId);

            # queries
            $values['repse'] = "";
            file_delete($vendor['repse']);
            $updated = query_update(TABLE_VENDORS, $values, "proveedorId = $vendorId");
            
            if($updated>0) {
                system_log($vendorId, TABLE_VENDORS, "Update", json_encode($values));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'deledo':

        if($global_perms['EDIT']) {

            # vars
            $vendorId = (int)session_get_data("userId");
            $vendor = get_vendor($vendorId);

            # queries
            $values['estadoDeCuenta'] = "";
            file_delete($vendor['estadoDeCuenta']);
            $updated = query_update(TABLE_VENDORS, $values, "proveedorId = $vendorId");
            
            if($updated>0) {
                system_log($vendorId, TABLE_VENDORS, "Update", json_encode($values));
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