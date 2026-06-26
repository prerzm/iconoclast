<?php

class ContractsAdendas {

	protected $id;
	public $info;
    public $fields;
    protected $html;

    public function __construct($id) {

		$record = sql_select_row("SELECT cv.id, cv.parentId, cv.fechaCreado, cv.firmaStatusId, cv.firmaFecha, cv.contrato, cv.anexo, cv.carta, 
									cv.fieldsValues, cv.info, cv.firma, 
									c.contratoId, c.tipo, c.subtipo, c.nombre, c.contrato, 
									v.proveedorId, v.rfc, v.razonSocial, v.repseNumero, v.repseAviso, v.email, v.banco, v.cuenta, v.clabe, 
									p.proyectoId, p.clave, p.titulo, p.fechaInicio, p.fechaFin, p.lugar, CONCAT('".PATH_PROJECTS."', uniqId, '/contratos/') AS pathContratos, 
									co.companyId, co.firmaContratos, 
									cs.contratoStatus 
								FROM ".TABLE_CONTRACTS_VENDORS." cv, ".TABLE_CONTRACTS." c, ".TABLE_CONTRACTS_STATUS." cs, ".TABLE_VENDORS." v, ".TABLE_PROJECTS." p, ".TABLE_COMPANIES." co 
								WHERE cv.contratoId = c.contratoId AND cv.proveedorId = v.proveedorId AND cv.firmaStatusId = cs.contratoStatusId AND 
									cv.proyectoId = p.proyectoId AND cv.id = $id");
		
        if($record) {
            $this->id = $id;
			$this->info = $record;
            $this->info['raw'] = base64_decode($record['contrato']);
			$this->info['fieldsValues'] = array_from_db($record['fieldsValues']);
			$this->extract_fields();
			$this->update_fields();
        }

    }

	public function get_id() {
		return (int)$this->id;
	}

	public function get($field) {
		if(isset($this->info[$field])) {
			return $this->info[$field];
		}
		return "";
	}

	public function get_fields() {
		return $this->fields;
	}

	public function get_vendor_fields() {

		$fields = array();
		
		foreach($this->fields as $f) {
			if($f['type']=="vendor") {
				$fields[] = $f;
			}
		}

		return $fields;

	}

	public function get_field_value($field) {
		foreach($this->fields as $f) {
			if($f['field']==$field) {
				return $f['value'];
			}
		}
		return "";
	}

	public function build() {

		# vars
		$text = $this->get("raw");

		# box
		if(trim($this->get_field_value("Importe_Box_Car_Rental"))=="" || trim($this->get_field_value("Importe_Box_Car_Rental"))=="NA" || trim($this->get_field_value("Importe_Box_Car_Rental"))=="N/A") {
			preg_match('/<box>(.*?)<\/box>/', $text, $result);
			if(var_is_valid_array($result) && isset($result[0]) && $result[0]!=="") {
				$text = str_replace($result[0]."\r\n\r\n", "", $text);
			}
		} else {
			$text = str_replace(array("<box>", "</box>"), "", $text);
		}

		# pago
		if(trim($this->get_field_value("Monto_de_Pago"))=="" || trim($this->get_field_value("Monto_de_Pago"))=="NA" || trim($this->get_field_value("Monto_de_Pago"))=="N/A") {
			preg_match('/<sipago>(.*?)<\/sipago>/', $text, $result);
			if(var_is_valid_array($result) && isset($result[0]) && $result[0]!=="") {
				$text = str_replace($result[0], "", $text);
				$text = str_replace(array("<nopago>", "</nopago>"), "", $text);
			}
		} else {
			preg_match('/<nopago>(.*?)<\/nopago>/', $text, $result);
			if(var_is_valid_array($result) && isset($result[0]) && $result[0]!=="") {
				$text = str_replace($result[0], "", $text);
				$text = str_replace(array("<sipago>", "</sipago>"), "", $text);
			}
		}

		$this->info['raw'] = $text;

	}

    public function extract_fields() {

		# get vendor fields from raw contract
        $contract = $this->get("raw");
        preg_match_all('/{(\w*)}\*?/s', $contract, $matches);
        if(is_array($matches) && isset($matches[0])) {
            $search = array_unique($matches[0]);
            $names = array_unique($matches[1]);
        }

        $fields = array();
        if(isset($names) && count($names)>0) {
            for($i=0; $i<count($names); $i++) {
                $req = (substr($search[$i], -1)=="*") ? true : false;
                $fields[] = array("search" => $search[$i], "field" => str_replace("*", " ", $names[$i]), "text" => str_replace(array("_", "*"), " ", $names[$i]), "type" => "vendor", "req" => $req, "value" => "");
                
            }
        }

		# get company (editable) fields from raw contract
        preg_match_all('/\|(\w*)\|/s', $contract, $matches);
        if(is_array($matches) && isset($matches[0])) {
            $search = array_unique($matches[0]);
            $names = array_unique($matches[1]);
        }

        if(isset($names) && count($names)>0) {
            for($i=0; $i<count($names); $i++) {
                $req = (substr($search[$i], -1)=="*") ? true : false;
                $fields[] = array("search" => $search[$i], "field" => str_replace("*", " ", $names[$i]), "text" => str_replace(array("_", "*"), " ", $names[$i]), "type" => "company", "req" => $req, "value" => "");
                
            }
        }

        $this->fields = $fields;
        
    }

	public function update_fields($values="") {

		# saved values
		$saved = $this->get("fieldsValues");
		if(is_array($this->fields) && is_array($saved)) {
			for($i=0; $i<count($this->fields); $i++) {
				if(isset($saved[$this->fields[$i]['field']])) {
					$this->fields[$i]['value'] = $saved[$this->fields[$i]['field']];
				}
			}
		}

		# form values
		if(is_array($this->fields) && is_array($values)) {
			for($i=0; $i<count($this->fields); $i++) {
				if(isset($values[$this->fields[$i]['field']])) {
					$this->fields[$i]['value'] = $values[$this->fields[$i]['field']];
				}
			}
		}

	}

	public function fill() {

		// replace project
		$search_project = array( "PROYECTO_CLAVE", "PROYECTO_NOMBRE", "PROYECTO_FECHA_INICIO", "PROYECTO_FECHA_FIN", "PROYECTO_LUGAR" );
		$replace_project = array( $this->get("clave"), $this->get("titulo"), DateES::format("d \d\\e F \d\\e\l Y", $this->get("fechaInicio")), DateES::format("d \d\\e F \d\\e\l Y", $this->get("fechaFin")), $this->get("lugar") );
	
		// replace vendor info
		$search_vendor = array( "PROVEEDOR_RFC", "PROVEEDOR_RAZON_SOCIAL", "PROVEEDOR_EMAIL", "PROVEEDOR_BANCO", "PROVEEDOR_CUENTA", "PROVEEDOR_CLABE", "PROVEEDOR_REPSE_NUMERO", "PROVEEDOR_REPSE_AVISO" );
		$replace_vendor = array( $this->get("rfc"), $this->get("razonSocial"), $this->get("email"), $this->get("banco"), $this->get("cuenta"), $this->get("clabe"), $this->get("repseNumero"), $this->get("repseAviso") );

		$text = str_replace($search_project, $replace_project, $this->get("raw"));
		$text = str_replace($search_vendor, $replace_vendor, $text);

		// replace fecha firma
		$text = str_replace("Contrato_Firma_Fecha", DateES::format("d \d\\e F \d\\e\l Y", $this->get("firmaFecha")), $text);

		// replace signatures
		# company signature
		$cs = new SignatureImage();
		$cs->fromfile(PATH_SIGNATURES.$this->get("firmaContratos"));
		$comp_sign = $cs->img();

		# vendor signature
		$vs = new SignatureImage();
		$vs->fromstr($this->get("firma"));
		$vendor_sign = $vs->img();

		if($vendor_sign!="") {
			$text = str_replace("EMPRESA_FIRMA", $comp_sign, $text);
			$text = str_replace("PROVEEDOR_FIRMA", $vendor_sign, $text);
		} else {
			$text = str_replace("EMPRESA_FIRMA", "", $text);
			$text = str_replace("PROVEEDOR_FIRMA", "", $text);
		}
		
		// replace adenda fields
		if($this->get("tipo")=="Adenda") {
			$fecha_contrato = query_select_single_value("fechaCreado", TABLE_CONTRACTS_VENDORS, "id = ".$this->get("parentId"));
			$fecha_firma = ($this->get("firmaFecha")!="N/A") ? $this->get("firmaFecha") : date("Y-m-d");
			$search_adenda = array( "FECHA_ADENDA", "FECHA_CONTRATO", "FECHA_FIRMA_ADENDA");
			$replace_adenda = array( DateES::format("d \d\\e F \d\\e\l Y", $this->get("fechaCreado")), DateES::format("d \d\\e F \d\\e\l Y", $fecha_contrato), DateES::format("d \d\\e F \d\\e\l Y", $fecha_firma));
			$text = str_replace($search_adenda, $replace_adenda, $text);
		}

		foreach($this->fields as $f) {
			if(!is_null($f['value']) && trim($f['value'])!="") {
				$text = str_replace($f['search'], $f['value'], $text);
			}
		}

		# clean
		$text = str_replace("|", "", $text);

        $this->html = $text;

	}

	public function save_fields() {
		$fields = array();
		if(is_array($this->fields)) {
			foreach($this->fields as $f) {
				$fields[$f['field']] = $f['value'];
			}
		}
		return query_update(TABLE_CONTRACTS_VENDORS, array("fieldsValues" => array_to_db($fields)), "id = ".$this->get_id());
	}

	public function sign($signature) {
		return query_update(TABLE_CONTRACTS_VENDORS, array("firmaStatusId" => CONTRACT_STATUS_SIGNED, "firmaFecha" => date("Y-m-d"), "firma" => $signature), "id = ".$this->get_id());
	}

	public function reject() {
		return sql_query("UPDATE ".TABLE_CONTRACTS_VENDORS." SET firmaStatusId = ".CONTRACT_STATUS_PENDING.", firmaFecha = NULL WHERE id = ".$this->get_id());
	}

    public function delete() {
        file_delete($this->get("anexo"));
		query_delete(TABLE_CONTRACTS_VENDORS, "parentId = ".$this->get_id());
		return query_delete(TABLE_CONTRACTS_VENDORS, "id = ".$this->get_id());
    }

	public function delete_attachment() {
		file_delete($this->get("pathContratos").$this->get("anexo"));
		return query_update(TABLE_CONTRACTS_VENDORS, array("anexo" => ""), "id = ".$this->get_id());
	}

	public function get_html() {
		$this->build();
		$this->fill();
		$html = text_to_html($this->html);
		return $html;
	}

	public function pdf() {
		$html = $this->get_html();
        $pdf = new ContractPDF();
        $pdf->load_body($html);
        $pdf->render();
        $pdf->stream("Contrato.pdf");
	}

}
