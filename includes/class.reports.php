<?php

# abstract class for reports
abstract class Report {

	protected $name;
	protected $header;
	protected $rows;
	protected $totals;

	abstract public function query($filters);

	public function getName() {

		return $this->name;

	}

	public function getHeader() {

		return $this->header;

	}

	public function displayHeader() {

		if(is_array($this->header)) {
			print "\t<tr>\n";
			foreach($this->header as $th) {
				print "\t<th>$th</th>\n";
			}
			print "\t</tr>\n";
		}

	}

	public function getRows() {

		return $this->rows;

	}

	public function displayRows() {

		if(is_array($this->rows)) {
			foreach($this->rows as $tr) {
				print "\t<tr>\n";
				foreach($tr as $key => $td) {
					if(is_numeric($td)) {
						print "\t<td style=\"text-align:right;\">".number_currency($td)."</td>\n";
					} else {
						print "\t<td>$td</td>\n";
					}
				}
				print "\t</tr>\n";
			}
		} else {
			print "\t<tr><td colspan=\"".count($this->header)."\">No hay resultados</td></tr>\n";
		}

	}

	public function getTotals() {

		return $this->totals;

	}

	public function displayTotals() {

		if(is_array($this->totals) && count($this->totals)>0) {
			print "\t<tr class=\"level_total\">\n";
			foreach($this->totals as $th) {
				if(is_numeric($th)) {
					print "\t<th style=\"background-color:#eaeaea;text-align:right;white-space:nowrap;\">".number_currency($th)."</th>\n";
				} else {
					print "\t<th style=\"background-color:#eaeaea;text-align:right;\">$th</th>\n";
				}
			}
			print "\t</tr>\n";
		}

	}

}
# ./Report


# RepPos - Cuentas por Pagar
class RepPos extends Report {

	public function __construct($filters) {

		$this->name = "Cuentas por Pagar";
		$this->query($filters);

	}

	public function query($filters) {

		# query
		list($this->header, $this->rows, $this->totals) = get_report_pos($filters);

	}

	public function displayHeader() {

		if(is_array($this->header)) {
			print "\t<tr>\n";
			foreach($this->header as $th) {
				if($th!="Total" && $th!="Banco" && $th!="CLABE" && $th!="ABA" && $th!="SWIFT") {
					print "\t<th>$th</th>\n";
				}
			}
			print "\t</tr>\n";
		}

	}

	public function displayRows() {

		if(is_array($this->rows)) {

			$results = $this->rows;

			for($i=0; $i<count($results); $i++) {

				print "\t<tr>\n";

				if($results[$i]['fechaDePago']!="" && strtotime($results[$i]['fechaDePago'])!==false) {
					print "\t<td>".$results[$i]['fechaDePago']."</td>\n";
				} else {
					print "\t<td>-</td>\n";
				}

				print "\t<td>".$results[$i]['clave']."</td>\n";
				print "\t<td>".$results[$i]['titulo']."</td>\n";
				print "\t<td>".$results[$i]['concepto']."</td>\n";
				print "\t<td>".$results[$i]['razonSocial']."</td>\n";

				if( file_is_valid($results[$i]['pathFacturas'].$results[$i]['facturaUuid'].".pdf") && file_is_valid($results[$i]['pathFacturas'].$results[$i]['facturaUuid'].".xml")) {
					print "<td>\t";
					print "<a href=\"file.download.php?f=".base64_encode($results[$i]['pathFacturas'].$results[$i]['facturaUuid'].".pdf")."&t=o\" title=\"Descargar\"><img src=\"images/icon_pdf.png\" style=\"width:24px;\" /></a>";
					print "<a href=\"file.download.php?f=".base64_encode($results[$i]['pathFacturas'].$results[$i]['facturaUuid'].".xml")."\" title=\"Descargar\"><img src=\"images/icon_xml.png\" style=\"width:24px;\" /></a>";
					print "\n</td>\n";
				} else {
					print "\t<td>-</td>\n";
				}

				print "\t<td>".$results[$i]['pagoForma']."</td>\n";

				print "\t<td style=\"text-align:right;white-space:nowrap;\">".number_currency($results[$i]['monto'])." ".$results[$i]['moneda']."</td>\n";
				print "\t<td style=\"text-align:right;\">".number_currency($results[$i]['retIVA'])."</td>\n";
				print "\t<td style=\"text-align:right;\">".number_currency($results[$i]['retISR'])."</td>\n";
				print "\t<td style=\"text-align:right;\">".number_currency($results[$i]['iva'])."</td>\n";
				print "\t<td style=\"text-align:right;\">".number_currency($results[$i]['totalMXN'])."</td>\n";

				print "\t<td>".$results[$i]['pagoStatus']."</td>\n";

				print "\t</tr>\n";

			}

		} else {
			print "\t<tr><td colspan=\"".count($this->header)."\">No hay resultados</td></tr>\n";
		}

	}

	public function export() {

		get_excel_pos($this->header, $this->rows);

	}

}
# ./RepPos


# RepComp - Cuentas por Pagar sin complemento
class RepComp extends Report {

	public function __construct($filters) {

		$this->name = "Cuentas sin Complemento de Pago";
		$this->query($filters);

	}

	public function query($filters) {

		# query
		list($this->header, $this->rows, $this->totals) = get_report_comp($filters);

	}

	public function displayHeader() {

		if(is_array($this->header)) {
			print "\t<tr>\n";
			foreach($this->header as $th) {
				if($th!="Total" && $th!="Banco" && $th!="CLABE" && $th!="ABA" && $th!="SWIFT") {
					print "\t<th>$th</th>\n";
				}
			}
			print "\t</tr>\n";
		}

	}

	public function displayRows() {

		if(is_array($this->rows)) {

			$results = $this->rows;

			for($i=0; $i<count($results); $i++) {
				print "\t<tr>\n";
				print "\t<td>".$results[$i]['proyecto']."</td>\n";
				print "\t<td>".$results[$i]['razonSocial']."</td>\n";
				print "\t<td>".$results[$i]['concepto']."</td>\n";
				print "\t<td>".$results[$i]['fechaDePago']."</td>\n";
				print "\t<td>".$results[$i]['pagoForma']."</td>\n";
				print "\t<td style=\"text-align:right;white-space:nowrap;\">".number_currency($results[$i]['totalMXN'])."</td>\n";
				print "\t<td>".$results[$i]['pagoStatus']."</td>\n";
				print "\t</tr>\n";
			}

		} else {
			print "\t<tr><td colspan=\"".count($this->header)."\">No hay resultados</td></tr>\n";
		}

	}

	public function export() {

		get_excel_comp($this->header, $this->rows);

	}

}
# ./RepComp


# RepProy - Proyectos
class RepProy extends Report {

	public function __construct($filters) {

		$this->name = "Proyectos";
		$this->query($filters);

	}

	public function query($filters) {

		# query
		list($this->header, $this->rows, $this->totals) = get_report_proyectos($filters);

	}

	public function export() {

		get_excel_proyectos($this->header, $this->rows);

	}

}


# RepConcept - Gastos por Concepto
class RepConcept extends Report {

	public function __construct($filters) {

		$this->name = "Gastos por Concepto";
		$this->query($filters);

	}

	public function query($filters) {

		# query
		list($this->header, $this->rows, $this->totals) = get_report_concept($filters);

	}

	public function displayHeader() {

		if(is_array($this->header)) {
			print "\t<tr>\n";
			foreach($this->header as $th) {
				if($th=="Concepto") {
					print "\t<th>$th</th>\n";
				} else {
					print "\t<th style=\"text-align:right;\">$th</th>\n";
				}
			}
			print "\t</tr>\n";
		}

	}

	public function displayRows() {

		if(is_array($this->rows)) {

			$results = $this->rows;

			for($i=0; $i<count($results); $i++) {

				print "\t<tr>\n";

				print "\t<td>".$results[$i]['concepto']."</td>\n";
				print "\t<td style=\"text-align:right;\">".number_currency($results[$i]['retIVA'])."</td>\n";
				print "\t<td style=\"text-align:right;\">".number_currency($results[$i]['retISR'])."</td>\n";
				print "\t<td style=\"text-align:right;\">".number_currency($results[$i]['iva'])."</td>\n";
				print "\t<td style=\"text-align:right;\">".number_currency($results[$i]['totalMXN'])."</td>\n";

				print "\t</tr>\n";

			}

		} else {
			print "\t<tr><td colspan=\"".count($this->header)."\">No hay resultados</td></tr>\n";
		}

	}

	public function export() {

		get_excel_concept($this->header, $this->rows);

	}

}
# ./RepConcept


# RepProvs - Proveedores
class RepProvs extends Report {

	public function __construct($filters) {

		$this->name = "Proveedores";
		$this->query($filters);

	}

	public function query($filters) {

		# query
		list($this->header, $this->rows, $this->totals) = get_report_proveedores($filters);

	}

	public function export() {

		get_excel_proveedores($this->header, $this->rows);

	}

}


# RepFlujo - Flujo de efectivo
class RepFlujo extends Report {

	public function __construct($filters) {

		$this->name = "Flujo de Efectivo";
		$this->query($filters);

	}

	public function query($filters) {

		# query
		list($this->header, $this->rows, $this->totals) = get_report_flujo($filters);

	}

	public function export() {

		get_excel_flujo($this->header, $this->rows);

	}

}


# RepDir - Directores
class RepDir extends Report {

	public function __construct($filters) {

		$this->name = "Directores";
		$this->query($filters);

	}

	public function query($filters) {

		# query
		list($this->header, $this->rows, $this->totals) = get_report_directores($filters);

	}

	public function export() {

		get_excel_directores($this->header, $this->rows);

	}

}


?>