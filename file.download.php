<?php

/** RZM PHP Framework **/

# include
include_once ("includes/inc.init.php");

# vars
$file = aget('f');
$type = aget('t');

# process
if($type=="") {
    file_download($file);
} elseif($type=="o") {
    file_open($file);
}

?>