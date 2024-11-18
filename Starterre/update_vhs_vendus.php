<?php

include 'fonctions_starterre.php';

ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');
set_time_limit(300); // 300 secondes = 5 minutes, adapte selon tes besoins



//UPDATE DE STARTERRE EN RECUPERANT LES VHS VENDUS

$nbr_vh_vendus_starterre = 0;

$recup_kepler_vhs_vendus_for_starterre = recup_vhs_vendus_kepler_for_starterre_bis();

/***  Pour test sur un seul véhicule ***/
// $reference = '5nclw5pfj3';
// $recup_kepler_for_starterre = recup_vh_unique_kepler_for_starterre($reference);
// var_dump($recup_kepler_for_starterre);
// die();

// si on trouve des données 
if (!empty($recup_kepler_vhs_vendus_for_starterre)) {
    foreach ($recup_kepler_vhs_vendus_for_starterre as $vh) {

        $reference_kepler = $vh->reference;

        // il on va chercher le idStarterre depuis le idKepler , car le delete se fait depuis le idStarterre
        $id_starterre = get_idStarterre_from_idKepler($reference_kepler);

        // si on trouve un idStarterre c'est qu'il est dans ma base
        if ($id_starterre) {
            //on le post en DELETE vers l'api STARTERRE
            $count = post_vh_to_delete_starterre($id_starterre);
            // et on actualise ma base pour que ça soit iso
            update_vh_replica_starterre($reference_kepler);

            //on affiche les données pour infos.
            echo $reference_kepler . " || " . $vh->vin . " || " . $vh->licenseNumber;
            sautdeligne();

            $nbr_vh_vendus_starterre++;
        }
    }
    sautdeligne();
    echo "nombre de vh vendus : $nbr_vh_vendus_starterre";
} else {
    sautdeligne();
    echo "Aucun véhicule vendu";
}
