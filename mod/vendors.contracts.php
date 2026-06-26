<?php

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "vendors.contracts.php";

# process
switch(aglobal('cmd', 25)) {

    case 'set':

        # vars
        $contract_vendor_id = (int)aget('id');

        if($contract_vendor_id<CONTRACTS_NEW_ID) {

            $vendorId = session_get_data("userId");
            $vendor = get_vendor($vendorId);
            $repse_req = (int)query_select_single_value("repseReq", TABLE_VENDORS, "proveedorId = $vendorId");
            if($repse_req==1) {
                $subtipo = "con repse";
            } else {
                $subtipo = "vendor";
            }
            $subtipo = "Contrato".get_contract_type_for_vendor($vendor["rfc"], $subtipo);
            $contratoId = query_select_single_value("contratoId", TABLE_CONTRACTS, "subtipo = '$subtipo'");
            $new_id = (int)query_select_single_value("id", TABLE_CONTRACTS_VENDORS, "", "id DESC") + 1;

            query_update(TABLE_CONTRACTS_VENDORS, array("id" => $new_id, "contratoId" => $contratoId), "id = $contract_vendor_id");
            $contract_vendor_id = $new_id;

        }

        # contract
        $contract = new ContractsAdendas($contract_vendor_id);

        # set contract
        if($contract->get_id()>0) {
            session_set_data(array("contract_id" => $contract_vendor_id));
            $fields = $contract->get_vendor_fields();
            if(count($fields)>0) {
                redirect("vendors.contracts.form.php");
            } else {
                redirect("vendors.contracts.sign.php");
            }
        }

        # error
        set_alert("error", "El contrato seleccionado no existe");

    break;
    
    case 'fill':

        # vars
        $contract_vendor_id = (int)session_get_data("contract_id");
        $contract = new ContractsAdendas($contract_vendor_id);

        # update info in session
        if($contract->get_id()>0) {
            contract_upload_attachment($contract_vendor_id, $contract->get("rfc"), $contract->get("pathContratos"));
            $contract->update_fields($_POST);
            $contract->save_fields();
            redirect("vendors.contracts.sign.php");
        }

        set_alert("error", "Hubo un problema al llenar el contrato seleccionado.");

    break;

	case 'sign':

        # vars
        $contract_vendor_id = (int)session_get_data("contract_id");
        $agreed = (int)apost('agreed');
        $signed = (int)apost('signed');
        $image_str = $_POST['image'];
        $contract = new ContractsAdendas($contract_vendor_id);
        $signature = new SignatureImage();
        $signature->fromstr($image_str);

        if($contract->get_id()>0 && $signature->valid()) {
            if($agreed==1 && $signed==1) {
                $contract->sign($signature->str());
                session_unset_data("contract_id");
                $return = "vendors.contracts.php";
                set_alert("success", "El documento ha sido firmado correctamente.");
            } else {
                set_alert("error", "Es necesario que de clic en la casilla para firmar el documento.");
            }
        } else {
            set_alert("error", "Hubo un problema al llenar el contrato seleccionado.");
        }
        
    break;

    case 'download':

        # vars
        $id = (int)aget('id');

        # records
        $record = get_contract_vendor($id);
        $project = get_project($record['proyectoId']);

        $file_contrato = $project['pathContratos'].$record['contrato'];
        $file_anexo = $project['pathContratos'].$record['anexo'];

        # process
        if(file_is_valid($file_contrato)) {

            if(file_is_valid($file_anexo)) {

                require("../includes/class.concatpdf.php");
                
                $pdf = new ConcatPdf();
                $pdf->setFiles(array($file_contrato, $file_anexo));
                $pdf->concat();
                
                $pdf->Output();

            } else {

                file_download(base64_encode($file_contrato), "pdf");
                die();

            }

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