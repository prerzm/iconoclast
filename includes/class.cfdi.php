<?php

/** RZM PHP Framework **/

function xml_get_attribute($object, $attribute) {

	$attributes = $object->attributes();

	if( isset($attributes[$attribute]) ) {
		return (string)$attributes[$attribute];
	} else {
		return false;
	}

}

class CFDI {

	var $comprobante;
	var $elementos;

	function __construct($xml_file) {

		$this->comprobante = simplexml_load_file($xml_file);
		$this->elementos = $this->comprobante->children('cfdi', true);
		#$this->impuestos = $this->elementos->Impuestos->children('cfdi', true);

	}

	function get_uuid() {

		$complemento = $this->elementos->Complemento;
		$timbrado = $complemento->children('tfd', true);
		return xml_get_attribute($timbrado, 'UUID');

	}

	function get_cfdi_info() {

		# info comprobante
		$invoice['UUID'] = $this->get_uuid();
		$invoice['Serie'] = xml_get_attribute($this->comprobante, 'Serie');
		$invoice['Folio'] = xml_get_attribute($this->comprobante, 'Folio');
		$invoice['Fecha'] = xml_get_attribute($this->comprobante, 'Fecha');
		$invoice['MetodoPago'] = xml_get_attribute($this->comprobante, 'MetodoPago');
		$invoice['FormaPago'] = xml_get_attribute($this->comprobante, 'FormaPago');
		$invoice['TipoDeComprobante'] = xml_get_attribute($this->comprobante, 'TipoDeComprobante');

		# info emisor
		$emisor = $this->elementos->Emisor;
		$invoice['Emisor RFC'] = xml_get_attribute($emisor, 'Rfc');
		$invoice['Emisor'] = xml_get_attribute($emisor, 'Nombre');

		# info receptor
		$receptor = $this->elementos->Receptor;
		$invoice['Receptor RFC'] = xml_get_attribute($receptor, 'Rfc');
		$invoice['Receptor'] = xml_get_attribute($receptor, 'Nombre');
		$invoice['UsoCFDI'] = xml_get_attribute($receptor, 'UsoCFDI');

		# info montos
		$invoice['Moneda'] = xml_get_attribute($this->comprobante, 'Moneda');
		$invoice['Subtotal'] = xml_get_attribute($this->comprobante, 'SubTotal');
		$Descuento = xml_get_attribute($this->comprobante, 'Descuento');
		$invoice['Descuento'] = ($Descuento!=false) ? $Descuento : 0;
		$invoice['Total'] = xml_get_attribute($this->comprobante, 'Total');

		# info impuestos
		$impuestos = $this->elementos->Impuestos;
		$TotalImpuestosTrasladados = xml_get_attribute($impuestos, 'TotalImpuestosTrasladados');
		$invoice['Traslados'] = ($TotalImpuestosTrasladados!=false) ? $TotalImpuestosTrasladados : 0;

		$TotalImpuestosRetenidos = xml_get_attribute($impuestos, 'TotalImpuestosRetenidos');
		$invoice['Retenciones'] = ($TotalImpuestosRetenidos!=false) ? $TotalImpuestosRetenidos : 0;

		return $invoice;
		
	}

	function get_comprobante() {

		$comprobante['Serie'] = xml_get_attribute($this->comprobante, 'Serie');
		$comprobante['Folio'] = xml_get_attribute($this->comprobante, 'Folio');
		$comprobante['Sello'] = xml_get_attribute($this->comprobante, 'Sello');
		$comprobante['NoCertificado'] = xml_get_attribute($this->comprobante, 'NoCertificado');
		$comprobante['Fecha'] = xml_get_attribute($this->comprobante, 'Fecha');
		$comprobante['LugarExpedicion'] = xml_get_attribute($this->comprobante, 'LugarExpedicion');
		$comprobante['MetodoPago'] = query_select_single_value("metodoPago", TABLE_SAT_METODO_PAGO, "claveMetodoPago = '".xml_get_attribute($this->comprobante, 'MetodoPago')."'");
		$comprobante['FormaPago'] = query_select_single_value("formaPago", TABLE_SAT_FORMA_PAGO, "claveFormaPago = '".xml_get_attribute($this->comprobante, 'FormaPago')."'");
		$comprobante['Moneda'] = xml_get_attribute($this->comprobante, 'Moneda');
		$comprobante['SubTotal'] = xml_get_attribute($this->comprobante, 'SubTotal');

		$Descuento = xml_get_attribute($this->comprobante, 'Descuento');
		if($Descuento!=false) {
			$comprobante['Descuento'] = $Descuento;
		} else {
			$comprobante['Descuento'] = 0;
		}

		$comprobante['Total'] = xml_get_attribute($this->comprobante, 'Total');

		return $comprobante;

	}

	function get_emisor() {

		$emisor = $this->elementos->Emisor;
		$values['Rfc'] = xml_get_attribute($emisor, 'Rfc');
		$values['Nombre'] = xml_get_attribute($emisor, 'Nombre');
		$values['RegimenFiscal'] = query_select_single_value("regimen", TABLE_SAT_REGIMEN_FISCAL, "regimenId = ".xml_get_attribute($emisor, 'RegimenFiscal'));

		return $values;
		
	}

	function get_receptor() {

		$receptor = $this->elementos->Receptor;
		$values['Rfc'] = xml_get_attribute($receptor, 'Rfc');
		$values['Nombre'] = xml_get_attribute($receptor, 'Nombre');
		$values['UsoCFDI'] = query_select_single_value("uso", TABLE_SAT_USO_CFDI, "claveUso = '".xml_get_attribute($receptor, 'UsoCFDI')."'");

		return $values;

	}

	function get_conceptos() {

		$conceptos = $this->elementos->Conceptos->children('cfdi', true);

		foreach($conceptos as $concepto) {
			$conceptos_array[] = array( "Cantidad" => xml_get_attribute($concepto, 'Cantidad'),
										"ClaveProdServ" => xml_get_attribute($concepto, 'ClaveProdServ'), 
										"ClaveUnidad" => xml_get_attribute($concepto, 'ClaveUnidad'), 
										"Unidad" => xml_get_attribute($concepto, 'Unidad'), 
										"Descripcion" => xml_get_attribute($concepto, 'Descripcion'), 
										"ValorUnitario" => xml_get_attribute($concepto, 'ValorUnitario'), 
										"Importe" => xml_get_attribute($concepto, 'Importe') 
										);
		}
		return $conceptos_array;

	}

	function get_traslados() {

		$impuestos = $this->elementos->Impuestos;

		$TotalImpuestosTrasladados = xml_get_attribute($impuestos, 'TotalImpuestosTrasladados');
		if($TotalImpuestosTrasladados!=false) {
			$traslados = $impuestos->Traslados->children('cfdi', true);
			foreach($traslados as $traslado) {
				# query impuesto
				$claveImpuesto = xml_get_attribute($traslado, 'Impuesto');
				$nombreImpuesto = query_select_single_value("impuesto", TABLE_SAT_IMPUESTOS, "claveImpuesto = '$claveImpuesto'");
				$tasaImpuesto = xml_get_attribute($traslado, 'TasaOCuota');
				$traslados_array[] = array ( "claveImpuesto" => $claveImpuesto, "nombreImpuesto" => $nombreImpuesto, "TasaImpuesto" => $tasaImpuesto, "Importe" => xml_get_attribute($traslado, 'Importe') );
			}
			return $traslados_array;
		} else {
			return false;
		}

	}

	function get_retenciones() {

		$retenciones_array = array();
		$impuestos = $this->elementos->Impuestos;

		$TotalImpuestosRetenidos = xml_get_attribute($impuestos, 'TotalImpuestosRetenidos');
		if($TotalImpuestosRetenidos!=false) {
			$retenciones = $impuestos->Retenciones->children('cfdi', true);
			foreach($retenciones as $retencion) {
				# query impuesto
				$claveImpuesto = xml_get_attribute($retencion, 'Impuesto');
				$nombreImpuesto = query_select_single_value("impuesto", TABLE_SAT_IMPUESTOS, "claveImpuesto = '$claveImpuesto'");
				$tasaImpuesto = xml_get_attribute($retencion, 'TasaOCuota');
				$retenciones_array[] = array ( "claveImpuesto" => $claveImpuesto, "nombreImpuesto" => $nombreImpuesto, "TasaImpuesto" => $tasaImpuesto, "Importe" => xml_get_attribute($retencion, 'Importe') );
			}
			return $retenciones_array;
		} else {
			return false;
		}
	
	}

	function get_timbrado() {

		$timbrado = $this->elementos->Complemento->children('tfd', true);
		
		$timbre['Version'] = xml_get_attribute($timbrado, 'Version');
		$timbre['FechaTimbrado'] = xml_get_attribute($timbrado, 'FechaTimbrado');
		$timbre['UUID'] = xml_get_attribute($timbrado, 'UUID');
		$timbre['NoCertificadoSAT'] = xml_get_attribute($timbrado, 'NoCertificadoSAT');
		$timbre['SelloSAT'] = xml_get_attribute($timbrado, 'SelloSAT');
		$timbre['RfcProvCertif'] = xml_get_attribute($timbrado, 'RfcProvCertif');
		$timbre['SelloCFD'] = xml_get_attribute($timbrado, 'SelloCFD');

		return $timbre;

	}

}

?>