<?php

include $_SERVER['DOCUMENT_ROOT'] . "/Starterre/fonctions_starterre.php";

echo $_SERVER['DOCUMENT_ROOT'] . "/Starterre/fonctions_starterre.php";

$environnement = 'prod';


if (isset($_POST)) {

    $id_kepler = trim($_POST['identifier_vh']);
    $type_recherche = $_POST['type_recherche'];
    switch ($type_recherche) {
        //RECHERCHE VH sur STARTERRE
        case "identifier_vh_kepler_from_starterre":
            $id_starterre = get_idStarterre_from_idKepler($id_kepler, $environnement);

            $datas_vh_starterre = starterre_recup_vh_by_identifier($id_starterre, $environnement);

            if (!empty($datas_vh_starterre)) {
                $result_final = $datas_vh_starterre;
            } else {
                $result_final = FALSE;
            }
            echo $result_final;
            break;


        case "identifier_vh_kepler_to_post_starterre":

            $array_for_csv = array();
            $datas_vh_for_starterre = recup_vh_unique_kepler_for_starterre($id_kepler);

            var_dump($datas_vh_for_starterre);

            die();

            if (!empty($datas_vh_for_starterre)) {

                foreach ($datas_vh_for_starterre as $nb_index_vh => $vh) {
                    //si le vh à un prix négociant HT on crée le véhicule
                    if (isset($vh->priceSellerWithoutTax) && $vh->priceSellerWithoutTax !== '') {
                        //UPDATE : si il ya un truc qui manque ou qui ne va pas pour starterre on s'embete pas on l'importe pas dans starterre.
                        $retour_json = mise_en_array_des_donnees_recup($array_for_csv, $nb_index_vh, $vh);
                    }
                }
            } else {
                $result_final = FALSE;
            }

            echo $result_final;
            break;

    }
}

