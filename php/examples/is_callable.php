<?php

class SomeClass {

    protected function someMethod() {

    }
}

$obj = new SomeClass();

var_dump(is_callable([$obj, 'SomeMethod'], false, $callback_name), $callback_name);

class Constructor {

    public function __construct()
    {
    }

    public function fn () {

    }
}

var_dump(is_callable(['Constructor', '__construct']));

var_dump(is_callable(['Constructor', 'fn']));