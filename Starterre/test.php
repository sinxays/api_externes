<?php

include 'fonctions_starterre.php';

ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');
set_time_limit(300); // 300 secondes = 5 minutes, adapte selon tes besoins

$environnement = 'prod';

//MAJ DES VHS VENDUS AR ENTRE FIN FEVRIER ET MODIF CODE POUR PRENDRE EN COMPTE LES VENDU AR

$nbr_vhs_maj = 0;
$count_appel_api = 0;


// on recupere depuis la base tous les vhs à l'état 1 donc non vendus
$get_vhs_en_vente = get_vhs_starterre_from_csv();

//ensuite on boucle par fourchette 50/heure car on a une limite de 100 appels api kepler par heure..


foreach ($get_vhs_en_vente as $vh) {
    $id_kepler = $vh['id_kepler'];
    $id_starterre = $vh['id_starterre'];

    if ($count_appel_api > 50) {
        break; // On arrête si on a atteint la limite de 50 appels API
    }

    //pour chaque vh on va checker sur kepler son état ( vendu, vendu AR, sorti.. etc ) en gros on va juste le passer a state 0 
    // si son statut de kepler est différent de parc
    $state = get_state_from_reference_vh_kepler($id_kepler);
    $count_appel_api++;

    //si il est vendu, on actualise la base des deux cotés (starterre et base adminer)
    if ($state !== 'vehicle.state.parc') {
        post_vh_to_delete_starterre($id_starterre, $environnement);
        //passage du state à l'état 0 du vh 
        update_vh_state_replica_starterre($id_kepler, 0, $environnement);
        echo "le véhicule <strong>" . $id_kepler . "</strong> a bien été delete de starterre et actualisé en base";
    }

}





