<?php

# include configuration file
include_once ("includes/inc.init.php");
include_once ("includes/lib.misc.php");
include_once ("includes/lib.numbers.php");
include_once ("includes/lib.dates.php");
include_once ("includes/lib.abp.reports.php");
include_once ("includes/class.reports.php");
require_once ("includes/PHPExcel.php");

# filters
$filters["report"] = aget('report', 12);
$filters["projectId"] = (int)aget('projectId');
$filters["concepto"] = aget('concepto',200);
$filters["budgetId"] = (int)aget('budgetId');
$filters["dateFrom"] = aget('dateFrom',10);
$filters["dateTo"] = aget('dateTo',10);
$filters["directorId"] = (int)aget('directorId');
$filters["pagoStatusId"] = (int)aget('pagoStatusId');
$filters["group"] = (int)aget('group');
$filters["proveedorId"] = (int)aget('proveedorId');
$filters["ordenarPor"] = (int)aget('ordenarPor');

# report
if($filters["report"]!="") {
    $type = strtolower($filters["report"]);
	$type = str_replace("_", "", ucwords($type));
	if(class_exists($type)) {
		$report = new $type($filters);
		$report->export();
	}
}

?>