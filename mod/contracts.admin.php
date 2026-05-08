<?php

# include configuration file
include_once ('../includes/inc.init.php');
include_once ('../includes/lib.dates.php');
include_once ('../includes/lib.numbers.php');

# return
$return = "contracts.admin.php";

# process
switch(aglobal('cmd', 20)) {

	case 'update':

        if($global_perms['EDIT']) {

            # vars
            $id = (int)apost('id');
            $contract = new ContractsAdendas($id);

            if($contract->get_id()>0) {

                # return
                $return = "contracts.admin.detail.php";
                params_add("id", $id);

                # update
                $contract->update_fields($_POST);
                $contract->save_fields();

                set_alert("success", "La información ha sido actualizada.");                 

            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'adenda':

        # vars
        $id = (int)apost('id');
        $return = "contracts.admin.detail.php";
        params_add("id", $id);

        if($global_perms['EDIT']) {

            # vars
            $contract = new ContractsAdendas($id);

            # verify fields
            if($contract->get_id()>0) {

                # get values
                $fields_values['Proyecto_Fecha_Inicio'] = apost('Proyecto_Fecha_Inicio');
                $fields_values['Proyecto_Fecha_Fin'] = apost('Proyecto_Fecha_Fin');
                $fields_values['Monto_de_Pago'] = apost('Monto_de_Pago');

                # get adenda id
                $contract_id = (int)query_select_single_value("contratoId", TABLE_CONTRACTS, "subtipo = '".str_replace("Contrato", "Adenda", $contract->get("subtipo"))."'");

                # insert adenda
                if($contract_id>0) {
                    $vendor = array("proveedorId" => $contract->get("proveedorId"), "rfc" => $contract->get("rfc"));
                    $adenda_id = vendor_add_adenda($vendor, $id, (int)$contract->get("proyectoId"), $contract->get("subtipo"), array_to_db($fields_values));
                    if($adenda_id>0) {
                        params_add("aId", $adenda_id);
                        set_alert("success", "La información ha sido actualizada.");
                    } else {
                        set_alert("error", "La adenda no pudo ser agregada");
                    }
                } else {
                    set_alert("error", "La adenda no pudo ser agregada");
                }

            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'reject':

        if($global_perms['EDIT']) {

            # vars
            $id = (int)aget('id');
            if($id<CONTRACTS_NEW_ID) {
                $contract = new ContractOld($id);
            } else {
                $contract = new ContractsAdendas($id);
            }
            
            if($contract->get_id()>0) {

                # return
                $return = "contracts.admin.detail.php";
                params_add("id", $id);

                # update
                $rejected = $contract->reject();

                if($rejected) {
                    $mail = new NEWMailer();
                    $mail->vendors_notify_contract_rejected($contract->get("email"), $contract->get("titulo"));
                    system_log($id, TABLE_CONTRACTS_VENDORS, "Update", json_encode(array("firmaStatusId" => CONTRACT_STATUS_SIGNED)));
                    set_alert("success", "La información ha sido actualizada.");                 
                }

            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'delattach':

        if($global_perms['EDIT']) {

            # vars
            $id = (int)aget('id');
            if($id<CONTRACTS_NEW_ID) {
                $contract = new ContractOld($id);
            } else {
                $contract = new ContractsAdendas($id);
            }
            
            # delete attachment
            if($contract->get_id()>0) {

                # return
                $return = "contracts.admin.detail.php";
                params_add("id", $id);

                $deleted = $contract->delete_attachment();

                if($deleted) {
                    system_log($id, TABLE_CONTRACTS_VENDORS, "Update", json_encode(array("anexo" => "")));
                    set_alert("success", "La información ha sido actualizada.");                 
                } else {
                    set_alert("error", "No se pudo eliminar el archivo, favor de intentar nuevamente.");                 
                }
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;
    
    case 'delnda':

        if($global_perms['EDIT']) {

            # vars
            $id = (int)aget('id');
            $contract = new ContractOld($id);
            
            if($contract->get_id()>0) {

                # return
                $return = "contracts.admin.detail.php";
                params_add("id", $id);

                $deleted = $contract->delete_nda();

                if($deleted) {
                    system_log($id, TABLE_CONTRACTS_VENDORS, "Update", json_encode(array("carta" => "")));
                    set_alert("success", "La información ha sido actualizada.");                 
                } else {
                    set_alert("error", "No se pudo eliminar el archivo, favor de intentar nuevamente.");                 
                }
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;
    
    case 'delad':

        if($global_perms['DELETE']) {

            # vars
            $id = (int)aget('id');
            $aId = (int)aget('aId');
            $return = "contracts.admin.detail.php";
            params_add("id", $id);

            # delete adenda
            if(query_delete(TABLE_CONTRACTS_VENDORS, "id = $aId")) {
                system_log($aId, TABLE_CONTRACTS_VENDORS, "Delete", json_encode(array("id" => "$id")));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "No se pudo eliminar la adenda, favor de intentar nuevamente.");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;
    
	case 'del':
    
        # return
        $return = "contracts.admin.detail.php";

        if($global_perms['DELETE']) {

            # vars
            $id = (int)aget('id');
            if($id<CONTRACTS_NEW_ID) {
                $contract = new ContractOld($id);
            } else {
                $contract = new ContractsAdendas($id);
            }
            
            if($contract->get_id()>0) {

                # delete
                $deleted = $contract->delete();
            
                if($deleted) {
                    $return = "contracts.admin.php";
                    system_log($id, TABLE_CONTRACTS_VENDORS, "Delete", json_encode(array("id" => $id)));
                    set_alert("success", "La información ha sido actualizada.");
                } else {
                    params_add("id", $id);
                    set_alert("error", "No se pudo eliminar el contrato, favor de intentar nuevamente.");
                }

            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'change':

        if($global_perms['EDIT']) {

            # return
            $return = "contracts.admin.detail.php";

            # vars
            $id = (int)apost('id');
            $contract = sql_select_row("SELECT * FROM ".TABLE_CONTRACTS_VENDORS." WHERE id = $id");

            if($contract) {

                # vars
                $contract_id = (int)apost('newContratoId');
                $new_id = (int)query_select_single_value("id", TABLE_CONTRACTS_VENDORS, "", "id DESC");
                $new_id++;

                # update
                if(query_update(TABLE_CONTRACTS_VENDORS, array("id" => $new_id, "contratoId" => $contract_id), "id = $id")) {
                    params_add("id", $new_id);
                    system_log($id, TABLE_CONTRACTS_VENDORS, "Update", json_encode(array("contratoId" => $contract_id)));
                    set_alert("success", "La información ha sido actualizada.");                 
                } else {
                    set_alert("error", "No se pudo actualizar el contrato seleccionado");
                }

            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'addctov':

        # vars
        $project_id = (int)apost('pid');
        $vendor_id = (int)apost('vid');
        $contract_id = (int)apost('contratoId');
        $servicios = apost('Servicios_Proporcionados_o_Personaje');
        $monto = apost('Monto_de_Pago');
        $monto_letra = (is_numeric($monto)) ? number_amount_to_text($monto)." MXN" : $monto;

        # process
        $vendor = get_vendor($vendor_id);
        $project = get_project($project_id);
        
        $values['proveedorId'] = $vendor_id;
        $values['proyectoId'] = $project_id;
        $values['contratoId'] = $contract_id;
        $values['fechaCreado'] = date("Y-m-d");
        $values['fieldsValues'] = array_to_db(array("Servicios_Proporcionados_o_Personaje" => $servicios, "Monto_de_Pago" => $monto_letra));
        $values['info'] = "";
        
        $id = query_insert(TABLE_CONTRACTS_VENDORS, $values);

        if($id>0) {

            if((bool)apost('notify_vendor')) {
                $mail = new NEWMailer();
                $mail->vendors_notify_contract([$vendor['email']], $project['titulo']);
            }

            $return = "projects.view.php";
            params_add("id", $project_id);
            system_log($id, TABLE_CONTRACTS_VENDORS, "Add", json_encode($values));
            set_alert("success", "La información ha sido actualizada.");
            
        } else {
            set_alert("error", "No se pudo agregar el contrato al proyecto.");
        }

    break;

    case 'pdf':

        # vars
        $id = (int)aget('id');
        if($id<CONTRACTS_NEW_ID) {
            $contract = new ContractOld($id);
        } else {
            $contract = new ContractsAdendas($id);
        }
        
        if($contract->get_id()>0) {
            $contract->pdf();
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