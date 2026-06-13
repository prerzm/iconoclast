<?php

/** Reports functions **/

# Styles for Excel reports
function get_style($type) {

	$styles = array();
	/*$styleCat[1] = array(
							'font'  => array('bold'  => true, 'color' => array('rgb' => 'ffffff'), 'size'  => 12, 'name'  => 'Verdana'),
							'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
							'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'e3d9fa') ), 
							'borders' => array('allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN ) )
						);*/
	$styles["title"] = array(
							'font'		=> array('size' => 16, 'bold'  => true),
							'alignment'	=> array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
						);
	$styles["header"] =	array(
							'font'		=> array('bold'  => true, 'color' => array('rgb' => '1a1a1a')),
							'alignment'	=> array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
							'borders'	=> array('bottom' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN ) ),
							'fill'		=> array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'dbdbdb') )
						);
	$styles["footer"] =	array(
							'font'		=> array('bold'  => true, 'color' => array('rgb' => '1a1a1a')),
							'alignment'	=> array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
							'borders'	=> array('top' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN ) ),
							'fill'		=> array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'dbdbdb') )
						);
	$styles["bordersOutline"] = array(
							'borders' => array('outline' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN ) )
						);
	$styles["bordersLeft"] = array(
							'borders' => array('left' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN ) )
						);
	$styles["cat0"] = array(
							'fill'	=> array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'e9e9e9') )
							);
	$styles["cat1"] = array(
							'fill'	=> array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'f9f9f9') )
							);

	$styles["cierreTitle"] = array(
								'font'		=> array('size' => 16, 'bold'  => true)
								);
	$styles["cierreSubtitle"] = array(
								'font'		=> array('size' => 14, 'bold'  => true),
								'borders' => array('outline' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN ) )
								);
	$styles["cierreHeader"] =	array(
									'font'		=> array('bold'  => true, 'color' => array('rgb' => '1a1a1a')),
									'alignment'	=> array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
									'borders'	=> array('bottom' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN ) ),
									'fill'		=> array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'dbdbdb') )
								);
	$styles["row0"] =	array(
							'alignment'	=> array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER), 
							'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ebf1de') )
						);
	$styles["row1"] =	array(
							'alignment'	=> array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER), 
							'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ddd9c4') )
						);
	$styles["ppago"] = array( 'font' => array('color' => array('rgb' => 'FF0000'), 'bold'  => true) );
					
	return $styles[$type];

}


# REP_POS - RepPos - Cuentas por Pagar
function get_report_pos($filters) {

    # filters
	$header = array("Fecha", "Clave", "Proyecto", "Concepto", "Proveedor", "Factura", "Forma de Pago", "Monto", "IVA", "IVA Ret", "ISR Ret", "Total MXN", "Estatus", "Total", "Banco", "CLABE", "ABA", "SWIFT");

	if($filters['projectId']>0) {
		$sql_project = "AND g.proyectoId = ".$filters['projectId'];
	} else {
		$sql_project = "";
	}

    if(strtotime($filters['dateFrom'])==false || strtotime($filters['dateTo'])==false) {
        $sql_date = "";
    } else {
        $sql_date = "AND g.fechaDePago BETWEEN '".$filters['dateFrom']."' AND '".$filters['dateTo']."'";
    }

    if($filters['pagoStatusId']>0) {
        $sql_status = "AND g.pagoStatusId = ".$filters['pagoStatusId'];
    } else {
        $sql_status = "";
    }

	if($filters['ordenarPor']==1) {
		$sort = "fechaDePago ASC, clave ASC, titulo ASC, v.razonSocial ASC";
	} elseif($filters['ordenarPor']==2) {
		$sort = "fechaDePago DESC, clave ASC, titulo, v.razonSocial ASC";
	} elseif($filters['ordenarPor']==3) {
		$sort = "v.razonSocial ASC, fechaDePago ASC, clave ASC, titulo ASC";
	}
    
    # query
    $results = sql_select(" SELECT 	g.fechaDePago, p.clave, p.titulo, g.prontoPago, g.concepto, v.razonSocial, g.facturaUuid, pf.pagoForma, 
									g.monto, g.moneda, g.totalMXN, g.retIVA, g.retISR, g.iva, ps.pagoStatus, 0 AS totalProveedor, v.banco, v.clabe, v.aba, v.swift, 
									v.proveedorId, CONCAT('".PATH_PROJECTS."', p.uniqId, '/facturas/') AS pathFacturas 
							FROM 	".TABLE_POS." g, ".TABLE_PROJECTS." p, ".TABLE_VENDORS." v, ".TABLE_SAT_FORMA_PAGO." pf, ".TABLE_PAYMENTS_STATUS." ps
							WHERE 	g.proyectoId = p.proyectoId AND g.proveedorId = v.proveedorId AND g.pagoFormaId = pf.pagoFormaId AND 
									g.pagoStatusId = ps.pagoStatusId AND p.companyId = ".session_get_data("companyId")." 
									$sql_project 
	                                $sql_date 
    	                            $sql_status 
							ORDER BY $sort");

	# totals
	$totals = array();
	if($results) {
		$totals = array("", "", "", "", "", "", "Total", 0, 0, 0, 0, 0, "");
		$monto_total = 0;
		$retiva_total = 0;
		$retisr_total = 0;
		$iva_total = 0;
		$total_mxn = 0;
		foreach($results as $pos) {
			$monto_total += $pos['monto'];
			$retiva_total += $pos['retIVA'];
			$retisr_total += $pos['retISR'];
			$iva_total += $pos['iva'];
			$total_mxn += $pos['totalMXN'];
		}
		$totals[7] = $monto_total;
		$totals[8] = $retiva_total;
		$totals[9] = $retisr_total;
		$totals[10] = $iva_total;
		$totals[11] = $total_mxn;
	}

	return array($header, $results, $totals);

}

function get_excel_pos($header, $results) {

	#var_dump($header, $results);

	# Create PHPExcel object
	$objPHPExcel = new PHPExcel();

	# vars
	global $styles;
    $currentRow = 2;
    
	# Set document properties
	$objPHPExcel->getProperties()->setCreator(session_get_data("name"))
								->setLastModifiedBy(session_get_data("name"))
								->setTitle("Cuentas por Pagar");

	# Title
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, 'Cuentas por Pagar');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$currentRow.':Q'.$currentRow);
	$objPHPExcel->getActiveSheet()->getStyle('A'.($currentRow).':Q'.$currentRow)->applyFromArray( get_style("title") );

	$currentRow += 2;

	# Data header
	$tableStartRow = $currentRow;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, "Fecha");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$currentRow, "Proyecto");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$currentRow, "Concepto");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$currentRow, "Proveedor");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$currentRow, "Factura");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$currentRow, "Forma de Pago");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$currentRow, "Monto");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$currentRow, "Moneda");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$currentRow, "IVA");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$currentRow, "IVA Ret");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$currentRow, "ISR Ret");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$currentRow, "Total MXN");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$currentRow, "Estatus");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$currentRow, "Banco");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$currentRow, "CLABE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$currentRow, "ABA");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.$currentRow, "SWIFT");

	# Style header
	$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':Q'.$currentRow)->applyFromArray( get_style("header") );

	# Set columns widths
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
    
    $currentRow++;
    
    # Data
    if($results) {

        $dataStartRow = $currentRow;

		$vendor_id = $results[0]['proveedorId'];
		$vendor_row_start = $currentRow;
		$row_bg = 1;

		for($i=0; $i<count($results); $i++, $currentRow++) {

			$new_vendor_id = $results[$i]['proveedorId'];

			# fecha
			if(is_null($results[$i]['fechaDePago'])) {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, "-");
			} else {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, PHPExcel_Shared_Date::PHPToExcel($results[$i]['fechaDePago']));
			}
			$objPHPExcel->setActiveSheetIndex(0)->getStyleByColumnAndRow(0, $currentRow)->getNumberFormat()->setFormatCode("dd/mm/yyy");
		
			# info
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$currentRow, $results[$i]['clave']." - ".$results[$i]['titulo']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$currentRow, $results[$i]['concepto']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$currentRow, $results[$i]['razonSocial']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$currentRow, $results[$i]['facturaUuid']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$currentRow, $results[$i]['pagoForma']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$currentRow, $results[$i]['monto']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$currentRow, $results[$i]['moneda']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$currentRow, $results[$i]['iva']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$currentRow, $results[$i]['retIVA']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$currentRow, $results[$i]['retISR']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$currentRow, $results[$i]['totalMXN']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$currentRow, $results[$i]['pagoStatus']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$currentRow, $results[$i]['banco']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$currentRow, $results[$i]['clabe']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$currentRow, $results[$i]['aba']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.$currentRow, $results[$i]['swift']);

			if($new_vendor_id!=$vendor_id) {
				# group and sum total
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('N'.$vendor_row_start.':N'.($currentRow-1));

				# group banco, clabe, aba, swift
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('N'.$vendor_row_start.':N'.($currentRow-1));
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('O'.$vendor_row_start.':O'.($currentRow-1));
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('P'.$vendor_row_start.':P'.($currentRow-1));
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('Q'.$vendor_row_start.':Q'.($currentRow-1));

				# apply style to previous row's cells
				$objPHPExcel->getActiveSheet()->getStyle('A'.$vendor_row_start.':Q'.($currentRow-1))->applyFromArray( get_style("row".($row_bg%2)) );

				# change vendor
				$vendor_id = $results[$i]['proveedorId'];
				$vendor_row_start = $currentRow;
				$row_bg++;
			}

			if($results[$i]['prontoPago']==1) {
				# apply style to prontos pagos
				$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':Q'.$currentRow)->applyFromArray( get_style("ppago") );
			}

		}

		# last row
		# group and sum total
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('N'.$vendor_row_start.':N'.($currentRow-1));

		# group banco, clabe, aba, swift
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('N'.$vendor_row_start.':N'.($currentRow-1));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('O'.$vendor_row_start.':O'.($currentRow-1));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('P'.$vendor_row_start.':P'.($currentRow-1));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('Q'.$vendor_row_start.':Q'.($currentRow-1));

		# apply style to previous row's cells
		$objPHPExcel->getActiveSheet()->getStyle('A'.$vendor_row_start.':Q'.($currentRow-1))->applyFromArray( get_style("row".($row_bg%2)) );

		# Set total row
		$objPHPExcel->getActiveSheet()->setCellValue("F".$currentRow, "Totales");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$currentRow, "=sum(G".$dataStartRow.":G".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("I".$currentRow, "=sum(I".$dataStartRow.":I".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("J".$currentRow, "=sum(J".$dataStartRow.":J".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("K".$currentRow, "=sum(K".$dataStartRow.":K".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("L".$currentRow, "=sum(L".$dataStartRow.":L".($currentRow-1).")");
    
        # Set formats
		$objPHPExcel->getActiveSheet()->getStyle("G".$dataStartRow.":G".$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle("I".$dataStartRow.":L".$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':Q'.$currentRow)->applyFromArray( get_style("footer") );
		$objPHPExcel->getActiveSheet()->getStyle('A'.$tableStartRow.':Q'.$currentRow)->applyFromArray( get_style("bordersOutline") );

    } else {
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$currentRow, "No hubo movimientos en el periodo seleccionado.");
    }

    # Set headers to send file
    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Cuentas por Pagar.xlsx"');
    
    # Save & Send Excel 2007
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

}


# REP_COMP - RepComp - Cuentas por Pagar sin complemento
function get_report_comp($filters) {

    # filters
	$header = array("Proyecto", "Proveedor", "Concepto", "Feecha de Pago", "Forma de Pago", "Total MXN", "Estatus");

	if($filters['projectId']>0) {
		$sql_project = "AND g.proyectoId = ".$filters['projectId'];
	} else {
		$sql_project = "";
	}

    if(strtotime($filters['dateFrom'])==false || strtotime($filters['dateTo'])==false) {
        $sql_date = "";
    } else {
        $sql_date = "AND g.fechaDePago BETWEEN '".$filters['dateFrom']."' AND '".$filters['dateTo']."'";
    }

    # query
	$results = sql_select(" SELECT	CONCAT(p.clave, ' - ', p.titulo) AS proyecto, v.razonSocial, g.concepto, g.fechaDePago, fp.pagoForma, g.totalMXN, ps.pagoStatus
							FROM ".TABLE_POS." g, ".TABLE_VENDORS." v, ".TABLE_PROJECTS." p, ".TABLE_SAT_FORMA_PAGO." fp, ".TABLE_PAYMENTS_STATUS." ps
							WHERE g.proveedorId = v.proveedorId AND g.proyectoId = p.proyectoId AND g.pagoFormaId = fp.pagoFormaId AND 
								g.pagoStatusId = ps.pagoStatusId AND g.pagoStatusId = 3 AND g.facturaUuid <> '' AND g.comprobante = ''
								$sql_project 
								$sql_date 
							ORDER BY p.clave ASC, p.titulo ASC, g.fechaDePago ASC");

	# totals
	$totals = false;

	# return
	return array($header, $results, $totals);

}

function get_excel_comp($header, $results) {

	#var_dump($header, $results);

	# Create PHPExcel object
	$objPHPExcel = new PHPExcel();

	# vars
	global $styles;
    $currentRow = 2;
    
	# Set document properties
	$objPHPExcel->getProperties()->setCreator(session_get_data("name"))
								->setLastModifiedBy(session_get_data("name"))
								->setTitle("Cuentas por Pagar");

	# Title
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, 'Cuentas por Pagar');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$currentRow.':Q'.$currentRow);
	$objPHPExcel->getActiveSheet()->getStyle('A'.($currentRow).':Q'.$currentRow)->applyFromArray( get_style("title") );

	$currentRow += 2;

	# Data header
	$tableStartRow = $currentRow;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, "Fecha");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$currentRow, "Proyecto");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$currentRow, "Concepto");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$currentRow, "Proveedor");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$currentRow, "Factura");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$currentRow, "Forma de Pago");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$currentRow, "Monto");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$currentRow, "Moneda");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$currentRow, "IVA");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$currentRow, "IVA Ret");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$currentRow, "ISR Ret");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$currentRow, "Total MXN");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$currentRow, "Estatus");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$currentRow, "Banco");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$currentRow, "CLABE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$currentRow, "ABA");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.$currentRow, "SWIFT");

	# Style header
	$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':Q'.$currentRow)->applyFromArray( get_style("header") );

	# Set columns widths
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
    
    $currentRow++;
    
    # Data
    if($results) {

        $dataStartRow = $currentRow;

		$vendor_id = $results[0]['proveedorId'];
		$vendor_row_start = $currentRow;
		$row_bg = 1;

		for($i=0; $i<count($results); $i++, $currentRow++) {

			$new_vendor_id = $results[$i]['proveedorId'];

			# fecha
			if(is_null($results[$i]['fechaDePago'])) {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, "-");
			} else {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, PHPExcel_Shared_Date::PHPToExcel($results[$i]['fechaDePago']));
			}
			$objPHPExcel->setActiveSheetIndex(0)->getStyleByColumnAndRow(0, $currentRow)->getNumberFormat()->setFormatCode("dd/mm/yyy");
		
			# info
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$currentRow, $results[$i]['clave']." - ".$results[$i]['titulo']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$currentRow, $results[$i]['concepto']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$currentRow, $results[$i]['razonSocial']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$currentRow, $results[$i]['facturaUuid']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$currentRow, $results[$i]['pagoForma']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$currentRow, $results[$i]['monto']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$currentRow, $results[$i]['moneda']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$currentRow, $results[$i]['iva']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$currentRow, $results[$i]['retIVA']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$currentRow, $results[$i]['retISR']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$currentRow, $results[$i]['totalMXN']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$currentRow, $results[$i]['pagoStatus']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$currentRow, $results[$i]['banco']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$currentRow, $results[$i]['clabe']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$currentRow, $results[$i]['aba']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.$currentRow, $results[$i]['swift']);

			if($new_vendor_id!=$vendor_id) {
				# group and sum total
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('N'.$vendor_row_start.':N'.($currentRow-1));

				# group banco, clabe, aba, swift
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('N'.$vendor_row_start.':N'.($currentRow-1));
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('O'.$vendor_row_start.':O'.($currentRow-1));
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('P'.$vendor_row_start.':P'.($currentRow-1));
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('Q'.$vendor_row_start.':Q'.($currentRow-1));

				# apply style to previous row's cells
				$objPHPExcel->getActiveSheet()->getStyle('A'.$vendor_row_start.':Q'.($currentRow-1))->applyFromArray( get_style("row".($row_bg%2)) );

				# change vendor
				$vendor_id = $results[$i]['proveedorId'];
				$vendor_row_start = $currentRow;
				$row_bg++;
			}

			if($results[$i]['prontoPago']==1) {
				# apply style to prontos pagos
				$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':Q'.$currentRow)->applyFromArray( get_style("ppago") );
			}

		}

		# last row
		# group and sum total
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('N'.$vendor_row_start.':N'.($currentRow-1));

		# group banco, clabe, aba, swift
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('N'.$vendor_row_start.':N'.($currentRow-1));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('O'.$vendor_row_start.':O'.($currentRow-1));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('P'.$vendor_row_start.':P'.($currentRow-1));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('Q'.$vendor_row_start.':Q'.($currentRow-1));

		# apply style to previous row's cells
		$objPHPExcel->getActiveSheet()->getStyle('A'.$vendor_row_start.':Q'.($currentRow-1))->applyFromArray( get_style("row".($row_bg%2)) );

		# Set total row
		$objPHPExcel->getActiveSheet()->setCellValue("F".$currentRow, "Totales");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$currentRow, "=sum(G".$dataStartRow.":G".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("I".$currentRow, "=sum(I".$dataStartRow.":I".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("J".$currentRow, "=sum(J".$dataStartRow.":J".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("K".$currentRow, "=sum(K".$dataStartRow.":K".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("L".$currentRow, "=sum(L".$dataStartRow.":L".($currentRow-1).")");
    
        # Set formats
		$objPHPExcel->getActiveSheet()->getStyle("G".$dataStartRow.":G".$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle("I".$dataStartRow.":L".$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':Q'.$currentRow)->applyFromArray( get_style("footer") );
		$objPHPExcel->getActiveSheet()->getStyle('A'.$tableStartRow.':Q'.$currentRow)->applyFromArray( get_style("bordersOutline") );

    } else {
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$currentRow, "No hubo movimientos en el periodo seleccionado.");
    }

    # Set headers to send file
    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Cuentas por Pagar.xlsx"');
    
    # Save & Send Excel 2007
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

}


# REP_CONCEPT - RepConcept - Gastos por Concepto
function get_report_concept($filters) {

    # filters
	$header = array("Concepto", "IVA Ret", "ISR Ret", "IVA", "Total");

	if($filters['projectId']>0) {
		$sql_project = "AND g.proyectoId = ".$filters['projectId'];
	} else {
		$sql_project = "";
	}

    if(strtotime($filters['dateFrom'])==false || strtotime($filters['dateTo'])==false) {
        $sql_date = "";
    } else {
        $sql_date = "AND g.fechaDePago BETWEEN '".$filters['dateFrom']."' AND '".$filters['dateTo']."'";
    }

    if($filters['concepto']!="") {
        $sql_concepto = "AND g.concepto LIKE '%".$filters['concepto']."%'";
    } else {
        $sql_concepto = "";
    }

    # query
    $results = sql_select(" SELECT 	g.concepto, SUM(g.retIVA) AS retIVA, SUM(g.retISR) AS retISR, SUM(g.iva) AS iva, SUM(g.totalMXN) AS totalMXN
							FROM 	".TABLE_POS." g, ".TABLE_PROJECTS." p 
							WHERE 	g.proyectoId = p.proyectoId AND p.companyId = ".session_get_data("companyId")." 
									$sql_project 
	                                $sql_date 
    	                            $sql_concepto 
							GROUP BY g.concepto 
							ORDER BY concepto ASC");

	# totals
	$totals = array();
	if($results) {
		$totals = array("Total", 0, 0, 0, 0);
		$monto_total = 0;
		$retiva_total = 0;
		$retisr_total = 0;
		$iva_total = 0;
		foreach($results as $pos) {
			$monto_total += $pos['totalMXN'];
			$retiva_total += $pos['retIVA'];
			$retisr_total += $pos['retISR'];
			$iva_total += $pos['iva'];
		}
		$totals[1] = $monto_total;
		$totals[2] = $retiva_total;
		$totals[3] = $retisr_total;
		$totals[4] = $iva_total;
	}

	return array($header, $results, $totals);

}

function get_excel_concept($header, $results) {

	#var_dump($header, $results);

	# Create PHPExcel object
	$objPHPExcel = new PHPExcel();

	# vars
	global $styles;
    $currentRow = 2;
    
	# Set document properties
	$objPHPExcel->getProperties()->setCreator(session_get_data("name"))
								->setLastModifiedBy(session_get_data("name"))
								->setTitle("Gastos por Concepto");

	# Title
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, 'Gastos por Concepto');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$currentRow.':E'.$currentRow);
	$objPHPExcel->getActiveSheet()->getStyle('A'.($currentRow).':E'.$currentRow)->applyFromArray( get_style("title") );

	$currentRow += 2;

	# Data header
	$tableStartRow = $currentRow;
	for($col=0; $col<count($header); $col++) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $currentRow, $header[$col]);
	}

	$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':E'.$currentRow)->applyFromArray( get_style("header") );

	# Set columns widths
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    
    $currentRow++;
    
    # Data
    if($results) {

        $dataStartRow = $currentRow;
		$row_bg = 1;

		for($i=0; $i<count($results); $i++, $currentRow++) {

			# info
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, $results[$i]['concepto']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$currentRow, $results[$i]['retIVA']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$currentRow, $results[$i]['retISR']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$currentRow, $results[$i]['iva']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$currentRow, $results[$i]['totalMXN']);

		}

		# last row

		# apply style to previous row's cells
		#$objPHPExcel->getActiveSheet()->getStyle('A'.$vendor_row_start.':Q'.($currentRow-1))->applyFromArray( get_style("row".($row_bg%2)) );

		# Set total row
		$objPHPExcel->getActiveSheet()->setCellValue("A".$currentRow, "Totales");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$currentRow, "=sum(B".$dataStartRow.":B".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$currentRow, "=sum(C".$dataStartRow.":C".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$currentRow, "=sum(D".$dataStartRow.":D".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$currentRow, "=sum(E".$dataStartRow.":E".($currentRow-1).")");
    
        # Set formats
		$objPHPExcel->getActiveSheet()->getStyle("B".$dataStartRow.":E".$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':E'.$currentRow)->applyFromArray( get_style("footer") );
		$objPHPExcel->getActiveSheet()->getStyle('A'.$tableStartRow.':E'.$currentRow)->applyFromArray( get_style("bordersOutline") );

    } else {
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$currentRow, "No hubo movimientos en el periodo seleccionado.");
    }

    # Set headers to send file
    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Gastos por Concepto.xlsx"');
    
    # Save & Send Excel 2007
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

}


# REP_PROY - RepProy
function get_report_proyectos($filters) {

	# filters
	$header = array("Proyecto", "Cliente", "Director", "Productor", "Productor en línea", "Monto");

	if($filters['projectId']>0) {
		$sql_project = "AND p.proyectoId = ".$filters['projectId'];
	} else {
		$sql_project = "";
	}

	# query
	$results = sql_select(" SELECT 	CONCAT(p.clave, ' - ', p.titulo) AS proyecto, p.cliente, p.director, p.productor, p.productorLinea, 
									SUM(po.totalMXN) AS monto
							FROM 	".TABLE_PROJECTS." p LEFT JOIN ".TABLE_POS." po ON p.proyectoId = po.proyectoId 
							WHERE 	p.activo = 1 AND p.companyId = ".session_get_data("companyId")." 
									$sql_project 
							GROUP BY po.proyectoId
							ORDER BY proyecto ASC");

	# totals
	$totals = array();
	if($results) {
		$totals = array("", "", "", "", "Total", 0);
		$monto_total = 0;
		foreach($results as $pos) {
			$monto_total += $pos['monto'];
		}
		$totals[5] = $monto_total;
	}

	return array($header, $results, $totals);

}

function get_excel_proyectos($header, $results) {

	# Create PHPExcel object
	$objPHPExcel = new PHPExcel();

	# vars
	global $styles;
    $currentRow = 2;
    
	# Set document properties
	$objPHPExcel->getProperties()->setCreator(session_get_data("name"))
								->setLastModifiedBy(session_get_data("name"))
								->setTitle("Proyectos");

	# Title
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, 'Proyectos');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$currentRow.':F'.$currentRow);
	$objPHPExcel->getActiveSheet()->getStyle('A'.($currentRow).':F'.$currentRow)->applyFromArray( get_style("title") );

	$currentRow += 2;

	# Data header
	$tableStartRow = $currentRow;
	for($col=0; $col<count($header); $col++) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $currentRow, $header[$col]);
	}

	$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':F'.$currentRow)->applyFromArray( get_style("header") );

	# Set columns widths
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    
    $currentRow++;
    
    # Data
    if($results) {

        $dataStartRow = $currentRow;

        for($i=0; $i<count($results); $i++, $currentRow++) {
			$col = 0;
			foreach($results[$i] as $value) {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $currentRow, $value);
				$col++;
			}
		}

		# Set total row
		$objPHPExcel->getActiveSheet()->setCellValue("E".$currentRow, "Total");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$currentRow, "=sum(F".$dataStartRow.":F".($currentRow-1).")");
    
        # Set formats
		$objPHPExcel->getActiveSheet()->getStyle("F".$dataStartRow.":F".$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':F'.$currentRow)->applyFromArray( get_style("footer") );
		$objPHPExcel->getActiveSheet()->getStyle('A'.$tableStartRow.':F'.$currentRow)->applyFromArray( get_style("bordersOutline") );

    } else {
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$currentRow, "No hubo movimientos en el periodo seleccionado.");
    }

    # Set headers to send file
    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Proyectos.xlsx"');
    
    # Save & Send Excel 2007
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

}


# REP_PROVS - RepProvs
function get_report_proveedores($filters) {

	# filters
	$header = array("Proveedor", "Proyecto", "Pendiente", "Autorizado", "Pagado", "Total");

	if($filters['projectId']>0) {
		$sql_project = "AND p.proyectoId = ".$filters['projectId'];
	} else {
		$sql_project = "";
	}

	if($filters['proveedorId']>0) {
		$sql_vendor = "AND gt.proveedorId = ".$filters['proveedorId'];
	} else {
		$sql_vendor = "";
	}

	# query
	$results = sql_select(" SELECT v.razonSocial, CONCAT(p.clave, ' - ', p.titulo) AS proyecto, IFNULL(gp.montoPend, 0) AS montoPend, 
								IFNULL(ga.montoAuth, 0) AS montoAuth, IFNULL(gd.montoPayed, 0) AS montoPayed, SUM(gt.totalMXN) AS montoTotal
							FROM ".TABLE_VENDORS." v, ".TABLE_PROJECTS." p, ".TABLE_POS." gt 
								LEFT JOIN (SELECT proveedorId, proyectoId, SUM(totalMXN) AS montoPend FROM ".TABLE_POS." WHERE pagoStatusId = 1 GROUP BY proveedorId, proyectoId) gp ON gt.proveedorId = gp.proveedorId AND gt.proyectoId = gp.proyectoId 
								LEFT JOIN (SELECT proveedorId, proyectoId, SUM(totalMXN) AS montoAuth FROM ".TABLE_POS." WHERE pagoStatusId = 2 GROUP BY proveedorId, proyectoId) ga ON gt.proveedorId = ga.proveedorId AND gt.proyectoId = ga.proyectoId 
								LEFT JOIN (SELECT proveedorId, proyectoId, SUM(totalMXN) AS montoPayed FROM ".TABLE_POS." WHERE pagoStatusId = 3 GROUP BY proveedorId, proyectoId) gd ON gt.proveedorId = gd.proveedorId AND gt.proyectoId = gd.proyectoId 
							WHERE v.proveedorId = gt.proveedorId AND p.proyectoId = gt.proyectoId AND p.activo = 1 AND p.companyId = ".session_get_data("companyId")." 
									$sql_project $sql_vendor 
							GROUP BY gt.proveedorId, gt.proyectoId 
							ORDER BY v.razonSocial ASC, proyecto ASC");

	# totals
	$totals = array();
	if($results) {
		$totals = array("", "Total", 0, 0, 0, 0);
		$total_pend = 0;
		$total_auth = 0;
		$total_payed = 0;
		$total_total = 0;
		foreach($results as $pos) {
			$total_pend += $pos['montoPend'];
			$total_auth += $pos['montoAuth'];
			$total_payed += $pos['montoPayed'];
			$total_total += $pos['montoTotal'];
		}
		$totals[2] = $total_pend;
		$totals[3] = $total_auth;
		$totals[4] = $total_payed;
		$totals[5] = $total_total;
	}

	return array($header, $results, $totals);

}

function get_excel_proveedores($header, $results) {

	# Create PHPExcel object
	$objPHPExcel = new PHPExcel();

	# vars
	global $styles;
    $currentRow = 2;
    
	# Set document properties
	$objPHPExcel->getProperties()->setCreator(session_get_data("name"))
								->setLastModifiedBy(session_get_data("name"))
								->setTitle("Proveedores");

	# Title
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, 'Proveedores');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$currentRow.':F'.$currentRow);
	$objPHPExcel->getActiveSheet()->getStyle('A'.($currentRow).':F'.$currentRow)->applyFromArray( get_style("title") );

	$currentRow += 2;

	# Data header
	$tableStartRow = $currentRow;
	for($col=0; $col<count($header); $col++) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $currentRow, $header[$col]);
	}

	$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':F'.$currentRow)->applyFromArray( get_style("header") );

	# Set columns widths
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    
    $currentRow++;
    
    # Data
    if($results) {

        $dataStartRow = $currentRow;

        for($i=0; $i<count($results); $i++, $currentRow++) {
			$col = 0;
			foreach($results[$i] as $value) {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $currentRow, $value);
				$col++;
			}
		}

		# Set total row
		$objPHPExcel->getActiveSheet()->setCellValue("B".$currentRow, "Total");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$currentRow, "=sum(C".$dataStartRow.":C".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$currentRow, "=sum(D".$dataStartRow.":D".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$currentRow, "=sum(E".$dataStartRow.":E".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$currentRow, "=sum(F".$dataStartRow.":F".($currentRow-1).")");
    
        # Set formats
		$objPHPExcel->getActiveSheet()->getStyle("C".$dataStartRow.":F".$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':F'.$currentRow)->applyFromArray( get_style("footer") );
		$objPHPExcel->getActiveSheet()->getStyle('A'.$tableStartRow.':F'.$currentRow)->applyFromArray( get_style("bordersOutline") );

    } else {
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$currentRow, "No hubo movimientos en el periodo seleccionado.");
    }

    # Set headers to send file
    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Proveedores.xlsx"');
    
    # Save & Send Excel 2007
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

}


# REP_FLUJO - Flujo de Efectivo
function get_report_flujo($filters) {

	# weeks
	$today = date("Y-m-d");
	$weekend = date("Y-m-d", strtotime($today. " + 6 days"));
	$weeks = array();
	for($i=0; $i<4; $i++) {
		$weeks[$i]['col'] = "sem".$i;
		$weeks[$i]['start_date'] = date("Y-m-d", strtotime($today. " + $i weeks"));
		$weeks[$i]['end_date'] = date("Y-m-d", strtotime($weekend. " + $i weeks"));
		$weeks[$i]['text'] = date("M-d", strtotime($weeks[$i]['start_date']))." a ".date("M-d", strtotime($weeks[$i]['end_date']));
	}

	# filters
	$header = array("Proyecto", $weeks[0]["text"], $weeks[1]["text"], $weeks[2]["text"], $weeks[3]["text"], "Total");

	if($filters['projectId']>0) {
		$sql_project = " AND proyectoId = ".$filters['projectId'];
	} else {
		$sql_project = "";
	}

	# projects
	$results = sql_select("	SELECT proyectoId, CONCAT(clave, ' - ', titulo) AS proyecto 
							FROM ".TABLE_PROJECTS." 
							WHERE activo = 1 AND companyId = ".session_get_data("companyId")." $sql_project");

	# amounts per week
	if($results) {
		for($i=0; $i<count($results); $i++) {
			$total = 0;
			for($j=0; $j<4; $j++) {
				$pos = sql_select_row("SELECT SUM(totalMXN) AS total 
											FROM ".TABLE_POS." 
											WHERE proyectoId = ".$results[$i]['proyectoId']." AND pagoStatusId <> ".PAYMENT_STATUS_PAYED." AND 
												fechaDePago BETWEEN '".$weeks[$j]['start_date']."' AND '".$weeks[$j]['end_date']."' 
											GROUP BY proyectoId");
				$results[$i][$weeks[$j]['col']] = ($pos) ? (float)$pos['total'] : 0;
				$total += $results[$i][$weeks[$j]['col']];
			}
			unset($results[$i]['proyectoId']);
			$results[$i]['total'] = $total;
		}
	}

	# totals
	$totals = array();
	if($results) {
		$totals = array("Total", 0, 0, 0, 0, 0);
		foreach($results as $pos) {
			$totals[1] += $pos['sem0'];
			$totals[2] += $pos['sem1'];
			$totals[3] += $pos['sem2'];
			$totals[4] += $pos['sem3'];
			$totals[5] += $pos['total'];
		}
	}

	return array($header, $results, $totals);

}

function get_excel_flujo($header, $results) {

	# Create PHPExcel object
	$objPHPExcel = new PHPExcel();

	# vars
	global $styles;
    $currentRow = 2;
    
	# Set document properties
	$objPHPExcel->getProperties()->setCreator(session_get_data("name"))
								->setLastModifiedBy(session_get_data("name"))
								->setTitle("Flujo de Efectivo");

	# Title
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, 'Flujo de Efectivo');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$currentRow.':F'.$currentRow);
	$objPHPExcel->getActiveSheet()->getStyle('A'.($currentRow).':F'.$currentRow)->applyFromArray( get_style("title") );

	$currentRow += 2;

	# Data header
	$tableStartRow = $currentRow;
	for($col=0; $col<count($header); $col++) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $currentRow, $header[$col]);
	}

	$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':F'.$currentRow)->applyFromArray( get_style("header") );

	# Set columns widths
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    
    $currentRow++;
    
    # Data
    if($results) {

        $dataStartRow = $currentRow;

        for($i=0; $i<count($results); $i++, $currentRow++) {
			$col = 0;
			foreach($results[$i] as $value) {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $currentRow, $value);
				$col++;
			}
		}

		# Set total row
		$objPHPExcel->getActiveSheet()->setCellValue("A".$currentRow, "Total");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$currentRow, "=sum(B".$dataStartRow.":B".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$currentRow, "=sum(C".$dataStartRow.":C".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$currentRow, "=sum(D".$dataStartRow.":D".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$currentRow, "=sum(E".$dataStartRow.":E".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$currentRow, "=sum(F".$dataStartRow.":F".($currentRow-1).")");
    
        # Set formats
		$objPHPExcel->getActiveSheet()->getStyle("B".$dataStartRow.":F".$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':F'.$currentRow)->applyFromArray( get_style("footer") );
		$objPHPExcel->getActiveSheet()->getStyle('A'.$tableStartRow.':F'.$currentRow)->applyFromArray( get_style("bordersOutline") );

    } else {
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$currentRow, "No hubo movimientos en el periodo seleccionado.");
    }

    # Set headers to send file
    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Flujo de Efectivo.xlsx"');
    
    # Save & Send Excel 2007
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

}


# REP_DIR - RepDir
function get_report_directores($filters) {

	# filters
	$header = array("Proyecto", "Director", "Pendiente", "Autorizado", "Pagado", "Total");

	if($filters['projectId']>0) {
		$sql_project = "AND p.proyectoId = ".$filters['projectId'];
	} else {
		$sql_project = "";
	}

	if($filters['directorId']>0) {
		$sql_vendor = "AND gt.proveedorId = ".$filters['directorId'];
	} else {
		$sql_vendor = "";
	}

	# query
	$results = sql_select(" SELECT CONCAT(p.clave, ' - ', p.titulo) AS proyecto, v.razonSocial, IFNULL(gp.montoPend, 0) AS montoPend, 
								IFNULL(ga.montoAuth, 0) AS montoAuth, IFNULL(gd.montoPayed, 0) AS montoPayed, SUM(gt.totalMXN) AS montoTotal
							FROM ".TABLE_VENDORS." v, ".TABLE_PROJECTS." p, ".TABLE_POS." gt 
								LEFT JOIN (SELECT proveedorId, proyectoId, SUM(totalMXN) AS montoPend FROM ".TABLE_POS." WHERE pagoStatusId = 1 GROUP BY proveedorId, proyectoId) gp ON gt.proveedorId = gp.proveedorId AND gt.proyectoId = gp.proyectoId 
								LEFT JOIN (SELECT proveedorId, proyectoId, SUM(totalMXN) AS montoAuth FROM ".TABLE_POS." WHERE pagoStatusId = 2 GROUP BY proveedorId, proyectoId) ga ON gt.proveedorId = ga.proveedorId AND gt.proyectoId = ga.proyectoId 
								LEFT JOIN (SELECT proveedorId, proyectoId, SUM(totalMXN) AS montoPayed FROM ".TABLE_POS." WHERE pagoStatusId = 3 GROUP BY proveedorId, proyectoId) gd ON gt.proveedorId = gd.proveedorId AND gt.proyectoId = gd.proyectoId 
							WHERE v.proveedorId = gt.proveedorId AND p.proyectoId = gt.proyectoId AND p.activo = 1 AND p.companyId = ".session_get_data("companyId")." AND v.director = 1 
									$sql_project $sql_vendor 
							GROUP BY gt.proveedorId, gt.proyectoId 
							ORDER BY v.razonSocial ASC, proyecto ASC");

	# totals
	$totals = array();
	if($results) {
		$totals = array("", "Total", 0, 0, 0, 0);
		$total_pend = 0;
		$total_auth = 0;
		$total_payed = 0;
		$total_total = 0;
		foreach($results as $pos) {
			$total_pend += $pos['montoPend'];
			$total_auth += $pos['montoAuth'];
			$total_payed += $pos['montoPayed'];
			$total_total += $pos['montoTotal'];
		}
		$totals[2] = $total_pend;
		$totals[3] = $total_auth;
		$totals[4] = $total_payed;
		$totals[5] = $total_total;
	}

	return array($header, $results, $totals);

}

function get_excel_directores($header, $results) {

	# Create PHPExcel object
	$objPHPExcel = new PHPExcel();

	# vars
	global $styles;
    $currentRow = 2;
    
	# Set document properties
	$objPHPExcel->getProperties()->setCreator(session_get_data("name"))
								->setLastModifiedBy(session_get_data("name"))
								->setTitle("Directores");

	# Title
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$currentRow, 'Directores');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$currentRow.':F'.$currentRow);
	$objPHPExcel->getActiveSheet()->getStyle('A'.($currentRow).':F'.$currentRow)->applyFromArray( get_style("title") );

	$currentRow += 2;

	# Data header
	$tableStartRow = $currentRow;
	for($col=0; $col<count($header); $col++) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $currentRow, $header[$col]);
	}

	$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':F'.$currentRow)->applyFromArray( get_style("header") );

	# Set columns widths
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    
    $currentRow++;
    
    # Data
    if($results) {

        $dataStartRow = $currentRow;

        for($i=0; $i<count($results); $i++, $currentRow++) {
			$col = 0;
			foreach($results[$i] as $value) {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $currentRow, $value);
				$col++;
			}
		}

		# Set total row
		$objPHPExcel->getActiveSheet()->setCellValue("B".$currentRow, "Total");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$currentRow, "=sum(C".$dataStartRow.":C".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$currentRow, "=sum(D".$dataStartRow.":D".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$currentRow, "=sum(E".$dataStartRow.":E".($currentRow-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$currentRow, "=sum(F".$dataStartRow.":F".($currentRow-1).")");
    
        # Set formats
		$objPHPExcel->getActiveSheet()->getStyle("C".$dataStartRow.":F".$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':F'.$currentRow)->applyFromArray( get_style("footer") );
		$objPHPExcel->getActiveSheet()->getStyle('A'.$tableStartRow.':F'.$currentRow)->applyFromArray( get_style("bordersOutline") );

    } else {
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$currentRow, "No hubo movimientos en el periodo seleccionado.");
    }

    # Set headers to send file
    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Directores.xlsx"');
    
    # Save & Send Excel 2007
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

}


?>