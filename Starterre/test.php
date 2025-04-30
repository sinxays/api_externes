<?php

include 'fonctions_starterre.php';

ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');
set_time_limit(300); // 300 secondes = 5 minutes, adapte selon tes besoins

$environnement = 'prod';

$chemin_fichier = "test.csv";
$vhs_to_delete_liste = get_infos_from_csv($chemin_fichier);
$liste_vhs_deleted = array();


foreach ($vhs_to_delete_liste as $vh_to_delete) {
    //si dans le fichier ya un id_kepler
    if ($vh_to_delete['id_kepler']) {
        $id_kepler = $vh_to_delete['id_kepler'];
        $id_starterre = get_idStarterre_from_idKepler($vh_to_delete['id_kepler'], $environnement);
    }
    //si ya pas de id kepler alors ona  aau moins un VIN quand meme sur la 3eme colonne
    else {
        //on va chercher le id starterre depuis le VIN 
        $id_starterre = get_idStarterre_from_VIN($vh_to_delete['vin'], $environnement);
        $id_kepler = get_idKepler_from_VIN($vh_to_delete['vin'], $environnement);
    }

    //suppression sur starterre via post en delete
    // post_vh_to_delete_starterre($id_starterre, $environnement);
    //passage du state à l'état 0 du vh 
    // update_vh_state_replica_starterre($id_kepler, 0, $environnement);
    array_push($liste_vhs_deleted,$id_kepler);
}

foreach($liste_vhs_deleted as $vh_deleted){
    echo "le véhicule de référence <b>$vh_deleted</b> a été delete";
    sautdeligne();
}


