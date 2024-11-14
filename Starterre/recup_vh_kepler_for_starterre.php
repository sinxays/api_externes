<?php

include 'fonctions_starterre.php';

ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');
set_time_limit(300); // 300 secondes = 5 minutes, adapte selon tes besoins



//EXPORT STARTERRE

//creer un array avec tous les parc pour faire la boucle par parc
$parc_array = array("CVO BOURGES", "CVO CLERMONT FERRAND", "CVO MASSY", "CVO ORLEANS sud", "CVO TROYES");
// $parc_array = array("CVO ORLEANS sud");

$array_for_csv = array();

$nbr_vh_cree_starterre = 0;
foreach ($parc_array as $parc) {

    $page = 1;
    $datas_find = TRUE;

    // si plusieurs page, tant qu'on trouve des données on boucle
    while ($datas_find == TRUE) {
        // $recup_kepler_for_starterre = recup_vhs_kepler_for_starterre($parc, $page);
        // $recup_kepler_for_starterre = recup_vhs_arrivage_kepler_for_starterre($parc, $page);
        $recup_kepler_for_starterre = recup_vhs_vendus_kepler_for_starterre($parc, $page);

        /***  Pour test sur un seul véhicule ***/
        // $reference = '5nclw5pfj3';
        // $recup_kepler_for_starterre = recup_vh_unique_kepler_for_starterre($reference);
        // var_dump($recup_kepler_for_starterre);
        // die();

        // si on trouve des données 
        if (!empty($recup_kepler_for_starterre)) {
            foreach ($recup_kepler_for_starterre as $nb_index_vh => $vh) {
                //on met en forme les données
                // $retour_json = mise_en_array_des_donnees_recup($array_for_csv, $nb_index_vh, $vh);

                echo $vh->reference;
                echo " || " . $vh->vin;

                echo "<br/>";

                //on le post vers l'api STARTERRE
                // $retour = post_vh_to_starterre($retour_json);
                // $nbr_vh_cree_starterre += $retour;
            }
            $page++;
        } else {
            $datas_find = FALSE;
        }
    }
}

sautdeligne();

echo "nombre de vh crées : $nbr_vh_cree_starterre";
