<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ('includes/lib.numbers.php');

/*# includes
include_once ('includes/inc.config.php');

include_once ('includes/inc.db.connect.php');
include_once ("includes/inc.db.tables.php");
include_once ("includes/lib.vars.php");
include_once ("includes/lib.database.php");
include_once ("includes/lib.sessions.php");
include_once ("includes/lib.login.php");
include_once ("includes/lib.misc.php");
include_once ("includes/lib.perms.php");
include_once ("includes/lib.numbers.php");
include_once ("includes/lib.dates.php");
include_once ("includes/lib.abp.php");
include_once ("includes/lib.abp.reports.php");
include_once ("includes/class.cfdi.php");
include_once ("vendor/autoload.php");
include_once ("includes/autoload.php");
require_once ("includes/PHPExcel.php");
*/

# process
#$record = sql_select_row("SELECT * FROM xxxx WHERE contratoId = 1");
#$contract = base64_decode($record['contrato']);

// secciones
#preg_match_all('/\<(.*?)\>/s', $contract, $matches);

// partes

// clausulas

// firma

$cvs = sql_select("SELECT * FROM ico_contratos_proveedores cv WHERE cv.proyectoId > 0 AND cv.firmaStatusId = 1 AND cv.gastoId = 0");

foreach($cvs as $c) {
	$id = (int)$c['id'];
	$pid = (int)$c['proyectoId'];
	$vid = (int)$c['proveedorId'];
	$pos = sql_select("SELECT g.gastoId, g.proyectoId, g.proveedorId, g.concepto, g.totalMXN FROM ico_gastos g WHERE g.proyectoId = $pid AND g.proveedorId = $vid");
	if($pos) {
		if(count($pos)==1) {
			print "<br>Asign only POS ".$pos[0]['gastoId']." to Contract $id...<br>";
			query_update("ico_contratos_proveedores", array("gastoId" => $pos[0]['gastoId']), "id = $id");
		} else {
			print "<br>Contract $id:";
			print "Found ".count($pos)." POS...<br>";
			if($c['fieldsValues']!="") {
				$values = array_from_db($c['fieldsValues']);
				$con_ser = (isset($values['Servicios_Proporcionados_o_Personaje'])) ? $values['Servicios_Proporcionados_o_Personaje'] : "";
				$con_mon = (isset($values['Monto_de_Pago'])) ? $values['Monto_de_Pago'] : "";
				foreach($pos as $p) {
					$pos_ser = $p['concepto'];
					$pos_mon = number_amount_to_text($p['totalMXN'])." MXN";
					# if con & mon are equal asign
					if($con_ser==$pos_ser && $con_mon==$pos_mon) {
						print "<br>Contract to POS found!! Asigning gastoId ".$p['gastoId']." to contract $id...<br>";
						query_update("ico_contratos_proveedores", array("gastoId" => $p['gastoId']), "id = $id");
					} else {
						# else add contract
						$vendor = get_vendor($p['proveedorId']);
						$project = get_project($p['proyectoId']);
						$fields_values = array("Servicios_Proporcionados_o_Personaje" => $pos_ser, "Monto_de_Pago" => number_amount_to_text($pos_mon)." MXN", "Proyecto_Fecha_Inicio" => $project['fechaInicio'], "Proyecto_Fecha_Fin" => $project['fechaFin']);
						print "<br>Genarate contract  for POS ".$p['gastoId']."...<br>";
						vendor_add_contract($vendor, $project, "vendor", array_to_db($fields_values), $p['gastoId']);
					}
				}
			} else {
				for($i=0; $i<count($pos); $i++) {
					$vendor = get_vendor($pos[$i]['proveedorId']);
					$project = get_project($pos[$i]['proyectoId']);
					$fields_values = array("Servicios_Proporcionados_o_Personaje" => $pos[$i]['concepto'], "Monto_de_Pago" => number_amount_to_text($pos[$i]['totalMXN'])." MXN", "Proyecto_Fecha_Inicio" => $project['fechaInicio'], "Proyecto_Fecha_Fin" => $project['fechaFin']);
					if($i==0) {
						print "<br>Asign first POS ".$pos[$i]['gastoId']." to Contract $id...<br>";
						query_update("ico_contratos_proveedores", array("gastoId" => $pos[$i]['gastoId']), "id = $id");
					} else {
						print "<br>Generate contract for POS ".$pos[$i]['gastoId']."...<br>";
						vendor_add_contract($vendor, $project, "vendor", array_to_db($fields_values), $pos[$i]['gastoId']);
					}
				}
			}
		}
		print "<div style=\"margin-left:50px;\">";
		print "</div>";
	} else {
		print "<br>Contract has no POS!!<br>";
		var_dump($c);
	}
}

# output
print '<pre>';

#var_dump($matches);

# end
#display_alerts();
print "<br>Finished... ".uniqid()."<br>";
print '</pre>';