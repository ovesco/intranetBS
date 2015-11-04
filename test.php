<?php

date_default_timezone_set('Europe/Zurich');

$date = new \DateTime();

echo $date->format('Y-m-d\TH:i:sP').PHP_EOL;