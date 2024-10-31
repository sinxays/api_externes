<?php

include 'fonctions_starterre.php';

ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');


//EXPORT STARTERRE

//creer un array avec tous les parc pour faire la boucle par parc
$parc_array = array("CVO BOURGES", "CVO CLERMONT FERRAND", "CVO MASSY", "CVO ORLEANS sud", "CVO TROYES");
// $parc_array = array("CVO ORLEANS sud");

$array_for_csv = array();

$last_index = 0;
foreach ($parc_array as $parc) {

    $page = 1;
    $datas_find = TRUE;

    // si plusieurs page, tant qu'on trouve des données on boucle
    while ($datas_find == TRUE) {
        $recup_kepler_for_starterre = recup_starterre($parc, $page);

        // si on trouve des données 
        if (!empty($recup_kepler_for_starterre)) {
            foreach ($recup_kepler_for_starterre as $nb_index_vh => $vh) {
                $retour_array[$last_index] = mise_en_array_des_donnees_recup($array_for_csv, $nb_index_vh, $vh);
                $last_index++;
            }
            $last_index = array_key_last($retour_array);
            $page++;
        } else {
            $datas_find = FALSE;
        }
    }
}

var_dump($retour_array);











