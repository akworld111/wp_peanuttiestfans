<?php

class Fv_Empty_Unit {
    protected $contest_id;

    function __get($name) {
        if ($name == 'status') {
            return 0;
        } elseif ($name == 'contest_id') {
            return $this->contest_id;
        } elseif ($name == 'id') {
            return false;
        } else {
            return '';    
        }
        
    }

    function __set($name, $value) {
        if ($name == 'contest_id') {
            $this->contest_id = (int)$value;
        }          
        return true;
    }    
    
}
