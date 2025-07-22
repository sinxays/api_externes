<?php

include 'fonctions_starterre.php';

ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');
set_time_limit(600); // 300 secondes = 5 minutes, adapte selon tes besoins




//EXPORT STARTERRE


/*************************** BIEN MODIFIER L'ENVIRONNEMENT SI PASSAGE EN TEST OU EN PROD (dev ou prod) !!!  *******************************/
$environnement = 'prod';
/*************************** BIEN MODIFIER L'ENVIRONNEMENT SI PASSAGE EN TEST OU EN PROD (dev ou prod) !!!  *******************************/

// if ($environnement === 'prod') {
//     echo "<script>
//         if (!confirm('Vous êtes en mode PRODUCTION ! Voulez-vous continuer ?')) {
//             window.location.href = '../index.php'; // Redirige ou stoppe l'exécution
//         }
//     </script>";
// }


//creer un array avec tous les parc pour faire la boucle par parc
$parc_array = array("CVO BOURGES", "CVO CLERMONT FERRAND", "CVO MASSY", "CVO ORLEANS sud", "CVO TROYES", "CVO ARRIVAGE");
// $parc_array = array("CVO ARRIVAGE");

/***  Pour test sur un seul véhicule ***/
// $reference = '97o2iyl6pr';
// $recup_kepler_for_starterre = recup_vh_unique_kepler_for_starterre($reference);
// sautdeligne();
// sautdeligne();

// var_dump($recup_kepler_for_starterre);
// die();


$array_for_csv = array();

$nbr_vhs_OK = 0;
$nbr_vhs_NO_OK = 0;
$nbr_vhs_NO_prix_pro = 0;


$array_vhs_ok = array();
$array_vhs_no_ok = array();
$array_vhs_prix_pro_none = array();

$nbr_vh_cree_starterre = 0;
foreach ($parc_array as $parc) {

    $page = 1;
    $datas_find = TRUE;

    // si plusieurs page, tant qu'on trouve des données on boucle
    while ($datas_find == TRUE) {

        $recup_kepler_for_starterre_parc = recup_vhs_kepler_for_starterre($parc, $page, 'parc');
        $recup_kepler_for_starterre_arrivage = recup_vhs_kepler_for_starterre($parc, $page, 'arrivage');

        //on assemble les deux tableaux (parc et arrivage) en un 
        $recup_kepler_for_starterre = array_merge($recup_kepler_for_starterre_parc, $recup_kepler_for_starterre_arrivage);

        // si on trouve des données 
        if (!empty($recup_kepler_for_starterre)) {
            foreach ($recup_kepler_for_starterre as $nb_index_vh => $vh) {

                $reference_kepler = $vh->reference;

                //si le vh à un prix négociant/pro HT on crée le véhicule
                if (isset($vh->priceSellerWithoutTax) && $vh->priceSellerWithoutTax !== '') {
                    //on met en forme les données

                    $retour_json = mise_en_array_des_donnees_recup($array_for_csv, $nb_index_vh, $vh);

                    // si retour[state] == 1 alors OK
                    // si retour[state] == 0 alors on va alimenter le tableau des erreurs 
                    if ($retour_json['state'] == 1) {

                        //on le post vers l'api STARTERRE , si le vh existe déja il sera juste updaté, si il n'existe pas il sera crée.
                        $retour = post_vh_to_starterre($retour_json['datas'], $environnement);

                        $array_vhs_ok[$nbr_vhs_OK] = $reference_kepler;

                        //on crée le véhicule dans ma base pour avoir un replica base <> base starterre, mais il ne sera pas crée si il existe déja
                        if ($retour) {
                            // On check si le vh existe déja et qu'il est a l'état delete dans la base avant, car ça peut etre un bdc annulé finalement et donc le véhicule ressort en état parc à nouveau.
                            $check_vh = check_if_vh_exist($reference_kepler, $environnement);

                            //si il existe pas alors on le crée
                            if (!$check_vh) {

                                create_vh_replica_starterre($reference_kepler, $retour['id_starterre'], $vh->licenseNumber, $vh->vin, $environnement);
                                $nbr_vh_cree_starterre++;

                                sautdeligne();
                                echo "le véhicule crée porte l'identifiant partner kepler : " . $retour['id_partner'] . " || " . $vh->vin . " || " . $vh->licenseNumber;
                                sautdeligne();
                                echo "le véhicule crée porte l'id starterre : " . $retour['id_starterre'];
                                separateur();

                            }
                            //si il existe déja c'est qu'il a été placé sur BDC puis annulé donc state passé à 0 , donc on repasse le vh a state 1 : parc
                            else {
                                //on check si le vh est à l'état 0 donc placé sur BDC
                                if ($check_vh['state'] == 0) {

                                     // et on doit le reposter ? 
                                    $retour = post_vh_to_starterre($retour_json['datas'], $environnement);
                                    
                                    //on le repasse à 1 state parc et on met à jour son id_starterre car il a changé 
                                    update_vh_state_replica_starterre($reference_kepler, 1, $environnement, $retour['id_starterre']);

                                   
                                }

                            }
                        }
                        $nbr_vhs_OK++;
                    }
                    //si pas ok pour mise en forme 
                    else {
                        $array_vhs_no_ok[$nbr_vhs_NO_OK]['reference_kepler'] = $reference_kepler;
                        $array_vhs_no_ok[$nbr_vhs_NO_OK]['cause'] = $retour_json['detail_erreur'];
                        $nbr_vhs_NO_OK++;
                    }

                }
                //si pas de prix pro HT
                else {

                    // update 27/03 : on va voir si le véhicule a déja existé et dans ce cas c'est un véhicule qui est passé LAG par exemple , et donc Guillaume a retiré le prix pro
                    $check_vh = check_if_vh_exist($reference_kepler, $environnement);
                    if ($check_vh) {
                        $id_starterre = get_idStarterre_from_idKepler($reference_kepler, $environnement);
                        //on le supprime de starterre
                        $return_delete_starterre = post_vh_to_delete_starterre($id_starterre, $environnement);
                        if ($return_delete_starterre) {
                            //on le passe a state 0 dans ma base
                            update_vh_state_replica_starterre($reference_kepler, 0, $environnement);
                        }
                    }
                    // fin update 27/03 

                    $array_vhs_prix_pro_none[$nbr_vhs_NO_prix_pro] = $reference_kepler;
                    $nbr_vhs_NO_prix_pro++;
                }
            }
            $page++;
            // $datas_find = FALSE;
        } else {
            $datas_find = FALSE;
        }
    }
}


$texte_html_vhs_erreur = 'LISTE DE VHS EN ERREUR (' . count($array_vhs_no_ok) . ') : <br>';
$texte_html_vhs_no_prix_pro = 'LISTE DE VHS SANS PRIX PRO (' . count($array_vhs_prix_pro_none) . ') : <br>';

sautdeligne();

echo "nombre de vh crées : $nbr_vh_cree_starterre";

sautdeligne();
echo "nombre de vhs OK ==> $nbr_vhs_OK";
sautdeligne();
echo "LISTE de VH OK:";
sautdeligne();
if (!empty($array_vhs_ok)) {
    foreach ($array_vhs_ok as $vh_ok) {
        echo $vh_ok . "<br>";
    }
}

sautdeligne();
separateur();

echo "nombre de vhs PAS OK ==> $nbr_vhs_NO_OK";
sautdeligne();
echo "LISTE DE VHS EN ERREUR :";
sautdeligne();
if (!empty($array_vhs_no_ok)) {
    foreach ($array_vhs_no_ok as $vh_no_ok) {
        $detail_cause = '';
        foreach ($vh_no_ok['cause'] as $cause) {
            $detail_cause .= $cause . " |";
        }
        echo "<b style='color: red;'>" . $vh_no_ok['reference_kepler'] . "</b> > " . $detail_cause . " <br>";
        $texte_html_vhs_erreur .= $vh_no_ok['reference_kepler'] . " > " . $detail_cause . " <br>";
    }
}

sautdeligne();
separateur();

echo "nombre de vhs sans prix pro HT ==> $nbr_vhs_NO_prix_pro";
sautdeligne();
echo "LISTE DE VHS SANS PRIX PRO HT:";
sautdeligne();
if (!empty($array_vhs_prix_pro_none)) {
    foreach ($array_vhs_prix_pro_none as $vh_prix_none) {
        echo $vh_prix_none . "<br>";
        $texte_html_vhs_no_prix_pro .= $vh_prix_none . "<br>";
    }
}

//si il y a des véhicules en erreur ou des vhs sans prix pro
if (count($array_vhs_no_ok) >= 1 || count($array_vhs_prix_pro_none) >= 1) {
    $infos_graphmail = getInfosForAccesToken();

    $token = getAccessToken($infos_graphmail);

    $expediteur = 'portail@massoutre-locations.com';
    $to = ['sinxay.souvannavong@massoutre-locations.com', 'guillaume.honnert@massoutre-locations.com', 'humberto.alves@massoutre-locations.com'];
    // $to = 'guillaume.honnert@massoutre-locations.com';
    $objet = 'Rapport vhs en erreur pour Starterre';
    $body = $texte_html_vhs_erreur . " <br><br>" . $texte_html_vhs_no_prix_pro;

    sendEmail($token, $expediteur, $to, $objet, $body);
}


