<?php

include 'fonctions_starterre.php';

ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');
set_time_limit(300); // 300 secondes = 5 minutes, adapte selon tes besoins



//UPDATE DE STARTERRE EN RECUPERANT LES VHS VENDUS



$array_for_csv = array();

$nbr_vh_update_starterre = 0;

$recup_kepler_vhs_vendus_for_starterre = recup_vhs_vendus_kepler_for_starterre_bis();

/***  Pour test sur un seul véhicule ***/
// $reference = '5nclw5pfj3';
// $recup_kepler_for_starterre = recup_vh_unique_kepler_for_starterre($reference);
// var_dump($recup_kepler_for_starterre);
// die();

// si on trouve des données 
if (!empty($recup_kepler_vhs_vendus_for_starterre)) {
    foreach ($recup_kepler_vhs_vendus_for_starterre as $nb_index_vh => $vh) {
        //on met en forme les données
        // $retour_json = mise_en_array_des_donnees_recup($array_for_csv, $nb_index_vh, $vh);

        echo $vh->reference;
        echo " || " . $vh->vin;
        echo "<br/>";

        //on le post vers l'api STARTERRE
        // $retour = post_vh_to_starterre($retour_json);
        $nbr_vh_update_starterre++;
    }
    sautdeligne();
    echo "nombre de vh crées : $nbr_vh_update_starterre";
} else {
    sautdeligne();
    echo "Aucun véhicule vendu";
}
