<?php

class SignatureImage {

    const WIDTH = 460;
    const HEIGHT = 285;
    protected $header = "data:image/png;base64,";
    protected $string = "";
    protected $image = false;

    public function fromstr($str="") {
        if($str!="") {
            $string = str_replace($this->header, "", $str);
            $image = @imagecreatefromstring(base64_decode($string));
            if($image!==false) {
                $this->string = $string;
                $this->image = $image;
            }
        }
    }

    public function fromfile($fullpath) {
        if(file_exists($fullpath) && is_file($fullpath)) {
            $imagepng = @imagecreatefrompng($fullpath);
            if($imagepng!==false) {
                $this->image = $imagepng;
                ob_start();
                imagepng($imagepng);
                $image_data = ob_get_contents();
                ob_end_clean();
                $this->string = base64_encode($image_data);
            }
        }
    }

    public function valid() {
        if($this->string!="" && $this->image!==false) {
            return true;
        }
        return false;
    }

    public function str() {
        return $this->string;
    }

    public function img() {
        if($this->valid()) {
            return '<img src="'.$this->header.$this->string.'" />';
        }
        return "";
    }

    public function src() {
        return $this->header.$this->string;
    }

    public function save($filename="") {
        if($this->valid()) {
            $filename = ($filename!="") ? $filename : uniqid();
            $fp = fopen($filename, "wb");
            if(fwrite($fp, base64_decode($this->string))!==false) {
                fclose($fp);
                return $filename;
            }
        }
        return false;
    }

}
