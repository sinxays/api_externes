<?php

include 'fonctions_starterre.php';

ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');
set_time_limit(300); // 300 secondes = 5 minutes, adapte selon tes besoins

$environnement = 'prod';

$id_kepler = "VF1RJB00070604639";

$ireference = get_idStarterre_from_VIN($id_kepler,$environnement);

var_dump($ireference);





