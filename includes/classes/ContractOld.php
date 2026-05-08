<?php

class ContractOld {

	protected $id;
	protected $info;
    protected $html;

    public function __construct($id) {

        $contract = sql_select_row("SELECT cv.*, 'Contrato' AS nombre, p.titulo, 
                                        v.rfc, v.razonSocial, v.email, v.repseReq, 
                                        cs.contratoStatus, 
                                        CONCAT('files/projects/', p.uniqId, '/contratos/', cv.contrato) AS contrato, 
                                        CONCAT('files/projects/', p.uniqId, '/contratos/', cv.anexo) AS anexo, 
                                        CONCAT('files/projects/', p.uniqId, '/contratos/', cv.carta) AS carta 
                                    FROM ".TABLE_CONTRACTS_VENDORS." cv, ".TABLE_PROJECTS." p, ".TABLE_VENDORS." v, ".TABLE_CONTRACTS_STATUS." cs 
                                    WHERE cv.proyectoId = p.proyectoId AND cv.proveedorId = v.proveedorId AND cv.firmaStatusId = cs.contratoStatusId AND 
                                        cv.id = $id");

        if($contract) {
            $this->id = (int)$id;
            $this->info = $contract;
            $this->html = "";
        }

    }

    public function get_id() {
        return $this->id;
    }

	public function get($field) {
		if(isset($this->info[$field])) {
			return $this->info[$field];
		}
		return "N/A";
	}

    public function get_fields() {
        return array();
    }

	public function get_html($type) {

        if(file_is_valid($this->get($type))) {
            $html = '<div style="text-align:center;">
                    <object data="'.$this->get($type).'" id="object_contract_'.$type.'" type="application/pdf" width="90%" height="800">
                        <div class="alert alert-error">
                            <h4>Hubo un problema al cargar el archivo anexo.</h4>
                        </div>
                    </object>
                </div>';
        } else {
            $html = '<div class="alert alert-error"><h4>Hubo un problema al cargar el archivo anexo.</h4></div>';
        }

        return $html;

    }

	public function reject() {

        # delete files
        file_delete($this->get("contrato"));
        file_delete($this->get("anexo"));
        file_delete($this->get("carta"));

		return sql_query("UPDATE ".TABLE_CONTRACTS_VENDORS." SET firmaStatusId = ".CONTRACT_STATUS_PENDING.", firmaFecha = NULL WHERE id = ".$this->get_id());

	}

    public function delete() {

        # delete files
        file_delete($this->get("contrato"));
        file_delete($this->get("anexo"));
        file_delete($this->get("carta"));

		return query_delete(TABLE_CONTRACTS_VENDORS, "id = ".$this->get_id());

    }

	public function delete_attachment() {

		# delete file
		file_delete($this->get("anexo"));

		#update
		return query_update(TABLE_CONTRACTS_VENDORS, array("anexo" => ""), "id = ".$this->get_id());

	}

	public function delete_nda() {

		# delete file
		file_delete($this->get("carta"));

		#update
		return query_update(TABLE_CONTRACTS_VENDORS, array("carta" => ""), "id = ".$this->get_id());

	}

}