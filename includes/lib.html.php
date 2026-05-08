<?php

/** RZM PHP Framework */

/** Functions for html output **/

# Return the html code for a select input option values
function form_select_options($data_array, $id, $name, $selected_id="") {

	# verify info
	if(!is_array($data_array) || count($data_array)==0 || $id=="" || $name=="") {
	
		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			return "<option>function form_select_options: params: $data_array, $id, $name, $selected_id</option>";
			
		} else {
	
			return "";
			
		}
		
	}
	
	# vars
	$str_options	= "";
	
	# process
	for($i=0; $i<count($data_array); $i++) {
	
		if($data_array[$i][$id]==$selected_id) {
		
			$str_options .= '<option value="'.$data_array[$i][$id].'" selected>'.$data_array[$i][$name].'</option>'."\n";
			
		} else {

			$str_options .= '<option value="'.$data_array[$i][$id].'">'.$data_array[$i][$name].'</option>'."\n";
			
		}
		
	}
	
	# return formed string
	return $str_options;

}


# Return the html code for a select input option values with groups
function form_select_options_groups($data_array, $group_by, $id, $name, $selected_id="") {

	# verify info
	if(!is_array($data_array) || count($data_array)==0 || $group_by=="" || $id=="" || $name=="") {
	
		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			return "<option>function form_select_options: params: $data_array, $id, $name, $selected_id</option>";
			
		} else {
	
			return "";
			
		}
		
	}
	
	# options
	$group = $data_array[0][$group_by];
	$str_options	= "<optgroup label=\"$group\">\n";
	for($i=0; $i<count($data_array); $i++) {

		if($data_array[$i][$group_by]!=$group) {
			$group = $data_array[$i][$group_by];
			$str_options .= "</otpgroup>\n";
			$str_options .= '<optgroup label="'.$data_array[$i][$group_by]."\">\n";
		}

		if($data_array[$i][$id]==$selected_id) {
			$str_options .= '<option value="'.$data_array[$i][$id].'" selected>'.$data_array[$i][$name].'</option>'."\n";
		} else {
			$str_options .= '<option value="'.$data_array[$i][$id].'">'.$data_array[$i][$name].'</option>'."\n";
		}
		
	}
	$str_options .= "</optgroup>\n";
	
	# return formed string
	return $str_options;

}


# Return the html code for a select of numbers
function form_select_options_numbers($start=0, $end=10, $steps=1, $selected_id="") {

	# vars
	$str_options	= "";
	
	# process
	for($i=$start; $i<=$end; $i+=$steps) {

		if($i==$selected_id) {
			$str_options .= '<option value="'.$i.'" selected>'.$i.'</option>'."\n";
		} else {
			$str_options .= '<option value="'.$i.'">'.$i.'</option>'."\n";
		}

	}
	
	# return formed string
	return $str_options;

}


# Return the html code for a select of hours
function form_select_options_hours($start=9, $end=19, $steps=30, $selected_id="") {

	# vars
	$str_options	= "";
	
	# process
	for($i=$start; $i<=$end; $i++) {

		if($steps>0) {
			for($j=0; $j<60; $j+=$steps) {
				$hours = ($i<10) ? "0".$i : $i;
				$value = ($j==0) ? $hours.":00" : $hours.":".$j;
				if($value==$selected_id) {
					$str_options .= '<option value="'.$value.'" selected>'.$value.'</option>'."\n";
				} else {
					$str_options .= '<option value="'.$value.'">'.$value.'</option>'."\n";
				}
			}
		}
	}
	
	# return formed string
	return $str_options;

}


# Return the html code for a select of days
function form_select_options_days($selected_id="") {

	# vars
	$str_options	= "";
	
	# process
	for($i=1; $i<32; $i++) {
	
		if($i == $selected_id) {
		
			$str_options .= '<option value="'.$i.'" selected>'.$i.'</option>'."\n";
			
		} else {

			$str_options .= '<option value="'.$i.'">'.$i.'</option>'."\n";
			
		}
		
	}
	
	# return formed string
	return $str_options;

}


# Return the html code for a select of months
function form_select_options_months($type="text", $selected_id="") {

	# vars
	$str_options	= "";
	
	# process
	for($i=1; $i<13; $i++) {

		$value = ($i<10) ? "0".$i : $i;
		
		# type
		if($type="text") {
			$text = get_date_es("F", date("Y-$i-01"));
		} elseif($type=="number") {
			$text	= ($i<10) ? "0".$i : $i;
		}
	
		if($value == $selected_id) {
			$str_options .= '<option value="'.$value.'" selected>'.$text.'</option>'."\n";
		} else {
			$str_options .= '<option value="'.$value.'">'.$text.'</option>'."\n";
		}
		
	}
	
	# return formed string
	return $str_options;

}


# Return the html code for a select of years (range)
function form_select_options_years($year_start="", $year_end="", $selected_id=0) {

	# vars
	$str_options	= "";
	
	# year start
	if($year_start=="") {
		$year_start	= date("Y") - 100;
	}
	
	if($year_end=="") {
		$year_end	= date("Y");
	}
	
	# process
	for($i=$year_end; $i>=$year_start; $i--) {
		
		if($i == $selected_id) {
		
			$str_options .= '<option value="'.$i.'" selected>'.$i.'</option>'."\n";
			
		} else {

			$str_options .= '<option value="'.$i.'">'.$i.'</option>'."\n";
			
		}
		
	}
	
	# return formed string
	return $str_options;

}


# Return the html code for a set of checkboxes
function form_checkboxes_set($data_array, $name, $value, $text, $selected_values) {

	# verify info
	if(!is_array($data_array) || count($data_array)==0 || $name=="" || $value=="" || $text=="" || !is_array($selected_values)) {
	
		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			return "function form_checkboxes_set: params: $data_array, $name, $value, $text, $selected_values</option>";
			
		} else {
	
			return "";
			
		}
		
	}
	
	# vars
	$str_options	= "";
	
	# process
	for($i=0; $i<count($data_array); $i++) {
	
		if(in_array($data_array[$i][$value], $selected_values)) {
		
			$str_options .= '<label><input type="checkbox" name="'.$name.'[]" value="'.$data_array[$i][$value].'" checked="checked">'.$data_array[$i][$text].'</label>'."\n";
			
		} else {

			$str_options .= '<label><input type="checkbox" name="'.$name.'[]" value="'.$data_array[$i][$value].'">'.$data_array[$i][$text].'</label>'."\n";
			
		}
		
	}
	
	# return formed string
	return $str_options;


}


# Return the html code for a set of radios
function form_radios_set($data_array, $id, $name, $value, $text, $selected_value="") {

	# verify info
	if(!is_array($data_array) || count($data_array)==0 || $name=="" || $value=="" || $text=="") {
	
		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			return "function form_radios_set: params: $data_array, $name, $value, $text, $selected_values</option>";
			
		} else {
	
			return "";
			
		}
		
	}
	
	# vars
	$str_options	= "";
	
	# process
	for($i=0; $i<count($data_array); $i++) {
	
		if($data_array[$i][$value]==$selected_value) {
		
			$str_options .= '<label><input type="radio" id="'.$id.'" name="'.$name.'" value="'.$data_array[$i][$value].'" checked="checked">'.$data_array[$i][$text].'</label>'."\n";
			
		} else {

			$str_options .= '<label><input type="radio" id="'.$id.'" name="'.$name.'" value="'.$data_array[$i][$value].'">'.$data_array[$i][$text].'</label>'."\n";
			
		}
		
	}
	
	# return formed string
	return $str_options;


}

?>