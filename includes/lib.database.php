<?php

/** RZM PHP Framework **/

function sql_select($sql, $debug=false) {

	# mysql connection var
	global $mysqli;

	# print query if debug is set
	if($debug==true) {
		print "<br>[sql_query] ".str_replace(array("SELECT", "FROM", "WHERE", "GROUP", "ORDER", "LIMIT"), array("<br>SELECT", "<br>FROM", "<br>WHERE", "<br>GROUP", "<br>ORDER", "<br>LIMIT"), $sql)."<br>";
	}
	
	# query & display error on dev
	$array	= array();
	
	if(ENVIRONMENT=="DEVELOPMENT") { 
	
		$result	= @$mysqli->query($sql) or
			sql_display_error($sql, $mysqli->errno, $mysqli->error);
	
	} else {
	
		# query
		$result	= @$mysqli->query($sql);
	
	}
	
	# form array
	if(($numrows=@$result->num_rows)>0) {
	
		while($row=$result->fetch_assoc()) {
			$array[] = $row;
		}
	
	} else {
	
		return false;
	}
	
	return $array;

}

function sql_select_row($sql, $debug=false) {

	# mysql connection var
	global $mysqli;

	# print query if debug is set
	if($debug==true) {
		print "<br>[sql_query] $sql<br>";
	}
	
	# query & display error on dev
	$array	= array();
	
	if(ENVIRONMENT=="DEVELOPMENT") { 
	
		$result	= @$mysqli->query($sql) or
			sql_display_error($sql, $mysqli->errno, $mysqli->error);
	
	} else {
	
		# query
		$result	= @$mysqli->query($sql);
	
	}
	
	# form array
	if(($numrows=@$result->num_rows)>0) {
	
		$array = @$result->fetch_assoc();
	
	} else {
	
		return false;
	}
	
	return $array;

}

function sql_count($sql, $debug=false) {

	# mysql connection var
	global $mysqli;

	# print query if debug is set
	if($debug==true) {
		print "<br>[sql_query] $sql<br>";
	}
	
	# query & display error on dev
	$array	= array();
	
	if(ENVIRONMENT=="DEVELOPMENT") { 
	
		$result	= @$mysqli->query($sql) or
			sql_display_error($sql, $mysqli->errno, $mysqli->error);
	
	} else {
	
		# query
		$result	= @$mysqli->query($sql);
	
	}
	
	# return total
	$numrows	= (int)@$result->num_rows;
	
	return $numrows;
	
}


function sql_update($sql, $debug=false) {

	# mysql connection var
	global $mysqli;

	# print query if debug is set
	if($debug==true) {
		print "<br>[sql_query] $sql<br>";
	}
	
	# query & display error on dev
	$array	= array();
	
	if(ENVIRONMENT=="DEVELOPMENT") { 
	
		$result	= @$mysqli->query($sql) or
			sql_display_error($sql, $mysqli->errno, $mysqli->error);
	
	} else {
	
		# query
		$result	= @$mysqli->query($sql);
	
	}
	
	# return number of affected rows
	return $mysqli->affected_rows;
	
}


function sql_query($sql, $debug=false) {

	# mysql connection var
	global $mysqli;
	
	# verify info
	if($debug==true) {
	
		print "<br>[sql_query] $sql<br>";
		return 1;
		
	} else {
	
		# query & display error on dev
		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			$result	= @$mysqli->query($sql) or
				sql_display_error($sql, $mysqli->errno, $mysqli->error);
		
			return @$mysqli->affected_rows;
	
		} else {
		
			# query
			$result	= @$mysqli->query($sql);
		
			return @$mysqli->affected_rows;
	
		}

	}
	
}


function query_select_single_value($fields, $tables, $where="", $order="", $debug=false) {

	# mysql connection var
	global $mysqli;

	# verify info
	if(trim($fields)=="" || trim($tables)=="") {

		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			die("function query_select_single_value: params($fields, $tables, $where, $order)");
			
		} else {
		
			return false;
			
		}
		
	}
	
	if(trim($where)!="") {
		$where	= "WHERE $where";
	}
	
	if(trim($order)!="") {
		$order	= "ORDER BY $order";
	}
	
	# form query
	$sql	= "SELECT $fields FROM $tables $where $order LIMIT 0, 1";
	
	# print query if debug is set
	if($debug==true) {
		print "<br>[sql_query] $sql<br>";
	}
	
	# query & display error on dev
	$array	= array();
	
	if(ENVIRONMENT=="DEVELOPMENT") {
	
		$result	= @$mysqli->query($sql) or
			sql_display_error($sql, $mysqli->errno, $mysqli->error);
	
	} else {
	
		# query
		$result	= @$mysqli->query($sql);
	
	}
	
	# extract single value
	if(($numrows=@$result->num_rows)>0) {
	
		$array 	= @$result->fetch_array(MYSQLI_NUM);

		$value	= $array[0];
	
	} else {
	
		return false;
	}
	
	return $value;

}


function query_update($table, $values, $where, $debug=false) {

	# mysql connection var
	global $mysqli;

	# verify and update
	if(trim($table)!="" && is_array($values) && count($values)>0 && trim($where)!="") {

		$i 			= 0;
		$str_fields	= "";
	
		foreach($values as $key=>$value) {
			if($i==0) {
				$str_fields .= (is_null($value)) ? "$key = NULL" : "$key='$value'";
			} else {
				$str_fields .= (is_null($value)) ? ", $key = NULL" : ", $key='$value'";
			}
			$i++;
		}
		
		// build query
		$sql = "UPDATE $table SET $str_fields WHERE $where;";
	
		if($debug==true) {
		
			print "<br>[sql_query] $sql<br>";
			return 1;
			
		} else {
		
			# query & display error on dev
			if(ENVIRONMENT=="DEVELOPMENT") { 
			
				$result	= @$mysqli->query($sql) or
					sql_display_error($sql, $mysqli->errno, $mysqli->error);
			
				return @$mysqli->affected_rows;
		
			} else {
			
				# query
				$result	= @$mysqli->query($sql);
			
				return @$mysqli->affected_rows;
		
			}
	
		}
	
	} else {

		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			die("function query_update: params(table, count values ".count($values).", $where)");
			
		} else {
		
			return 0;
			
		}
		
	}
	
}


function query_delete($table, $where, $debug=false) {

	# mysql connection var
	global $mysqli;
	
	# verify info
	if(trim($table)!="" && trim($where)!="") {

		// build query
		$sql = "DELETE FROM $table WHERE $where;";
	
		if($debug==true) {
		
			print "<br>[sql_query] $sql<br>";
			return 1;
			
		} else {
		
			# query & display error on dev
			if(ENVIRONMENT=="DEVELOPMENT") { 
			
				$result	= @$mysqli->query($sql) or
					sql_display_error($sql, $mysqli->errno, $mysqli->error);
			
				return @$mysqli->affected_rows;
		
			} else {
			
				# query
				$result	= @$mysqli->query($sql);
			
				return @$mysqli->affected_rows;
		
			}
	
		}
	
	} else {

		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			die("function query_delete($table, $where, $debug)");
			
		} else {
		
			return 0;
			
		}
		
	}
	
}


function query_insert($table, $values, $debug=0) {

	# mysql connection var
	global $mysqli;

	if(is_array($values) && count($values)>0) {

		$i 			= 0;
		$str_fields	= "(";
		$str_values	= "(";
	
		foreach($values as $key=>$value) {
			if($i==0) {
				$str_fields.="$key";
				$str_values.="'".$mysqli->real_escape_string($value)."'";
			} else {
				$str_fields.=", $key";
				$str_values.=", '".$mysqli->real_escape_string($value)."'";
			}
			$i++;
		}
		
		$str_fields.=")";
		$str_values.=")";

		// build query
		$sql = "INSERT INTO $table $str_fields VALUES $str_values;";
	
		if($debug==1) {
		
			print "[sql_query] $sql<br>";
			
			# return random id for testing purposes
			return rand(100000,110000);
			
		} else {
		
			# query & display error on dev
			if(ENVIRONMENT=="DEVELOPMENT") { 
			
				$result	= @$mysqli->query($sql) or
					sql_display_error($sql, $mysqli->errno, $mysqli->error);
			
				return @$mysqli->insert_id;
		
			} else {
			
				# query
				$result	= @$mysqli->query($sql);
			
				return @$mysqli->insert_id;
		
			}
	
		}
	
	} else {
	
		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			die("function query_insert: params($table, count values ".count($values).")");
			
		} else {
		
			return false;
			
		}
	
	}
	
}


function sql_display_error($sql, $mysqli_errno, $mysqli_err) {

	# log error
	error_log("$mysqli_errno - $mysqli_err  ($sql)");

	if(ENVIRONMENT=="DEVELOPMENT") { 
		
		print "<table cellspacing=\"0\" cellpadding=\"5\" style=\"width:100%; border: 1px solid #000000;\">
				<tr>
				<td align=\"center\" style=\"border-bottom: 1px solid #000000; \"><font color=\"red\">[$mysqli_errno] - $mysqli_err</font></td>
				</tr>
				<tr><td height=\"10\"></td></tr>
				<tr>
				<td align=\"center\">$sql</td>
				</tr>
				</table>";
	
	}

}


?>