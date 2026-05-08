<?php

class FileHandler {

    protected $path;
    protected $saved_name;
    protected $download_name;

    public function __construct($path, $name, $download = "") {
        $this->path = $path;
        $this->saved_name = $name;
        $this->download_name = $download;
    }

    public function get_original_name() {
        return $this->original;
    }

    public function get_path() {
        return $this->path;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_extension() {
        return pathinfo($this->name, PATHINFO_EXTENSION);
    }

    public function is_valid() {
        if(file_exists($this->path.$this->saved_name) && is_file($this->path.$this->saved_name)) {
            return true;
        }
        return false;
    }

    public function download() {

        if($this->is_valid()) {

            # download name
            $download_name = ($this->download_name!="") ? $this->download_name : $this->saved_name;

            # set headers
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=\"$download_name\"");
            header("Content-Type: ".mime_content_type($this->path.$this->saved_name));
            header("Content-Transfer-Encoding: binary");

            # Read the file from disk
            readfile($this->path.$this->saved_name);

        }

    }

    public function delete() {
        
        # verify file path & name
        if($this->is_valid()) {
            return unlink($this->path.$this->saved_name);
        }

        return false;
        
    }

}