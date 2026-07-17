<?php

class XMLDocument {

    protected $info;
    protected $errors;

    public function __construct($xml_string) {

        $previous_value = libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false; 
        $dom->loadXml($xml_string);
        libxml_use_internal_errors($previous_value);
        if (libxml_get_errors()) {
            $this->errors = libxml_get_errors();
        } else {
            $this->info = $this->DOMtoArray($dom);
        }

    }

    public function is_valid() {
        if( is_array($this->info) && count($this->info)>0 && !is_array($this->errors) ) {
            return true;
        }
        return false;
    }

    public function get_errors() {
        return $this->errors;
    }

    public function get($field) {
        if(isset($this->info[$field])) {
            return $this->info[$field];
        } else {
            return "";
        }
    }

    public function get_by_path($path) {
        $data = $this->info;
        $value = "";
        if($this->is_valid()) {
            if(strpos($path, "/")!==false) {
                $keys = explode("/", $path);
                if(is_array($keys) && count($keys)>0) {
                    foreach($keys as $k) {
                        if(isset($data[$k])) {
                            $data = $data[$k];
                        } else {
                            return "";
                        }
                    }
                    $value = $data;
                }
            }
        }
        return $value;
    }

    public function DOMtoArray($root) {
        $result = array();

        if ($root->hasAttributes()) {
            $attrs = $root->attributes;
            foreach ($attrs as $attr) {
                $result['@attributes'][$attr->name] = $attr->value;
            }
        }

        if ($root->hasChildNodes()) {
            $children = $root->childNodes;
            if ($children->length == 1) {
                $child = $children->item(0);
                if (in_array($child->nodeType,[XML_TEXT_NODE,XML_CDATA_SECTION_NODE])) {
                    $result['_value'] = $child->nodeValue;
                    return (count($result) == 1) ? $result['_value'] : $result;
                }
            }
            $groups = array();
            foreach ($children as $child) {
                if (!isset($result[$child->nodeName])) {
                    $result[$child->nodeName] = $this->DOMtoArray($child);
                } else {
                    if (!isset($groups[$child->nodeName])) {
                        $result[$child->nodeName] = array($result[$child->nodeName]);
                        $groups[$child->nodeName] = 1;
                    }
                    $result[$child->nodeName][] = $this->DOMtoArray($child);
                }
            }
        }
        
        return $result;
    }

}
