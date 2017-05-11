<?php

$arr = array(1=>'one', 2=>'two');

foreach ($arr as $key => &$val) {} // do nothing
var_dump($arr);

foreach ($arr as $key => $val) {} // do nothing
var_dump($arr);

$arr = array(&$arr);
var_dump($arr === $arr);