<?php

include 'fonctions_starterre.php';

ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');
// set_time_limit(300); // 300 secondes = 5 minutes, adapte selon tes besoins


// $test = recup_vhs_changed_fleet_for_starterre('PARC LOCATION');

$immatriculations_interessements = recup_immats_test();

foreach ($immatriculations_interessements as $key => $immatriculation) {
    echo $immatriculation['id'] . ' ==> ' . $immatriculation['immatriculation'] . '<br>';

    // put_type_vehicule($immatriculation['immatriculation'],$immatriculation['id']);
}

// var_dump($test);

