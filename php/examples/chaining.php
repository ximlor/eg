<?php

class Ex {

    protected $data;

    function set_name($name) {
        $this->data['name'] = $name;
        return $this;
    }

    function set_age($age) {
        $this->data['age'] = $age;
        return $this;
    }

    function __get($key){
        if(isset($this[$key])) {
            return $this[$key];
        }
    }
}

$ex = new Ex();

$ex->set_name('lili')->set_age(12);
