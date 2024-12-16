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

        /***  Pour test sur un seul véhicule ***/
        $reference = '10yqbo5kb';
        $recup_kepler_for_starterre = recup_vh_unique_kepler_for_starterre($reference);
        var_dump($recup_kepler_for_starterre);
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

                        //on le post vers l'api STARTERRE , si le vh existe déja il sera juste updaté, si il n'existe pas il sera crée.
                        $retour = post_vh_to_starterre($retour_json);

                        //on crée le véhicule dans ma base pour avoir un replica base <> base starterre, mais il ne sera pas crée si il existe déja
                        if ($retour) {
                            // On check si le vh existe déja et qu'il est a l'état delete dans la base avant, car ça peut etre un bdc annulé finalement et donc le véhicule ressort en état parc à nouveau.
                            $check_vh = check_if_vh_exist($reference_kepler);

                            //si il existe pas alors on le crée
                            if (!$check_vh) {

                                create_vh_replica_starterre($reference_kepler, $retour['id_starterre'], $vh->licenseNumber, $vh->vin);
                                $nbr_vh_cree_starterre++;

                                sautdeligne();
                                echo "le véhicule crée porte l'identifiant partner kepler : " . $retour['id_partner'] . " || " . $vh->vin . " || " . $vh->licenseNumber;
                                sautdeligne();
                                echo "le véhicule crée porte l'id starterre : " . $retour['id_starterre'];
                                separateur();

                            }
                            //si il existe déja c'est qu'il a été placé sur BDC puis annulé, donc on repasse le vh a state 1 : parc
                            else {
                                update_vh_replica_starterre($reference_kepler, 1);
                            }
                        }


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

echo "nombre de vh crées : $nbr_vh_cree_starterre";
