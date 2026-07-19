<?php

# include configuration file
include_once ('../includes/inc.init.php');
include_once ('../includes/lib.numbers.php');
require_once ("../includes/PHPExcel.php");

# return
$return = "wages.php";

# process
switch(aglobal('cmd', 25)) {

    case 'load_file':

        # vars
        $error = false;
        $contracts_added = 0;
        $pos_added = array();
        $vendors_emails = array();
        $proyectoId = (int)apost('proyectoId');
        $monto_total = 0;
        $pago_dias = (int)apost('pagoDias');

        if($global_perms['ADD']) {

            # get project
            $project = get_project($proyectoId);

            if($project===false) {
                $error = true;
                set_alert("error", "No se pudo encontrar el proyecto.");
            }

            # upload file
            if($error==false) {
                
                $file_nomina = (isset($_FILES) && isset($_FILES['nomina']) && $_FILES['nomina']['size']>0 && $_FILES['nomina']['error']==0) ? $_FILES['nomina'] : false;

                if($file_nomina!==false) {

                    $new_file_name = file_filter_filename("Nom-".substr(uniqid(), -4)."-".$file_nomina['name']);
                    $uploaded = file_upload($file_nomina['tmp_name'], $project['pathCierres'], $new_file_name);

                    if($uploaded) {

                        $filename = $project['pathCierres'].$new_file_name;

                    } else {
                        $error = true;
                        set_alert("error", $uploaded);
                    }

                } else {
                    $error = true;
                    set_alert("error", "Hubo un error al subir el archivo.");
                }

            }

            # read file
            if($error==false && file_exists($filename) && is_file($filename)) {

                $excelReader = PHPExcel_IOFactory::createReaderForFile($filename);
                $excelObj = $excelReader->load($filename);
                $worksheet = $excelObj->getSheet(0);
                $lastRow = $worksheet->getHighestRow();
                $lastColumn = $worksheet->getHighestColumn();

                # get headers
                $honorarios_header = 0;
                $ppagos_header = 0;
                $facturas_header = 0;
                $talento_header = 0;
                $invoice_header = 0;
                for ($row = 1; $row <= $lastRow; $row++) {
                    $cell = trim($worksheet->getCell('A'.$row)->getValue());
                    if($cell=="PAGO RECIBO DE HONORARIOS") { $honorarios_header = $row + 1; }
                    if($cell=="PRONTOS PAGOS FACTURA") { $ppagos_header = $row + 1; }
                    if($cell=="PAGO NORMAL FACTURA") { $facturas_header = $row + 1; }
                    if($cell=="TALENTO POR PAGAR") { $talento_header = $row + 1; }
                    if($cell=="PAGO FACTURAS AL EXTRANJERO") { $invoice_header = $row + 1; }
                }
                
            }

            # HONORARIOS
            if($error==false && $honorarios_header>0) {

                # get headers
                $col_nombre = 0; $col_rfc = 0; $col_razon = 0; $col_concepto = 0; $col_email = 0; $col_monto = 0; $col_retiva = 0; $col_retisr = 0; $col_subtotal = 0; $col_total = 0;
                foreach(range('A', 'Z') as $column) {
                    $cell = trim(mb_strtolower($worksheet->getCell($column.$honorarios_header)->getValue(), mb_detect_encoding($worksheet->getCell($column.$honorarios_header)->getValue())));
                    switch($cell) {
                        case 'nombre': $col_nombre = $column; break;
                        case 'razon social': case 'razón social': $col_razon = $column; break;
                        case 'rfc': $col_rfc = $column; break;
                        case 'concepto': $col_concepto = $column; break;
                        case 'mail al que se notifica el pago': $col_email = $column; break;
                        case 'pago normal': $col_monto = $column; break;
                        case 'ret. iva': $col_retiva = $column; break;
                        case 'ret. isr': $col_retisr = $column; break;
                        case 'subtotal': $col_subtotal = $column; break;
                        case 'total': $col_total = $column; break;
                    }
                }

                if( $col_rfc===0 || $col_razon===0 || $col_concepto===0 || $col_email===0 || $col_monto===0 || $col_retiva===0 || $col_retisr===0 || $col_subtotal===0 || $col_total===0 ) {

                    $error = true;
                    set_alert("error", "El encabezado de Pago Recibo de Honorarios no contiene las columnas necesarias (RFC, Razón Social, etc.).");

                } else {

                    # get data
                    $row = $honorarios_header + 1;
                    $nombre = "";
                    while($nombre!="SUBTOTAL") {

                        $nombre = trim($worksheet->getCell($col_nombre.$row)->getValue());
                        $rfc = strtoupper(trim($worksheet->getCell($col_rfc.$row)->getValue()));
                        $razonSocial = trim($worksheet->getCell($col_razon.$row)->getValue());
                        $concepto = trim($worksheet->getCell($col_concepto.$row)->getValue());
                        $email = trim($worksheet->getCell($col_email.$row)->getValue());
                        $monto = number_float($worksheet->getCell($col_monto.$row)->getCalculatedValue());
                        $iva = number_float($monto * TAX_IVA);
                        $retIVA = number_float($worksheet->getCell($col_retiva.$row)->getCalculatedValue());
                        $retISR = number_float($worksheet->getCell($col_retisr.$row)->getCalculatedValue());
                        $total = number_float($worksheet->getCell($col_total.$row)->getCalculatedValue());

                        if($nombre!="" && $nombre!="SUBTOTAL" && $rfc!="" && $razonSocial!="" && var_is_valid_rfc($rfc) && var_is_email($email) && $monto>0 && $total>0) {

                            # get vendor
                            $vendor = add_get_vendor($rfc, $razonSocial, $email);

                            # add pos
                            if(is_array($vendor)) {
                                $vendorId = (int)$vendor['proveedorId'];
                                $honorarios_values['proyectoId'] = $proyectoId;
                                $honorarios_values['proveedorId'] = $vendorId;
                                $honorarios_values['pagoStatusId'] = PAYMENT_STATUS_PENDING;
                                $honorarios_values['concepto'] = $concepto;
                                $honorarios_values['pagoDias'] = $pago_dias;
                                $honorarios_values['monto'] = $monto;
                                $honorarios_values['iva'] = $iva;
                                $honorarios_values['retIVA'] = $retIVA;
                                $honorarios_values['retISR'] = $retISR;
                                $honorarios_values['total'] = $total;
                                $honorarios_values['totalMXN'] = $total;
                                $honorarios_values['pagoFormaId'] = $global_company['revisionPagoFormaId'];
                                $honorarios_values['pagoMetodoId'] = $global_company['revisionPagoMetodoId'];
                                $honorarios_values['usoCfdiId'] = $global_company['revisionUsoCfdiId'];
                                $honorarios_values['facturaInfo'] = "";

                                $pos_id = query_insert(TABLE_POS, $honorarios_values);

                                if($pos_id>0) {
                                    add_po_log($pos_id, "Creado por ".session_get_data("name"));
                                    $vendors_emails[] = $email;
                                    $monto_total += $total;
                                    $pos_added[] = $pos_id;
                                    # contract
                                    if((bool)$global_company['generarContrato']) {
                                        $fields_values = array("Servicios_Proporcionados_o_Personaje" => $honorarios_values['concepto'], "Monto_de_Pago" => number_amount_to_text($honorarios_values['totalMXN'])." MXN", "Proyecto_Fecha_Inicio" => $project['fechaInicio'], "Proyecto_Fecha_Fin" => $project['fechaFin']);
                                        if(vendor_add_contract($vendor, $project, "vendor", array_to_db($fields_values), $pos_id)) {
                                            $contracts_added++;
                                        }
                                    }
                                }                                
                            }

                        } else {

                            if($nombre!="" && $nombre!="SUBTOTAL" && ($rfc!="" || $razonSocial!="" || $email!="")) {

                                $row_error = false;

                                if(!var_is_valid_rfc($rfc)) {
                                    $row_error = true;
                                    set_alert("warning", "La cuenta del proveedor $razonSocial no se agregó ya que el RFC es incorrecto ($row).");
                                }
                                if($row_error==false && $razonSocial=="") {
                                    $row_error = true;
                                    set_alert("warning", "La cuenta del proveedor $rfc no se agregó ya que falta la Razón Social ($row).");
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

                        $row++;

                    }

                }

            }

            # PRONTOS PAGOS
            if($error==false && $ppagos_header>0) {

                # get headers
                $col_nombre = 0; $col_rfc = 0; $col_razon = 0; $col_concepto = 0; $col_email = 0; $col_monto = 0; $col_iva = 0; $col_total = 0;
                foreach(range('A', 'Z') as $column) {
                    $cell = trim(mb_strtolower($worksheet->getCell($column.$ppagos_header)->getValue(), mb_detect_encoding($worksheet->getCell($column.$ppagos_header)->getValue())));
                    switch($cell) {
                        case 'nombre': $col_nombre = $column; break;
                        case 'razon social': case 'razón social': $col_razon = $column; break;
                        case 'rfc': $col_rfc = $column; break;
                        case 'concepto': $col_concepto = $column; break;
                        case 'mail al que se notifica el pago': $col_email = $column; break;
                        case 'pronto pago': $col_monto = $column; break;
                        case 'iva': $col_iva = $column; break;
                        case 'subtotal': $col_total = $column; break;
                    }
                }

                if( $col_rfc===0 || $col_razon===0 || $col_concepto===0 || $col_email===0 || $col_monto===0 || $col_iva===0 || $col_total===0 ) {

                    $error = true;
                    set_alert("error", "El encabezado de Prontos Pagos no contiene las columnas necesarias (RFC, Razón Social, etc.).");

                } else {

                    # get data
                    $row = $ppagos_header + 1;
                    $nombre = "";
                    while($nombre!="SUBTOTAL") {

                        $nombre = trim($worksheet->getCell($col_nombre.$row)->getValue());
                        $rfc = strtoupper(trim($worksheet->getCell($col_rfc.$row)->getValue()));
                        $razonSocial = trim($worksheet->getCell($col_razon.$row)->getValue());
                        $concepto = trim($worksheet->getCell($col_concepto.$row)->getValue());
                        $email = trim($worksheet->getCell($col_email.$row)->getValue());
                        $monto = number_float($worksheet->getCell($col_monto.$row)->getCalculatedValue());
                        $iva = number_float($worksheet->getCell($col_iva.$row)->getCalculatedValue());
                        $total = number_float($worksheet->getCell($col_total.$row)->getCalculatedValue());

                        if($nombre!="" && $nombre!="SUBTOTAL" && $rfc!="" && $razonSocial!="" && var_is_valid_rfc($rfc) && var_is_email($email) && $monto>0 && $total>0) {

                            # get vendor id
                            $vendor = add_get_vendor($rfc, $razonSocial, $email);

                            # add pos
                            if(is_array($vendor)) {
                                $vendorId = (int)$vendor['proveedorId'];

                                $pagos_values['proyectoId'] = $proyectoId;
                                $pagos_values['proveedorId'] = $vendorId;
                                $pagos_values['pagoStatusId'] = PAYMENT_STATUS_PENDING;
                                $pagos_values['prontoPago'] = 1;
                                $pagos_values['concepto'] = $concepto;
                                $pagos_values['pagoDias'] = $pago_dias;
                                $pagos_values['monto'] = $monto;
                                $pagos_values['iva'] = $iva;
                                $pagos_values['total'] = $total;
                                $pagos_values['totalMXN'] = $total;
                                $pagos_values['pagoFormaId'] = $global_company['revisionPagoFormaId'];
                                $pagos_values['pagoMetodoId'] = $global_company['revisionPagoMetodoId'];
                                $pagos_values['usoCfdiId'] = $global_company['revisionUsoCfdiId'];
                                $pagos_values['facturaInfo'] = "";

                                $pos_id = query_insert(TABLE_POS, $pagos_values);

                                if($pos_id>0) {
                                    add_po_log($pos_id, "Creado por ".session_get_data("name"));
                                    $vendors_emails[] = $email;
                                    $monto_total += $total;
                                    $pos_added[] = $pos_id;
                                    # contract
                                    if((bool)$global_company['generarContrato']) {
                                        $fields_values = array("Servicios_Proporcionados_o_Personaje" => $pagos_values['concepto'], "Monto_de_Pago" => number_amount_to_text($pagos_values['totalMXN'])." MXN", "Proyecto_Fecha_Inicio" => $project['fechaInicio'], "Proyecto_Fecha_Fin" => $project['fechaFin']);
                                        if(vendor_add_contract($vendor, $project, "vendor", array_to_db($fields_values), $pos_id)) {
                                            $contracts_added++;
                                        }
                                    }
                                }
                            }

                        } else {

                            if($nombre!="" && $nombre!="SUBTOTAL" && ($rfc!="" || $razonSocial!="" || $email!="")) {

                                $row_error = false;

                                if(!var_is_valid_rfc($rfc)) {
                                    $row_error = true;
                                    set_alert("warning", "La cuenta del proveedor $razonSocial no se agregó ya que el RFC es incorrecto ($row).");
                                }
                                if($row_error==false && $razonSocial=="") {
                                    $row_error = true;
                                    set_alert("warning", "La cuenta del proveedor $rfc no se agregó ya que falta la Razón Social ($row).");
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

                        $row++;

                    }

                }

            }

            # FACTURAS
            if($error==false && $facturas_header>0) {

                # get headers
                $col_nombre = 0; $col_rfc = 0; $col_razon = 0; $col_concepto = 0; $col_email = 0; $col_monto = 0; $col_iva = 0; $col_total = 0;
                foreach(range('A', 'Z') as $column) {
                    $cell = trim(mb_strtolower($worksheet->getCell($column.$facturas_header)->getValue(), mb_detect_encoding($worksheet->getCell($column.$facturas_header)->getValue())));
                    switch($cell) {
                        case 'nombre': $col_nombre = $column; break;
                        case 'razon social': case 'razón social': $col_razon = $column; break;
                        case 'rfc': $col_rfc = $column; break;
                        case 'concepto': $col_concepto = $column; break;
                        case 'mail al que se notifica el pago': $col_email = $column; break;
                        case 'pago normal': $col_monto = $column; break;
                        case 'iva': $col_iva = $column; break;
                        case 'subtotal': $col_total = $column; break;
                    }
                }

                if( $col_rfc===0 || $col_razon===0 || $col_concepto===0 || $col_email===0 || $col_monto===0 || $col_iva===0 || $col_total===0 ) {

                    $error = true;
                    set_alert("error", "El encabezado de Pago Normal Factura no contiene las columnas necesarias (RFC, Razón Social, etc.).");

                } else {

                    # get data
                    $row = $facturas_header + 1;
                    $nombre = "";
                    while($nombre!="SUBTOTAL") {

                        $nombre = trim($worksheet->getCell($col_nombre.$row)->getValue());
                        $rfc = strtoupper(trim($worksheet->getCell($col_rfc.$row)->getValue()));
                        $razonSocial = trim($worksheet->getCell($col_razon.$row)->getValue());
                        $concepto = trim($worksheet->getCell($col_concepto.$row)->getValue());
                        $email = trim($worksheet->getCell($col_email.$row)->getValue());
                        $monto = number_float($worksheet->getCell($col_monto.$row)->getCalculatedValue());
                        $iva = number_float($worksheet->getCell($col_iva.$row)->getCalculatedValue());
                        $total = number_float($worksheet->getCell($col_total.$row)->getCalculatedValue());

                        if($nombre!="" && $nombre!="SUBTOTAL" && $rfc!="" && $razonSocial!="" && var_is_valid_rfc($rfc) && var_is_email($email) && $monto>0 && $total>0) {

                            # get vendor
                            $vendor = add_get_vendor($rfc, $razonSocial, $email);

                            # add pos
                            if(is_array($vendor)) {
                                $vendorId = (int)$vendor['proveedorId'];

                                $facturas_values['proyectoId'] = $proyectoId;
                                $facturas_values['proveedorId'] = $vendorId;
                                $facturas_values['pagoStatusId'] = PAYMENT_STATUS_PENDING;
                                $facturas_values['concepto'] = $concepto;
                                $facturas_values['pagoDias'] = $pago_dias;
                                $facturas_values['monto'] = $monto;
                                $facturas_values['iva'] = $iva;
                                $facturas_values['total'] = $total;
                                $facturas_values['totalMXN'] = $total;
                                $facturas_values['pagoFormaId'] = $global_company['revisionPagoFormaId'];
                                $facturas_values['pagoMetodoId'] = $global_company['revisionPagoMetodoId'];
                                $facturas_values['usoCfdiId'] = $global_company['revisionUsoCfdiId'];
                                $facturas_values['facturaInfo'] = "";

                                $pos_id = query_insert(TABLE_POS, $facturas_values);

                                if($pos_id>0) {
                                    add_po_log($pos_id, "Creado por ".session_get_data("name"));
                                    $vendors_emails[] = $email;
                                    $monto_total += $total;
                                    $pos_added[] = $pos_id;
                                    # contracts
                                    if((bool)$global_company['generarContrato']) {
                                        $fields_values = array("Servicios_Proporcionados_o_Personaje" => $facturas_values['concepto'], "Monto_de_Pago" => number_amount_to_text($facturas_values['totalMXN'])." MXN", "Proyecto_Fecha_Inicio" => $project['fechaInicio'], "Proyecto_Fecha_Fin" => $project['fechaFin']);
                                        if(vendor_add_contract($vendor, $project, "vendor", array_to_db($fields_values), $pos_id)) {
                                            $contracts_added++;
                                        }
                                    }
                                }
                            }

                        } else {

                            if($nombre!="" && $nombre!="SUBTOTAL" && ($rfc!="" || $razonSocial!="" || $email!="")) {

                                $row_error = false;

                                if(!var_is_valid_rfc($rfc)) {
                                    $row_error = true;
                                    set_alert("warning", "La cuenta del proveedor $razonSocial no se agregó ya que el RFC es incorrecto ($row).");
                                }
                                if($row_error==false && $razonSocial=="") {
                                    $row_error = true;
                                    set_alert("warning", "La cuenta del proveedor $rfc no se agregó ya que falta la Razón Social ($row).");
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

                        $row++;

                    }

                }

            }

            # TALENTO
            if($error==false && $talento_header>0) {

                # get headers
                $col_nombre = 0; $col_rfc = 0; $col_razon = 0; $col_personaje = 0; $col_email = 0; $col_monto = 0; $col_iva = 0; $col_total = 0;
                foreach(range('A', 'Z') as $column) {
                    $cell = trim(mb_strtolower($worksheet->getCell($column.$talento_header)->getValue(), mb_detect_encoding($worksheet->getCell($column.$talento_header)->getValue())));
                    switch($cell) {
                        case 'nombre': $col_nombre = $column; break;
                        case 'personaje': $col_personaje = $column; break;
                        case 'subtotal 2': $col_monto = $column; break;
                        case 'iva': $col_iva = $column; break;
                        case 'total': $col_total = $column; break;
                        case 'agencia': $col_razon = $column; break;
                        case 'rfc': $col_rfc = $column; break;
                        case 'mail al que se notifica el pago': $col_email = $column; break;
                    }
                }

                if( $col_nombre===0 || $col_personaje===0 || $col_monto===0 || $col_iva===0 || $col_total===0 || $col_razon===0 || $col_rfc===0 || $col_email===0 ) {

                    $error = true;
                    set_alert("error", "El encabezado de Talento Por Pagar no contiene las columnas necesarias (RFC, Razón Social, etc.).");

                } else {

                    # get data
                    $row = $talento_header + 1;
                    $nombre = "";
                    while($nombre!="SUBTOTAL") {

                        $nombre = trim($worksheet->getCell($col_nombre.$row)->getValue());
                        $personaje = trim($worksheet->getCell($col_personaje.$row)->getValue());
                        $monto = number_float($worksheet->getCell($col_monto.$row)->getCalculatedValue());
                        $iva = number_float($worksheet->getCell($col_iva.$row)->getCalculatedValue());
                        $total = number_float($worksheet->getCell($col_total.$row)->getCalculatedValue());
                        $razonSocial = trim($worksheet->getCell($col_razon.$row)->getValue());
                        $rfc = strtoupper(trim($worksheet->getCell($col_rfc.$row)->getValue()));
                        $email = trim($worksheet->getCell($col_email.$row)->getValue());

                        if($nombre!="" && $nombre!="SUBTOTAL" && $rfc!="" && $razonSocial!="" && var_is_valid_rfc($rfc) && var_is_email($email) && $monto>0 && $total>0) {

                            # get vendor
                            $vendor = add_get_vendor($rfc, $razonSocial, $email);

                            # add pos
                            if(is_array($vendor)) {
                                $vendorId = (int)$vendor['proveedorId'];

                                $talento_values['proyectoId'] = $proyectoId;
                                $talento_values['proveedorId'] = $vendorId;
                                $talento_values['pagoStatusId'] = PAYMENT_STATUS_PENDING;
                                $talento_values['concepto'] = "Talento ".$personaje;
                                $talento_values['pagoDias'] = $pago_dias;
                                $talento_values['monto'] = $monto;
                                $talento_values['iva'] = $iva;
                                $talento_values['total'] = $total;
                                $talento_values['totalMXN'] = $total;
                                $talento_values['pagoFormaId'] = $global_company['revisionPagoFormaId'];
                                $talento_values['pagoMetodoId'] = $global_company['revisionPagoMetodoId'];
                                $talento_values['usoCfdiId'] = $global_company['revisionUsoCfdiId'];
                                $talento_values['facturaInfo'] = "";

                                $pos_id = query_insert(TABLE_POS, $talento_values);

                                if($pos_id>0) {
                                    add_po_log($pos_id, "Creado por ".session_get_data("name"));
                                    $vendors_emails[] = $email;
                                    $monto_total += $total;
                                    $pos_added[] = $pos_id;
                                    # contracts
                                    if((bool)$global_company['generarContrato']) {
                                        $fields_values = array("Servicios_Proporcionados_o_Personaje" => $talento_values['concepto'], "Monto_de_Pago" => number_amount_to_text($talento_values['totalMXN'])." MXN", "Proyecto_Fecha_Inicio" => $project['fechaInicio'], "Proyecto_Fecha_Fin" => $project['fechaFin']);
                                        if(vendor_add_contract($vendor, $project, "talento", array_to_db($fields_values), $pos_id)) {
                                            $contracts_added++;
                                        }
                                    }
                                }
                            }

                        } else {

                            if($nombre!="" && $nombre!="SUBTOTAL" && ($rfc!="" || $razonSocial!="" || $email!="")) {

                                $row_error = false;

                                if(!var_is_valid_rfc($rfc)) {
                                    $row_error = true;
                                    set_alert("warning", "La cuenta del proveedor $razonSocial no se agregó ya que el RFC es incorrecto ($row).");
                                }
                                if($row_error==false && $razonSocial=="") {
                                    $row_error = true;
                                    set_alert("warning", "La cuenta del proveedor $rfc no se agregó ya que falta la Razón Social ($row).");
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

                        $row++;

                    }

                }

            }

            # INVOICES EXTRANJERO
            if($error==false && $invoice_header>0) {

                # get headers
                $col_nombre = 0; $col_rfc = 0; $col_razon = 0; $col_concepto = 0; $col_email = 0; $col_total = 0; $col_moneda = 0;
                foreach(range('A', 'Z') as $column) {
                    $cell = trim(mb_strtolower($worksheet->getCell($column.$invoice_header)->getValue(), mb_detect_encoding($worksheet->getCell($column.$invoice_header)->getValue())));
                    switch($cell) {
                        case 'nombre': $col_nombre = $column; break;
                        case 'concepto': $col_concepto = $column; break;
                        case 'total': $col_total = $column; break;
                        case 'moneda': $col_moneda = $column; break;
                        case 'razon social': case 'razón social': $col_razon = $column; break;
                        case 'nif / cuit / cif / ruc': $col_rfc = $column; break;
                        case 'mail al que se notifica el pago': $col_email = $column; break;
                    }
                }

                if( $col_rfc===0 || $col_razon===0 || $col_concepto===0 || $col_email===0 || $col_total===0  || $col_moneda===0 ) {

                    $error = true;
                    set_alert("error", "El encabezado de Pago de Facturas al Extranjero no contiene las columnas necesarias (NIF, Razón Social, etc.).");

                } else {

                    # get data
                    $row = $invoice_header + 1;
                    $nombre = "";
                    while($nombre!="SUBTOTAL") {

                        $nombre = trim($worksheet->getCell($col_nombre.$row)->getValue());
                        $rfc = strtoupper(trim($worksheet->getCell($col_rfc.$row)->getValue()));
                        $razonSocial = trim($worksheet->getCell($col_razon.$row)->getValue());
                        $concepto = trim($worksheet->getCell($col_concepto.$row)->getValue());
                        $email = trim($worksheet->getCell($col_email.$row)->getValue());
                        $total = number_float($worksheet->getCell($col_total.$row)->getCalculatedValue());
                        $moneda = strtoupper(trim($worksheet->getCell($col_moneda.$row)->getValue()));
                        $moneda = (isset($global_currencies[$moneda])) ? $moneda : "MXN";

                        if($nombre!="" && $nombre!="SUBTOTAL" && $rfc!="" && $razonSocial!="" && var_is_email($email) && $total>0) {

                            # get vendor
                            $vendor = add_get_vendor($rfc, $razonSocial, $email);

                            # add pos
                            if(is_array($vendor)) {
                                $vendorId = (int)$vendor['proveedorId'];

                                $invoice_values['proyectoId'] = $proyectoId;
                                $invoice_values['proveedorId'] = $vendorId;
                                $invoice_values['pagoStatusId'] = PAYMENT_STATUS_PENDING;
                                $invoice_values['concepto'] = $concepto;
                                $invoice_values['pagoDias'] = $pago_dias;
                                $invoice_values['moneda'] = $moneda;
                                $invoice_values['tipoDeCambio'] = $global_currencies[$moneda];
                                $invoice_values['monto'] = $total;
                                $invoice_values['total'] = $total;
                                $invoice_values['totalMXN'] = $total * $invoice_values['tipoDeCambio'];
                                $invoice_values['pagoFormaId'] = $global_company['revisionPagoFormaId'];
                                $invoice_values['pagoMetodoId'] = $global_company['revisionPagoMetodoId'];
                                $invoice_values['usoCfdiId'] = $global_company['revisionUsoCfdiId'];
                                $invoice_values['facturaInfo'] = "";

                                $pos_id = query_insert(TABLE_POS, $invoice_values);

                                if($pos_id>0) {
                                    add_po_log($pos_id, "Creado por ".session_get_data("name"));
                                    $monto_total += $total;
                                    $pos_added[] = $pos_id;
                                    # contracts
                                    if((bool)$global_company['generarContrato'] && (bool)VENDOR_CONTRACT_TO_FOREIGN==true) {
                                        $fields_values = array("Servicios_Proporcionados_o_Personaje" => $invoice_values['concepto'], "Monto_de_Pago" => number_amount_to_text($invoice_values['totalMXN'])." MXN", "Proyecto_Fecha_Inicio" => $project['fechaInicio'], "Proyecto_Fecha_Fin" => $project['fechaFin']);
                                        if(vendor_add_contract($vendor, $project, "vendor", array_to_db($fields_values), $pos_id)) {
                                            $contracts_added++;
                                        }
                                    }
                                }
                            }

                        } else {

                            if($nombre!="" && $nombre!="SUBTOTAL" && ($rfc!="" || $razonSocial!="" || $email!="")) {

                                $row_error = false;

                                if(trim($rfc)=="") {
                                    $row_error = true;
                                    set_alert("warning", "La cuenta del proveedor $razonSocial no se agregó ya que el NIF / CUIT / CIF / RUC es incorrecto ($row).");
                                }
                                if($row_error==false && $razonSocial=="") {
                                    $row_error = true;
                                    set_alert("warning", "La cuenta del proveedor $rfc no se agregó ya que falta la Razón Social ($row).");
                                }
                                if($row_error==false && !var_is_email($email)) {
                                    $row_error = true;
                                    set_alert("warning", "La cuenta del proveedor $razonSocial no se agregó ya que el correo es inválido.");
                                }
                                if($row_error==false && $total==0) {
                                    $row_error = true;
                                    set_alert("warning", "La cuenta del proveedor $razonSocial no se agregó ya que el monto es inválido.");
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
                $mail->vendors_notify_pos($vendors_emails, $project['titulo']);
            }

            # notices
            if(is_array($pos_added) && count($pos_added)>0) {
                wages_add($proyectoId, $new_file_name, $monto_total, $pos_added);
                set_alert("success", "Se agregaron ".count($pos_added)." cuentas por pagar.");
                set_alert("warning", "Se agregaron $contracts_added contratos.");
            } else {
                set_alert("error", "No se agregó ninguna cuenta por pagar.");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

    case 'download':

        # vars
        $id = (int)aget('id');
        $nomina = get_nomina_info($id);

        if($nomina) {
            $project = get_project($nomina['proyectoId']);
            if(file_exists($project['pathCierres'].$nomina['archivo']) && is_file($project['pathCierres'].$nomina['archivo'])) {
                file_download(base64_encode($project['pathCierres'].$nomina['archivo']), "application/vnd.ms-excel");
                die();
            } else {
                set_alert("error", "Hubo un problema al recuperar el archivo, por favor intenta nuevamente.");
            }
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