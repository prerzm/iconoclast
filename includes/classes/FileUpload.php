<?php

class FileUpload {

    protected $file;
    protected $save_path;
    protected $save_name;
    protected $error_on_empty = false;
    protected $error = 0;
    protected $errors = array(
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the maximum allowed file size',
        2 => 'The uploaded file exceeds the maximum allowed file size directive',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A system error or extension stopped the file upload.',
        20 => 'Not a file.',
        21 => 'The source file is not an uploaded file.',
        22 => 'The file could not be moved to the specified path',
        23 => "The file type submitted is not allowed"
    );
    protected $allowed_types = array();

    public function __construct($file, $path, $allowed=["pdf"]) {

        if( !is_array($file) || !isset($file['error']) || !is_int($file['error']) || !isset($file['name']) ) {
            throw new \Exception("Invalid input file structure!");
        }

        $this->file = $file;
        $this->save_path = $path;
        $this->set_allowed_types($allowed);

    }

    public function set_allowed_types($allowed) {
        $allowed_types = array();
        $default_types = array(
            "pdf" => "application/pdf", 
            "jpg" => "image/jpeg", 
            "jpeg" => "image/jpeg", 
            "png" => "image/png", 
            "gif" => "image/gif", 
            "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        );
        if(is_array($allowed) && count($allowed)>0) {
            foreach($allowed as $ext) {
                if(isset($default_types[$ext])) {
                    $allowed_types[$ext] = $default_types[$ext];
                }
            }
        } else {
            $allowed_types = $default_types;
        }
        $this->allowed_types = $allowed_types;
    }

    public function file_submitted() {
        return ($this->file['name']!="");
    }

    public function is_valid() {

        if( $this->file['error']>0 ) {
            $this->error = $this->file['error'];
            return false;
        }

        $extension = (string)pathinfo($this->file['name'], PATHINFO_EXTENSION);
        $mime_type = mime_content_type($this->file['tmp_name']);
        if( $this->file['name']=="" || !isset($this->allowed_types[$extension]) || $this->allowed_types[$extension]!=$mime_type ) {
            $this->error = 23;
            return false;
        }

        if(!is_file($this->file['tmp_name'])) {
            $this->error = 20;
            return false;
        }

        if(!is_uploaded_file($this->file['tmp_name']) ) {
            $this->error = 21;
            return false;
        }

        return true;
    
    }

    public function get_error() {
        return $this->errors[$this->error];
    }

    public function upload() {

        if($this->is_valid()) {

            # check upload path
            if(!is_dir($this->save_path)) {
                mkdir($this->save_path);
            }

            # set save name
            $this->save_name = uniqid().".".pathinfo($this->file['name'], PATHINFO_EXTENSION);

            if(!move_uploaded_file($this->file['tmp_name'], $this->save_path.$this->save_name)) {
                $this->error = 22;
                return false;
            }

            return true;

        }

        return false;

    }

    public function get_save_path() {
        return $this->save_path;
    }

    public function get_save_name() {
        return $this->save_name;
    }

    public function get_array_for_db() {
        $values['dateAdded'] = date("Y-m-d H:i:s");
        $values['original'] = $this->file['name'];
        $values['path'] = $this->save_path;
        $values['saved'] = $this->save_name;
        return $values;
    }

}