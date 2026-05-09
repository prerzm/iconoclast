<?php

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "system.php";

# process
switch(aglobal('cmd', 20)) {

	case 'del_cuentas':
	
		if($global_perms['EDIT']) {

			# truncate tables
			sql_query("TRUNCATE TABLE ".TABLE_CUSTOMERS);
			$updated = query_delete(TABLE_SYSTEM_LOG, "modulo = '".TABLE_CUSTOMERS."'");

			# query
			if($updated) {
				set_alert("success", "La información ha sido eliminada.");
			} else {
				set_alert("error", "Hubo un problema, favor de intentar nuevamente");
			}
		
		} else {
			set_alert("error", "No cuenta con los permisos para acceder a este módulo");
		}

	break;

	case 'del_directores':
	
		if($global_perms['EDIT']) {

			# truncate tables
			sql_query("TRUNCATE TABLE ".TABLE_DIRECTORS);
			$updated = query_delete(TABLE_SYSTEM_LOG, "modulo = '".TABLE_DIRECTORS."'");

			# query
			if($updated) {
				set_alert("success", "La información ha sido eliminada.");
			} else {
				set_alert("error", "Hubo un problema, favor de intentar nuevamente");
			}
		
		} else {
			set_alert("error", "No cuenta con los permisos para acceder a este módulo");
		}

	break;

	case 'del_gastos':
	
		if($global_perms['EDIT']) {

			# truncate tables
			sql_query("TRUNCATE TABLE ".TABLE_POS);
			sql_query("TRUNCATE TABLE ".TABLE_POS_LOG);
			$updated = query_delete(TABLE_SYSTEM_LOG, "modulo = '".TABLE_POS."'");

			# query
			if($updated) {
				set_alert("success", "La información ha sido eliminada.");
			} else {
				set_alert("error", "Hubo un problema, favor de intentar nuevamente");
			}
		
		} else {
			set_alert("error", "No cuenta con los permisos para acceder a este módulo");
		}

	break;

	case 'del_nominas':
	
		if($global_perms['EDIT']) {

			# truncate tables
			sql_query("TRUNCATE TABLE ".TABLE_WAGES);
			$updated = query_delete(TABLE_SYSTEM_LOG, "modulo = '".TABLE_WAGES."'");

			# query
			if($updated) {
				set_alert("success", "La información ha sido eliminada.");
			} else {
				set_alert("error", "Hubo un problema, favor de intentar nuevamente");
			}
		
		} else {
			set_alert("error", "No cuenta con los permisos para acceder a este módulo");
		}

	break;

	case 'del_proyectos':
	
		if($global_perms['EDIT']) {

			# truncate tables
			sql_query("TRUNCATE TABLE ".TABLE_PROJECTS);
			sql_query("TRUNCATE TABLE ".TABLE_PROJECTS_BUDGETS);
			sql_query("TRUNCATE TABLE ".TABLE_PROJECTS_BUDGETS_ITEMS);
			$updated = query_delete(TABLE_SYSTEM_LOG, "modulo = '".TABLE_PROJECTS."'");
			$updated += query_delete(TABLE_SYSTEM_LOG, "modulo = '".TABLE_PROJECTS_BUDGETS."'");
			$updated += query_delete(TABLE_SYSTEM_LOG, "modulo = '".TABLE_PROJECTS_BUDGETS_ITEMS."'");

			# query
			if($updated) {
				set_alert("success", "La información ha sido eliminada.");
			} else {
				set_alert("error", "Hubo un problema, favor de intentar nuevamente");
			}
		
		} else {
			set_alert("error", "No cuenta con los permisos para acceder a este módulo");
		}

	break;

	case 'del_proveedores':
	
		if($global_perms['EDIT']) {

			# truncate tables
			sql_query("TRUNCATE TABLE ".TABLE_VENDORS);
			$updated = query_delete(TABLE_SYSTEM_LOG, "modulo = '".TABLE_VENDORS."'");

			# query
			if($updated) {
				set_alert("success", "La información ha sido eliminada.");
			} else {
				set_alert("error", "Hubo un problema, favor de intentar nuevamente");
			}
		
		} else {
			set_alert("error", "No cuenta con los permisos para acceder a este módulo");
		}

	break;

	case 'del_intentos':
	
		if($global_perms['EDIT']) {

			# truncate tables
			sql_query("TRUNCATE TABLE ".TABLE_USERS_ATTEMPTS);

			set_alert("success", "La información ha sido eliminada.");
		
		} else {
			set_alert("error", "No cuenta con los permisos para acceder a este módulo");
		}

	break;

	case 'del_budgets':
	
		if($global_perms['EDIT']) {

			# truncate tables
			sql_query("TRUNCATE TABLE ".TABLE_PROJECTS_BUDGETS);
			sql_query("TRUNCATE TABLE ".TABLE_PROJECTS_BUDGETS_ITEMS);
			$updated = query_delete(TABLE_SYSTEM_LOG, "modulo = '".TABLE_PROJECTS_BUDGETS."'");
			$updated += query_delete(TABLE_SYSTEM_LOG, "modulo = '".TABLE_PROJECTS_BUDGETS_ITEMS."'");

			# query
			if($updated>0) {
				set_alert("success", "La información ha sido eliminada.");
			} else {
				set_alert("error", "Hubo un problema, favor de intentar nuevamente");
			}
		
		} else {
			set_alert("error", "No cuenta con los permisos para acceder a este módulo");
		}

	break;

	case 'resetdb':
	
		if($global_perms['EDIT']) {

			# vars
			$updated = 0;
			$error = false;

			# truncate tables
			if(sql_query("TRUNCATE TABLE ".TABLE_CUSTOMERS)) { $updated++; }
			if(sql_query("TRUNCATE TABLE ".TABLE_DIRECTORS)) { $updated++; }
			if(sql_query("TRUNCATE TABLE ".TABLE_POS)) { $updated++; }
			if(sql_query("TRUNCATE TABLE ".TABLE_POS_LOG)) { $updated++; }
			if(sql_query("TRUNCATE TABLE ".TABLE_SYSTEM_LOG)) { $updated++; }
			if(sql_query("TRUNCATE TABLE ".TABLE_VENDORS)) { $updated++; }
			if(sql_query("TRUNCATE TABLE ".TABLE_PROJECTS)) { $updated++; }
			if(sql_query("TRUNCATE TABLE ".TABLE_PROJECTS_BUDGETS)) { $updated++; }
			if(sql_query("TRUNCATE TABLE ".TABLE_PROJECTS_BUDGETS_ITEMS)) { $updated++; }
			if(sql_query("TRUNCATE TABLE ".TABLE_USERS_ATTEMPTS)) { $updated++; }
			if(sql_query("TRUNCATE TABLE ".TABLE_WAGES)) { $updated++; }

			# query
			if($updated>0) {
				set_alert("success", "La información ha sido eliminada.");
			} else {
				set_alert("error", "Hubo un problema, favor de intentar nuevamente");
			}
		
		} else {
			set_alert("error", "No cuenta con los permisos para acceder a este módulo");
		}

	break;

	case 'updatedb':
	
		if($global_perms['EDIT']) {

			# includes
			include("../includes/class.dbupdate.php");

			# vars
			$updated = false;
			$debug = (bool)apost('debug');

			# apply changes
			$db = new DBUpdate($debug);

			if($db->hasChanges()) {
				$updated = $db->processUpdates();
			}

			# query
			if($updated) {
				set_alert("success", "La información ha sido actualizada.");
			} else {
				$db->getMessages();
			}
		
		} else {
			set_alert("error", "No cuenta con los permisos para acceder a este módulo");
		}

	break;

	case 'backupdb':
	
		$cmd = PATH_MYSQL."mysqldump --defaults-extra-file=".PATH_DBUPDATE."config.cnf servicio_primo --add-drop-table --no-tablespaces > ".PATH_DBUPDATE.date("Y-m-d")."_servicio_primo.sql";
		exec($cmd, $output, $result);
		
		if($output==0) {
			set_alert("success", "La base de datos fue actualizada.");
		} else {
			set_alert("error", "Hubo un problema, favor de intentar nuevamente");
		}
		
	break;

	case 'transfernames':

		print "corregir nombres de archivos de transfers...<br><br>";

		$files = sql_select("SELECT g.gastoId, g.proveedorId, g.transfer AS file, 
									p.proyectoId, CONCAT('".PATH_PROJECTS."', p.uniqId, '/transfers/') AS path 
							FROM ".TABLE_POS." g, ".TABLE_PROJECTS." p 
							WHERE g.proyectoId = p.proyectoId AND g.transfer LIKE '%,%'");

		$i = 0;
		foreach($files as $f) {
			$filename = $f['path'].$f['file'];
			if(file_is_valid($filename)) {
				$new_file = str_replace(",", ".", $f['file']);
				if(rename($filename, $f['path'].$new_file)) {
					query_update(TABLE_POS, array("transfer" => $new_file), "gastoId = ".(int)$f['gastoId']);
					print "file ".$f['file']." renamed to $new_file<br>";
					$i++;
				}
			}
		}
		print "<br>$i files renamed<br>";

		die();

	break;

	default:

		# set error & error message on session
		set_alert("error", "Hubo un problema en la información, por favor intenta nuevamente.");
	
	break;
	
}

# redirect
header("Location: ../$return");

?>