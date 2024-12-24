<?php

include 'fonctions_starterre.php';

ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');
set_time_limit(300); // 300 secondes = 5 minutes, adapte selon tes besoins


//PAGE TEST
$nbr_vh_delete_starterre = 0;

// on récupere la base vehicule starterre soit par la base soit par un export de starterre
$vhs_starterre = get_vhs_starterre_from_base();
// $vhs_starterre = get_vhs_starterre_from_csv();

foreach ($vhs_starterre as $vh) {
    $count = post_vh_to_delete_starterre($vh['id_starterre']);
    $nbr_vh_delete_starterre += $count;
}

sautdeligne();

echo "nombre de vh supprimés : $nbr_vh_delete_starterre";
