<?php

# include configuration file
include_once ("includes/inc.init.php");

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

# output
print '<pre>';

#var_dump($matches);
var_dump($_SESSION, session_id());

# end
#display_alerts();
print "<br>Finished... ".uniqid()."<br>";
print '</pre>';