<?php

/** RZM PHP Framework **/

// Function for return a float number from a string
function number_float($number, $decimals=2) {

	$number = (float)str_replace( array("$", ",", " "), "", $number);

	return number_format($number, $decimals, ".", "");
	
}

// Function to format number as currency
function number_currency($amount, $symbol="$", $code="") {

	# strip amount
	$amount	= str_replace(array(",", "$", " "), "", $amount);

	if($code=="") {
		$code = "";
	} else {
		$code = " ".$code;
	}

	return $symbol." ".number_format($amount, 2, ".", ",").$code;
	
}


// Function to format number as thousands.decimals
function number_thousands_decimals($amount, $decimals=2) {

	# strip amount
	$amount	= str_replace(array(",", "$", " "), "", $amount);

	return number_format($amount, $decimals, ".", ",");
	
}


// Function to format number as thousands
function number_thousands($amount) {

	# strip amount
	$amount	= str_replace(array(",", "$", " "), "", $amount);
	$amount = $amount * 1;

	return number_format($amount, 0, "", ",");
	
}

function number_get_dec($amount) {
    $pos = strpos($amount, ".");
    if($pos===false) {
        return "00";
    } else {
        return str_pad(substr($amount, $pos+1, 2), 2, "0");
    }
}

function number_get_hundreds_text($amount) {
    
    $hunds = ['', 'Ciento', 'Doscientos', 'Trescientos', 'Cuatrocientos', 'Quinientos', 'Seiscientos', 'Setecientos', 'Ochocientos', 'Novecientos'];
    $tens = ['', 'Diez', 'Veinte', 'Treinta', 'Cuarenta', 'Cincuenta', 'Sesenta', 'Setenta', 'Ochenta', 'Noventa'];
    $tensu = ['', 'Once', 'Doce', 'Trece', 'Catorce', 'Quince', 'Dieciseis', 'Diecisiete', 'Dieciocho', 'Diecinueve'];
    $units = ['', 'Uno', 'Dos', 'Tres', 'Cuatro', 'Cinco', 'Seis', 'Siete', 'Ocho', 'Nueve'];
    $arr = str_split(str_pad((int)$amount, 3, "0", STR_PAD_LEFT));
    $text = "";

    if($arr[0]==0) {
        if($arr[1]==0) {
            $text = $units[$arr[2]];
        } else {
            if($arr[2]==0) {
                $text = $tens[$arr[1]];
            } else {
                if($arr[1]==1) {
                    $text = $tensu[$arr[2]];
                } else {
                    $text = $tens[$arr[1]]." y ".$units[$arr[2]];
                }
            }
        }
    } else {
        if($arr[0]==1 && $arr[1]==0 && $arr[2]==0) {
            $text = "Cien ";
        } else {
            $text = $hunds[$arr[0]]." ";
        }
        if($arr[1]==0) {
            $text .= $units[$arr[2]];
        } else {
            if($arr[2]==0) {
                $text .= $tens[$arr[1]];
            } else {
                if($arr[1]==1) {
                    $text .= $tensu[$arr[2]];
                } else {
                    $text .= $tens[$arr[1]]." y ".$units[$arr[2]];
                }
            }
        }
    }

    return $text;

}

function number_get_mill_hunds($amount) {

    $chunks = str_split(str_pad((int)$amount, 9, "0", STR_PAD_LEFT), 3);
    $text = "";

    $millions = (int)$chunks[0];
    if($millions>0) {
        $text = number_get_hundreds_text($chunks[0]);
        if($millions==1) {
            $text .= " Millón ";
        } else {
            $text .= " Millones ";
        }
    }

    $thous = (int)$chunks[1];
    if($thous>0) {
        $text .= number_get_hundreds_text($chunks[1])." Mil ";
    }

    $text .= number_get_hundreds_text($chunks[2]);

    return $text;

}

function number_amount_to_text($amount) {

    $decimals = number_get_dec($amount);
    $amount = number_get_mill_hunds(number_float($amount));
    $text = "$amount $decimals/100";
    $text = str_replace("  ", " ", $text);

    return $text;

}

?>