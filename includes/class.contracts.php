<?php

# class for contracts

class Contract {

	public $id;
	public $companyId;
	public $companySignature;
	public $type;
	public $class_name;
	public $name;
	public $updated;
	public $contract;
	public $text;
	public $fields;
	protected $attach;

	public function __construct($class_name, $contract_vendor_id) {

		$record = sql_select_row("	SELECT 	c.*, CONCAT('".PATH_SIGNATURES."', co.firmaContratos) AS firmaContratos 
									FROM 	".TABLE_CONTRACTS_VENDORS." cp, ".TABLE_PROJECTS." p, ".TABLE_CONTRACTS." c, ".TABLE_COMPANIES." co 
									WHERE 	cp.proyectoId = p.proyectoId AND p.companyId = c.companyId AND p.companyId = co.companyId AND 
											c.className = '$class_name' AND cp.id = $contract_vendor_id");
		if($record) {
			$this->id = (int)$contract_vendor_id;
			$this->companyId = (int)$record['companyId'];
			$this->companySignature = $record['firmaContratos'];
			$this->type = $record['tipo'];
			$this->class_name = $record['className'];
			$this->name = $record['nombre'];
			$this->updated = $record['lastUpdated'];
			$this->contract = base64_decode($record['contrato']);
			$this->fields = get_contract_fields($this->contract);
		}
	}

	public function display_fields_html() {

		$html = "";

		foreach($this->fields as $f) {
			$html .= "\n".'<div class="control-group">
						<label class="control-label">'.$f['text'].'</label>
						<div class="controls">
							<input type="text" name="'.$f['field'].'" class="span10 m-wrap" />
						</div>
					</div>';
		}

		print $html;

	}

	public function display_fields_js() {

		$html = "rules: {";
		foreach($this->fields as $f) {
			if($f['req']) {
				$html .= "\n\t".$f['field'].": { required: true },";
			}
		}
		$html .= "\n}";

		print $html;

	}

	public function fill($post) {

        for($i=0; $i<count($this->fields); $i++) {
			$key = $this->fields[$i]['field'];
            $this->fields[$i]['res'] = (isset($post[$key]) && $post[$key]!="") ? $post[$key] : "NA";
        }

	}

	public function set_text($text) {
		
		$this->text = $text;

	}

	public function set_body_as_text() {

		$this->text = $this->get_body();
	}

	public function upload_attach($files, $vendor_rfc, $project_path) {

	}

	public function get_body() {

		$body = "";
		$firma_start = strpos($this->contract, "--FIRMA--");

		if($firma_start!==false) {
			$body = substr($this->contract, 0, $firma_start);
		}
	
		return $body;
	
	}

	public function replace_info($project, $vendor) {

		// replace project
		$search_project = array( "PROYECTO_CLAVE", "PROYECTO_NOMBRE", "PROYECTO_CLIENTE", "PROYECTO_FECHA_FILMACION", "PROYECTO_DIRECTOR", "PROYECTO_PRODUCTOR" );
		$replace_project = array( $project['clave'], $project['titulo'], $project['cliente'], $project['diaFilmacion'], $project['director'], $project['productor'] );
	
		// replace vendor info
		$search_vendor = array( "PROVEEDOR_RFC", "PROVEEDOR_RAZON_SOCIAL", "PROVEEDOR_EMAIL", "PROVEEDOR_BANCO", "PROVEEDOR_CUENTA", "PROVEEDOR_CLABE" );
		$replace_vendor = array( $vendor['rfc'], $vendor['razonSocial'], $vendor['email'], $vendor['banco'], $vendor['cuenta'], $vendor['clabe'] );
	
		// replace date & other info
		$search_date = array( "FECHA_DIAS", "FECHA_MES", "FECHA_AÑO", "--FIRMA--" );
		$replace_date = array( date("d"), get_date_es("F", date("Y-m-d")), date("Y"), "" );
	
		$this->text = str_replace($search_project, $replace_project, $this->text);
		$this->text = str_replace($search_vendor, $replace_vendor, $this->text);
		$this->text = str_replace($search_date, $replace_date, $this->text);

		foreach($this->fields as $f) {
			$this->text = str_replace($f['search'], $f['res'], $this->text);
		}

		return $this->text;

	}

	public function get_html() {

		$html = str_replace( array("[", "]"), array("<b>", "</b>"), $this->text);
		$html = nl2br($html);

		return $html;
		
	}

	public function get_html_for_pdf() {

		return str_replace( array("[", "]"), array("<b>", "</b>"), utf8_decode($this->text));

	}

	public function create_pdf($vendor_rfc, $vendor_signature, $project_path) {

	}

}


# 1 - Personas Morales - Proveedores 2021

class ContractPM extends Contract {

	public function __construct($contract_vendor_id) {
		parent::__construct(__CLASS__, $contract_vendor_id);
	}

	public function display_fields_html() {

		$html = "";

		foreach($this->fields as $f) {
			$html .= "\n".'<div class="control-group">
						<label class="control-label">'.$f['text'].'</label>
						<div class="controls">
							<input type="text" name="'.$f['field'].'" class="span10 m-wrap" />
						</div>
					</div>';
		}

		$html .= '<div class="control-group">
					<label class="control-label">Anexar cotización del trabajo realizado dentro de la filmación (formato PDF)</label>
					<div class="controls">
						<input type="file" name="anexo" class="span10 m-wrap"/><br>
					</div>
				</div>';

		print $html;

	}

	public function display_fields_js() {

		$html = "rules: {";
		foreach($this->fields as $f) {
			if($f['req']) {
				$html .= "\n\t".$f['field'].": { required: true },";
			}
		}
		$html .= "\n\t"."anexo: { required: true }";
		$html .= "\n}";

		print $html;

	}

	public function upload_attach($files, $vendor_rfc, $project_path) {

		$document = ( isset($files["anexo"]) && $files["anexo"]['size']>0 && $files["anexo"]['error']==0) ? $files["anexo"] : false;
        if($document!==false) {
            $attach_name = get_contract_attach_filename($vendor_rfc);
            $uploaded = save_contract_attach($document, $project_path, $attach_name);
            if($uploaded===true) {
                $this->attach = $attach_name;
				return query_update(TABLE_CONTRACTS_VENDORS, array("anexo" => $attach_name), "id = ".$this->id);
            } else {
                set_alert("error", "Hubo un problema al subir el anexo - $uploaded");
            }
		}

		return false;

	}

	public function create_pdf($project, $vendor, $signature) {

		# get contract html
		$this->set_text($this->contract);
		$this->replace_info($project, $vendor);
		$html = $this->get_html_for_pdf();

        # generate pdf
        $pdf = new PrimoPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','', 10);
        $pdf->WriteHTML($html);
        
        # add signatures
        $pdf->add_signature($this->companySignature, 105, 33);
        $pdf->add_signature($project['pathFirmas'].$signature, 40, 32);

        # save pdf
		$contract_filename = $vendor['rfc']."_".uniqid()."_PM.pdf";
        $pdf->Output("F", $project['pathContratos'].$contract_filename);

        # update contract_vendors record
        if(file_exists($project['pathContratos'].$contract_filename) && is_file($project['pathContratos'].$contract_filename)) {

			return query_update(TABLE_CONTRACTS_VENDORS, array("contrato" => $contract_filename, "info" => ""), "id = ".$this->id);

        } else {
			set_alert("error", "No se pudo guardar el contrato, favor de intentar nuevamente");
		}

		return false;
		
	}

}


# 2 - Personas Físicas - Servicios Profesionales con Trabajadores

class ContractPF extends Contract {

	public function __construct($contract_vendor_id) {
		parent::__construct(__CLASS__, $contract_vendor_id);
	}

	public function create_pdf($project, $vendor, $signature) {

		# get contract html
		$this->set_text($this->contract);
		$this->replace_info($project, $vendor);
		$html = $this->get_html_for_pdf();

        # generate pdf
        $pdf = new PrimoPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','', 10);
        $pdf->WriteHTML($html);
        
        # add signatures
        $pdf->add_signature($this->companySignature, 145, 33);
        $pdf->add_signature($project['pathFirmas'].$signature, 70, 32);

        # save pdf
		$contract_filename = $vendor['rfc']."_".uniqid()."_PF.pdf";
        $pdf->Output("F", $project['pathContratos'].$contract_filename);

        # update contract_vendors record
        if(file_exists($project['pathContratos'].$contract_filename) && is_file($project['pathContratos'].$contract_filename)) {

			return query_update(TABLE_CONTRACTS_VENDORS, array("contrato" => $contract_filename, "info" => ""), "id = ".$this->id);

        } else {
			set_alert("error", "No se pudo guardar el contrato, favor de intentar nuevamente");
		}

		return false;
		
	}

}


# 3 - Carta Confidencialidad (NDA)

class ContractNDA extends Contract {

	public function __construct($contract_vendor_id) {
		parent::__construct(__CLASS__, $contract_vendor_id);
	}

	public function create_pdf($project, $vendor, $signature) {

		# get contract html
		$this->set_text($this->contract);
		$this->replace_info($project, $vendor);
		$html = $this->get_html_for_pdf();

        # generate pdf
        $pdf = new PrimoPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','', 10);
        $pdf->WriteHTML($html);
        
        # add signatures
        $pdf->add_signature($project['pathFirmas'].$signature, 30, 30);

        # save pdf
		$contract_filename = $vendor['rfc']."_".uniqid()."_NDA.pdf";
        $pdf->Output("F", $project['pathContratos'].$contract_filename);

        # update contract_vendors record
        if(file_exists($project['pathContratos'].$contract_filename) && is_file($project['pathContratos'].$contract_filename)) {

			return query_update(TABLE_CONTRACTS_VENDORS, array("carta" => $contract_filename, "info" => ""), "id = ".$this->id);

        } else {
			set_alert("error", "No se pudo guardar el contrato, favor de intentar nuevamente");
		}

		return false;
		
	}

}


# 5 - Agencias - Contrato Personas Morales

class ContractAgenciasPM extends Contract {

	public function __construct($contract_vendor_id) {
		parent::__construct(__CLASS__, $contract_vendor_id);
	}

	public function create_pdf($project, $vendor, $signature) {

		# get contract html
		$this->set_text($this->contract);
		$this->replace_info($project, $vendor);
		$html = $this->get_html_for_pdf();

        # generate pdf
        $pdf = new PrimoPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','', 10);
        $pdf->WriteHTML($html);
        
        # add signatures
		$pdf->Image($this->companySignature, 20, $pdf->GetY()-33);
		$pdf->Image($project['pathFirmas'].$signature, 90, $pdf->GetY()-38);

        # save pdf
		$contract_filename = $vendor['rfc']."_".uniqid()."_APM.pdf";
        $pdf->Output("F", $project['pathContratos'].$contract_filename);

        # update contract_vendors record
        if(file_exists($project['pathContratos'].$contract_filename) && is_file($project['pathContratos'].$contract_filename)) {

			return query_update(TABLE_CONTRACTS_VENDORS, array("contrato" => $contract_filename, "info" => ""), "id = ".$this->id);

        } else {
			set_alert("error", "No se pudo guardar el contrato, favor de intentar nuevamente");
		}

		return false;
		
	}

}


# 6 - Agencias - Contrato Personas Físicas

class ContractAgenciasPF extends Contract {

	public function __construct($contract_vendor_id) {
		parent::__construct(__CLASS__, $contract_vendor_id);
	}

	public function create_pdf($project, $vendor, $signature) {

		# get contract html
		$this->set_text($this->contract);
		$this->replace_info($project, $vendor);
		$html = $this->get_html_for_pdf();

        # generate pdf
        $pdf = new PrimoPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','', 10);
        $pdf->WriteHTML($html);
        
        # add signatures
		$pdf->Image($this->companySignature, 20, $pdf->GetY()-33);
		$pdf->Image($project['pathFirmas'].$signature, 90, $pdf->GetY()-38);

        # save pdf
		$contract_filename = $vendor['rfc']."_".uniqid()."_APM.pdf";
        $pdf->Output("F", $project['pathContratos'].$contract_filename);

        # update contract_vendors record
        if(file_exists($project['pathContratos'].$contract_filename) && is_file($project['pathContratos'].$contract_filename)) {

			return query_update(TABLE_CONTRACTS_VENDORS, array("contrato" => $contract_filename, "info" => ""), "id = ".$this->id);

        } else {
			set_alert("error", "No se pudo guardar el contrato, favor de intentar nuevamente");
		}

		return false;
		
	}

}


# 4 - Agencias - Carta Confidencialidad (NDA)

class ContractAgenciasNDA extends Contract {

	public function __construct($contract_vendor_id) {
		parent::__construct(__CLASS__, $contract_vendor_id);
	}

	public function create_pdf($project, $vendor, $signature) {

		# get contract html
		$this->set_text($this->contract);
		$this->replace_info($project, $vendor);
		$html = $this->get_html_for_pdf();

        # generate pdf
        $pdf = new PrimoPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','', 10);
        $pdf->WriteHTML($html);
        
        # add signature
		$pdf->Image($project['pathFirmas'].$signature, 9, $pdf->GetY()-30);

        # save pdf
		$contract_filename = $vendor['rfc']."_".uniqid()."_ANDA.pdf";
        $pdf->Output("F", $project['pathContratos'].$contract_filename);

        # update contract_vendors record
        if(file_exists($project['pathContratos'].$contract_filename) && is_file($project['pathContratos'].$contract_filename)) {

			return query_update(TABLE_CONTRACTS_VENDORS, array("carta" => $contract_filename, "info" => ""), "id = ".$this->id);

        } else {
			set_alert("error", "No se pudo guardar el contrato, favor de intentar nuevamente");
		}

		return false;
		
	}

}