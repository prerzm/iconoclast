<?php

use Dompdf\Dompdf;
use Dompdf\Options;

class ContractPDF extends Dompdf {

    public function load_body($body) {
        $html = '<!DOCTYPE html><html lang="es-MX">';
		$html .= "<head><style>body {font-family:sans-serif;font-size:10px;font-style:normal;font-weight:normal;}</style></head>";
        $html .= "<body>$body</body>";
        $html .= "</html>";
        $this->loadHtml($html);
    }

}
