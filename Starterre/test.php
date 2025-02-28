<?php

include 'fonctions_starterre.php';

ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');
set_time_limit(600); // 300 secondes = 5 minutes, adapte selon tes besoins


//PAGE TEST

// $recup_kepler_for_starterre = recup_vhs_kepler_for_starterre($parc, $page, 'arrivage');
// $recup_kepler_for_starterre = recup_vh_unique_kepler_for_starterre('3bj03lvyh');

// var_dump($recup_kepler_for_starterre);
// die();

//creer un array avec tous les parc pour faire la boucle par parc
$parc_array = array("CVO BOURGES", "CVO CLERMONT FERRAND", "CVO MASSY", "CVO ORLEANS sud", "CVO TROYES");

$array_for_csv = array();

$nbr_vhs_OK = 0;
$nbr_vhs_NO_OK = 0;

$nbr_vh_cree_starterre = 0;
foreach ($parc_array as $parc) {

    $page = 1;
    $datas_find = TRUE;

    // si plusieurs page, tant qu'on trouve des données on boucle
    while ($datas_find == TRUE) {

        $recup_kepler_for_starterre_parc = recup_vhs_kepler_for_starterre($parc, $page, 'parc');
        $recup_kepler_for_starterre_arrivage = recup_vhs_kepler_for_starterre($parc, $page, 'arrivage');

        $recup_kepler_for_starterre = array_merge($recup_kepler_for_starterre_parc, $recup_kepler_for_starterre_arrivage);
        // var_dump($recup_kepler_for_starterre);
        // die();

        // echo gettype($recup_kepler_for_starterre);
        // die();

        // si on trouve des données 
        if (!empty($recup_kepler_for_starterre)) {
            foreach ($recup_kepler_for_starterre as $nb_index_vh => $vh) {

                $reference_kepler = $vh->reference;

                //si le vh à un prix négociant HT on crée le véhicule
                if (isset($vh->priceSellerWithoutTax) && $vh->priceSellerWithoutTax !== '') {
                    //on met en forme les données

                    //UPDATE : si il ya un truc qui manque ou qui ne va pas pour starterre on s'embete pas on l'importe pas dans starterre.
                    $retour_json = mise_en_array_des_donnees_recup($array_for_csv, $nb_index_vh, $vh);

                    if ($retour_json) {

                        echo $reference_kepler . " OK";
                        sautdeligne();
                        $nbr_vhs_OK++;

                    } else {
                        $nbr_vhs_NO_OK++;
                    }

                }
            }
            $page++;
        } else {
            $datas_find = FALSE;
        }
    }
}


sautdeligne();
echo "nombre de vhs OK ==> $nbr_vhs_OK";
sautdeligne();
echo "nombre de vhs PAS OK ==> $nbr_vhs_NO_OK";