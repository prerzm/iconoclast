<?php

# clas DBUpdate

class DBUpdate {

    protected $log;
    protected $changes;
    protected $debug = false;
    protected $processed = false;
    protected $messages = array();

    public function __construct($debug) {

        $file = PATH_DBUPDATE."dbupdate.sql";
        if(file_exists($file) && is_file($file)) {

            # read log
            $this->log = file_get_contents($file);
            
            # read changes
            $lines = explode(PHP_EOL, $this->log);
            if($lines) {
                $rows = array();
                foreach($lines as $line) {
                    if(substr($line, 0, 1)!="#" && trim(substr($line, 0, 1))!="") {
                        $rows[] = $line;
                    }
                }
                $this->changes = $rows;
            }

            # set value to apply or print changes
            $this->debug = (bool)$debug;

        }

    }

    public function hasChanges() {
        if(is_array($this->changes) && count($this->changes)>0) {
            return true;
        } else {
            return false;
        }
    }

    public function processUpdates() {

        if($this->hasChanges()) {
            $this->processed = true;
            foreach($this->changes as $sql) {
                if(sql_query($sql, $this->debug)>0) {
                    $this->messages[] = "Executed: $sql";
                } else {
                    $this->messages[] = "Error executing: $sql";
                    $this->processed = false;
                }
            }
        }

        if($this->debug===false) {
            $this->updateLog();
            $this->deleteUpdates();
        }

        return $this->processed;
        
    }

    public function getMessages() {
        foreach($this->messages as $msg) {
            print "$msg<br>";
        }
        die();
    }

    public function updateLog() {
        if($this->processed) {
            $file = PATH_DBUPDATE."dblog.sql";
            file_put_contents($file, PHP_EOL . PHP_EOL . $this->log, FILE_APPEND);
        }
    }

    public function deleteUpdates() {
        if($this->processed) {
            $file = PATH_DBUPDATE."dbupdate.sql";
            if(file_exists($file) && is_file($file)) {
                unlink($file);
            }
        }
    }

}

?>