<?php

# include files
include_once ('includes/inc.config.php');
include_once ('includes/inc.db.connect.php');
include_once ("includes/inc.db.tables.php");
include_once ("includes/lib.database.php");
include_once ("includes/lib.misc.php");
include_once ("includes/lib.abp.php");
include_once ("vendor/autoload.php");
include_once ("includes/autoload.php");


# vars
$iconoclast_id = 2;
$global_company = get_company_info($iconoclast_id);
load_settings();

# payment complement
$sql_date = "AND (g.comprobanteNotify IS NULL OR g.comprobanteNotify > '".date("Y-m-d", strtotime("-10 days"))."')";
$pos = sql_select(" SELECT	g.gastoId, CONCAT(p.clave, ' - ', p.titulo) AS proyecto, v.razonSocial, v.email, g.concepto, g.fechaDePago, g.comprobante, g.comprobanteNotify, fp.pagoForma, g.totalMXN, ps.pagoStatus
                    FROM ".TABLE_POS." g, ".TABLE_VENDORS." v, ".TABLE_PROJECTS." p, ".TABLE_COMPANIES." c, ".TABLE_SAT_FORMA_PAGO." fp, ".TABLE_PAYMENTS_STATUS." ps
                    WHERE g.proveedorId = v.proveedorId AND g.proyectoId = p.proyectoId AND p.companyId = c.companyId AND g.pagoFormaId = fp.pagoFormaId AND g.pagoStatusId = ps.pagoStatusId AND 
                        g.pagoStatusId = 3 AND g.pagoMetodoId <> 1 AND g.facturaUuid <> '' AND g.comprobante = ''
                        $sql_date 
                    ORDER BY g.fechaDePago DESC
                    LIMIT 0, 30");

if($pos) {
    $today = date("Y-m-d");
    $emails = array();
    foreach($pos as $p) {
        $emails[] = $p['email'];
        query_update(TABLE_POS, array("comprobanteNotify" => $today), "gastoId = ".(int)$p['gastoId']);
    }
}

# send email
$emails = array_unique($emails);
$mail = new NEWMailer();
$mail->vendors_reminder_comp($emails);

?>