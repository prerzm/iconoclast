<?php

/** PRE PHP Framework **/

function get_currencies($exclude="") {

    $exclude = ($exclude!="") ? " WHERE currencyCode <> '$exclude'" : "";
    $query = sql_select("SELECT * FROM ".TABLE_CURRENCIES." $exclude");

    $currencies = array();
    foreach($query as $i => $currency) {
        $currencies[$currency['currencyCode']] = (float)$currency['exchangeRate'];
    }

    return $currencies;

}

function get_exchange_rate($code) {

    $currencies = get_currencies();

    if(isset($currencies[$code])) {
        return $currencies[$code];
    }

    return 1;

}

function currency_convert_to_usd($amount) {

    $rate = get_exchange_rate("USD");

    return (float)($amount / $rate);
    
}


?>