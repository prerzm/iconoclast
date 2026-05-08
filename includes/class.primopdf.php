<?php

require('fpdf/htmlpdf.php');

class PrimoPDF extends HtmlPDF {

    // add Primo signature
    public function add_signature($signature, $offset_x, $offset_y) {
        $this->Image($signature, $this->GetX()-$offset_x, $this->GetY()-$offset_y);
    }
    
}