<?php

class Document extends FileHandler {

    protected $name;
    protected $status;
    
    public function __construct($name, $status) {
        $this->name = $name;
        $this->status = $status;
    }

    public function get_status() {
        return "valid";
    }

    public function get_footer() {
        return '<button class="btn btn-small btn-danger">Eliminar</button>';
    }

    public function get_div() {
        $div = '<div class="filebox">
                    <div class="filebox_header">'.$this->name.'</div>
                    <div class="filebox_content"><img src="images/icon_file_'.$this->get_status().'.png" /></div>
                    <div class="filebox_footer"><button class="btn btn-small btn-danger">Eliminar</button></div>
                </div>';

    }

}