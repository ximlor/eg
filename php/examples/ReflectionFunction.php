<?php

/*
 * The ReflectionFunction class reports information about a function.
 */

$example = function($p1, $p2 = null) {

};

$func_info = new ReflectionFunction($example);

foreach( $func_info->getParameters() as $param ){
    print "{$param}\n";
}

