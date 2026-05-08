<?php

/** RZM PHP Framework **/

# include configuration file
include_once ('../includes/inc.init.php');

# process
switch(aglobal('cmd', 20)) {

    case 'search_customer':

        # vars
        $keyword = aget('query');
        $results = sql_select(" SELECT cuentaId AS data, razonSocial AS value 
                                FROM ".TABLE_CUSTOMERS." 
                                WHERE (rfc LIKE '%$keyword%' OR razonSocial LIKE '%$keyword%' OR email LIKE '%$keyword%') AND deleted = 0 
                                LIMIT 0, 50
                            ");
    
        # results
		if($results) {
			
			// json encode results
			$jsonArray = json_encode($results);

			print "{";
			print '"query": "'.$keyword.'",';
			print '"suggestions": ';
			print $jsonArray;
			print "}";

		} else {
			
			print "{";
			print '"query": "'.$keyword.'",';
			print '"suggestions": []';
			print "}";

		}

    break;

    case 'search_vendor':

        # vars
        $keyword = aget('query');
        $results = sql_select(" SELECT CONCAT(proveedorId, '|', extranjero) AS data, razonSocial AS value 
                                FROM ".TABLE_VENDORS." 
                                WHERE (rfc LIKE '%$keyword%' OR razonSocial LIKE '%$keyword%' OR email LIKE '%$keyword%') AND deleted = 0 
                                LIMIT 0, 50
                            ");
    
        # results
		if($results) {
			
			// json encode results
			$jsonArray = json_encode($results);

			print "{";
			print '"query": "'.$keyword.'",';
			print '"suggestions": ';
			print $jsonArray;
			print "}";

		} else {
			
			print "{";
			print '"query": "'.$keyword.'",';
			print '"suggestions": []';
			print "}";

		}

    break;

}	

?>