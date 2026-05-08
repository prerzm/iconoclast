<?php

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "settings.php";

# process
switch(aglobal('cmd', 20)) {

    case 'add':

        if($global_perms['ADD']) {

            # vars
            $invoice_exists = false;
            $config['configKey'] = apost('configKey', 50);
            $config['configName'] = apost('configName', 150);
            $config['configValue'] = apost('configValue', 450);
            $config['configPublic'] = (int)apost('configPublic');
            $config['configType'] = apost('configType', 10);
            $options = apost('configOptions', 1750);

            # options
            if(!var_is_empty($options)) {
                $ops = array();
                $rows = explode(PHP_EOL, $options);
                if( is_array($rows) && count($rows)>1 ) {
                    foreach($rows as $row) {
                        $text_values = explode(",", $row);
                        if( is_array($text_values) && count($text_values)==2) {
                            $ops[] = array("text" => $text_values[0], "value" => $text_values[1]);
                        }
                    }
                }
                $config['configOptions'] = json_encode($ops, JSON_UNESCAPED_UNICODE);
            }

            # query
            $updated = query_insert(TABLE_SETTINGS, $config);
            
            if($updated>0) {
                system_log($updated, TABLE_SETTINGS, "Add", json_encode($config));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'update':
	
        if($global_perms['EDIT']) {

            # vars
            $updated = 0;
            $fields = (isset($_POST['config'])) ? $_POST['config'] : false;

            # query
            if(is_array($fields)) {

                foreach($fields as $configId => $configValue) {
                    $values = array("configValue" => $configValue);
                    $this_updated = query_update(TABLE_SETTINGS, $values, "configId = $configId");
                    if($this_updated>0) {
                        $updated++;
                        system_log($configId, TABLE_SETTINGS, "Update", json_encode($values));
                    }
                }

            }

            if($updated>0) {
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

	break;

	case 'del':
	
        if($global_perms['DELETE']) {

            # vars
            $updated = 0;
            $configId = (int)aget('id');
            $config = get_setting($configId);

            # query
            if($config) {
                $updated = query_delete(TABLE_SETTINGS, "configId = $configId");
            }

            if($updated>0) {
                system_log($configId, TABLE_SETTINGS, "Delete", json_encode($config));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
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