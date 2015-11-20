<?php

date_default_timezone_set('Europe/Zurich');

$class = 'class="ui8 autre-truc Bidon autre_9"';
$matches = array();


preg_match('/\".*?\"/', $class, $matches);//match word

var_dump($matches);

$matches2 = array();
preg_match_all('/([a-zA-Z0-9-_]+)/', $matches[0], $matches2);//match word

var_dump($matches2);