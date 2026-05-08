<?php

# Settings

class Setting {

    protected $id;
    protected $cat;
    protected $key;
    protected $name;
    protected $class;
    protected $value;
    protected $options;

    public function __construct($id, $cat, $key, $name, $class, $value, $options="") {
        $this->id = (int)$id;
        $this->cat = $cat;
        $this->key = $key;
        $this->name = $name;
        $this->class = $class;
        $this->value = $value;
        $this->options = $options;
    }

    public function getId() {
        return $this->id;
    }

    public function getCat() {
        return $this->cat;
    }

    public function getKey() {
        return $this->key;
    }

    public function getName() {
        return $this->name;
    }
    public function getClass() {
        return $this->class;
    }
    public function getValue() {
        return $this->value;
    }

    public function getOptions() {
        return $this->options;
    }

}



class Text extends Setting {

    public function displayField() {
        print '<input type="text" name="config['.$this->getId().']" id="'.$this->getKey().'" class="'.$this->getClass().'" value="'.$this->getValue().'">';
    }

}


class Radio extends Setting {

    public function displayField() {

        $ops = $this->getOptions();

        if(is_array($ops)) {
            for($i=0; $i<count($ops); $i++) {
                $str = '<label><input type="radio" name="config['.$this->getId().']" id="'.$this->getKey().$i.'" value="'.$ops[$i]['value'].'"';
                $str .= ($this->getValue()==$ops[$i]['value']) ? ' checked>' : '>';
                $str .= ' '.$ops[$i]['text']."</label>\n";
                print $str;
            }
        }

    }

}


class Combo extends Setting {

    public function displayField() {

        $ops = $this->getOptions();

        if(is_array($ops)) {
            $str = '<select name="config['.$this->getId().']" id="'.$this->getKey().'" class="'.$this->getClass().'">';
            for($i=0; $i<count($ops); $i++) {
                $str .= '<option value="'.$ops[$i]['value'].'"';
                $str .= ($ops[$i]['value']==$this->getValue()) ? ' selected>' : '>';
                $str .= $ops[$i]['text']."</option>\n";
            }
            $str .= '</select><br>';
            print $str;
        }

    }

}

?>