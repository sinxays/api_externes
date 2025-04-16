<?php

use App\Connection;

include __DIR__ . '/../pdo.php';




ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);





function starterre_recup_vh_by_identifier($identifier_vh, $environnement)
{

    $url = set_url_environnement_starterre_vh($environnement);
    // $url = "https://cameleon.starterre.dev/api/vehicles";

    $data = array(
        "id" => $identifier_vh,
    );

    // d'apres la doc cameleon, pour récupérer un véhicule il faut faire /api/vehicles/{id} donc on colle direct l'id derriere l'url
    $full_url = $url . "/" . $identifier_vh;

    // Initialiser une session cURL
    $ch = curl_init();

    // le token
    $token = use_token_from_base('prod');

    // Configuration de la requête cURL
    curl_setopt($ch, CURLOPT_URL, $full_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Retourner la réponse sous forme de chaîne
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $token"
    ]);
    // Désactiver la vérification SSL (à utiliser en développement seulement)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Exécuter la requête
    $response = curl_exec($ch);

    // Fermer la session cURL
    curl_close($ch);

    return $response;
}

// RECUPERER LE TOKEN 
function goCurlToken($url)
{


    $ch2 = curl_init();

    curl_setopt($ch2, CURLOPT_URL, $url);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, "apiKey=54c4fbe3ee0ed3683a17488371d5e762b9c5f4db6a7bc0507d3518c40bedfe300fbece014e3eac21acd380a142e874c460931659fe922bc1ef170d1f325e499c");

    $result = curl_exec($ch2);

    curl_close($ch2);

    $data = json_decode($result, true);

    return $data['value'];
}




// JSON TO CSV
function array2csv($data, $delimiter = ';', $enclosure = '"', $escape_char = "\\")
{
    $f = fopen('test_datas_bdc.csv', 'wr+');
    $entete[] = ["N° Bon Commande", "Immatriculation", "RS/Nom Acheteur", "Prix Vente HT", "Prix de vente TTC", "Vendeur Vente", "date du dernier BC", "Destination de sortie", "VIN"];

    //entete
    foreach ($entete as $item) {
        fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
    }

    foreach ($data as $item) {
        fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
    }
    rewind($f);
    return stream_get_contents($f);
}




// SAUT DE LIGNE 
function sautdeligne()
{
    echo "<br/>";
    echo "<br/>";
}



function get_token()
{
    $url_token = "https://www.kepler-soft.net/api/v3.0/auth-token/";

    $ch2 = curl_init();

    curl_setopt($ch2, CURLOPT_URL, $url_token);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, "apiKey=54c4fbe3ee0ed3683a17488371d5e762b9c5f4db6a7bc0507d3518c40bedfe300fbece014e3eac21acd380a142e874c460931659fe922bc1ef170d1f325e499c");

    $result = curl_exec($ch2);

    curl_close($ch2);

    $data = json_decode($result, true);

    return $data['value'];
}

function get_token_starterre($environnement)
{
    // Définir l'URL de l'API pour l'authentification
    switch ($environnement) {
        case 'prod':
            $url = "https://cameleon.starterre.fr/auth";
            break;
        case 'dev':
            $url = "https://cameleon.starterre.dev/auth";
            break;
    }


    // Préparer les données du body (le payload JSON)
    switch ($environnement) {
        case 'prod':
            $data = array(
                "email" => "sinxay.souvannavong@massoutre-locations.com",
                "password" => "wUK8aqXcTlxqQ8!M"
            );
            break;
        case 'dev':
            $data = array(
                "email" => "guillaume.honnert@massoutre-locations.com",
                "password" => "KXpOmwlRolW8%mAl"
            );
            break;
    }

    // Convertir le tableau en format JSON
    $json_data = json_encode($data);

    // Initialiser une session cURL
    $ch = curl_init();

    // Configuration de la requête cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Retourner la réponse sous forme de chaîne
    curl_setopt($ch, CURLOPT_POST, true);            // Indiquer qu'il s'agit d'une requête POST
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); // indique que c'est du JSON
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data); // Envoyer les données en JSON


    // Désactiver la vérification SSL (à utiliser en développement seulement)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Exécuter la requête
    $response = curl_exec($ch);

    // Fermer la session cURL
    curl_close($ch);

    // Traiter la réponse de l'API
    if ($response === false) {
        echo "Erreur dans la requête cURL. get_token_starterre";
        die();
    } else {
        // Convertir la réponse JSON en tableau PHP
        $response_data = json_decode($response, true);

        // Vérifier si le token est bien présent dans la réponse
        if (isset($response_data['token'])) {
            $token = $response_data['token'];
            // echo "Token JWT obtenu : " . $token;
            // separateur();
            return $token;
        } else {
            echo "Erreur : le token n'a pas été obtenu.";
            print_r($response_data);  // Pour déboguer la réponse
            die();
        }
    }
}


function get_vhs_starterre_from_base($environnement, $id_kepler = '')
{

    $pdo = set_environnement_pdo($environnement);

    $request = $pdo->query("SELECT id_kepler,id_starterre FROM vehicules");
    $result_vhs = $request->fetchAll(PDO::FETCH_ASSOC);

    return $result_vhs;
}

function get_infos_from_csv($chemin_fichier)
{


    // Ouvrir le fichier en mode lecture
    if (($handle = fopen($chemin_fichier, "r")) !== FALSE) {
        // Ignorer la première ligne (en-têtes)
        fgetcsv($handle);

        // Lire chaque ligne du fichier CSV
        $i = 0;
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            // Afficher chaque ligne (tableau)
            // print_r($data);
            $array[$i]['id_kepler'] = $data[1];
            $array[$i]['id_starterre'] = $data[2];
            $i++;
        }
        // Fermer le fichier après lecture
        fclose($handle);

        return $array;


    } else {
        echo "Impossible d'ouvrir le fichier.";
    }
}






function recup_vhs_kepler_for_starterre($parc, $page, $state)
{
    // le token
    $token = get_token();


    // parc || arrivage 
    switch ($state) {
        case 'parc':
            $conditions['state'] = 'vehicle.state.parc';
            $conditions['isNotAvailableForSelling'] = FALSE;
            break;

        case 'arrivage':
            $conditions['state'] = 'vehicle.state.on_arrival';
            $conditions['isNotAvailableForSelling'] = FALSE;
            break;

        default:
            $conditions['state'] = 'vehicle.state.parc';
            $conditions['isNotAvailableForSelling'] = FALSE;
            break;
    }


    $dataArray = array(
        "state" => $conditions['state'],
        "isNotAvailableForSelling" => $conditions['isNotAvailableForSelling'],
        "fleet" => $parc,
        "count" => 100,
        "page" => $page
    );


    $request_vehicule = "v3.7/vehicles/";
    $url = "https://www.kepler-soft.net/api/";


    $url_vehicule = $url . "" . $request_vehicule;


    $data = http_build_query($dataArray);


    $getURL = $url_vehicule . '?' . $data;


    // print_r($getURL);
    // sautdeligne();


    $ch = curl_init();
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';


    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);


    $result = curl_exec($ch);


    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }


    // var_dump(gettype($result));
    // print_r($result);

    curl_close($ch);

    // créer un objet à partir du retour qui est un string
    $obj_vehicule = json_decode($result);

    //on prend l'object qui est dans l'array
    $return = $obj_vehicule;
    // on retourne un objet

    return $return;
}


function recup_vhs_vendus_kepler_for_starterre($vendu_AR = '')
{
    // le token
    $token = get_token();

    // date du jour
    $currentDateTime = date('Y-m-d');

    if ($vendu_AR !== '') {
        $dataArray = array(
            "state" => 'vehicle.state.sold_ar',
            "isNotAvailableForSelling" => FALSE,
            "dateUpdatedStart" => $currentDateTime,
            "count" => 100
        );
    } else {
        $dataArray = array(
            "state" => 'vehicle.state.sold',
            "isNotAvailableForSelling" => FALSE,
            "dateUpdatedStart" => $currentDateTime,
            "count" => 100
        );
    }



    $request_vehicule = "v3.7/vehicles/";
    $url = "https://www.kepler-soft.net/api/";

    $url_vehicule = $url . "" . $request_vehicule;

    $data = http_build_query($dataArray);

    $getURL = $url_vehicule . '?' . $data;

    // print_r($getURL);
    // sautdeligne();

    $ch = curl_init();
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }
    curl_close($ch);

    // var_dump(gettype($result));
    // print_r($result);

    // créer un objet à partir du retour qui est un string
    $obj_vehicule = json_decode($result);

    //on prend l'object qui est dans l'array
    $return = $obj_vehicule;
    // on retourne un objet
    // var_dump($return);
    return $return;
}


function recup_vhs_arrivage_kepler_for_starterre($parc, $page)
{
    // le token
    $token = get_token();


    $dataArray = array(
        "state" => 'vehicle.state.on_arrival',
        "isNotAvailableForSelling" => TRUE,
        "fleet" => $parc,
        "count" => 100,
        "page" => $page
    );


    $request_vehicule = "v3.7/vehicles/";
    $url = "https://www.kepler-soft.net/api/";


    $url_vehicule = $url . "" . $request_vehicule;


    $data = http_build_query($dataArray);


    $getURL = $url_vehicule . '?' . $data;


    // print_r($getURL);
    // sautdeligne();


    $ch = curl_init();
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';


    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);


    $result = curl_exec($ch);


    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }


    // var_dump(gettype($result));
    // print_r($result);


    curl_close($ch);


    // créer un objet à partir du retour qui est un string
    $obj_vehicule = json_decode($result);


    //on prend l'object qui est dans l'array
    $return = $obj_vehicule;
    // on retourne un objet


    // var_dump($return);


    return $return;
}



function recup_vh_unique_kepler_for_starterre($reference)
{
    // le token
    $token = get_token();

    // var_dump($token);

    $dataArray = array(
        "reference" => $reference,
        "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.pending,vehicle.state.out,vehicle.state.out_ar,vehicle.state.canceled,vehicle.state.on_arrival,vehicle.state.parc',
        "isNotAvailableForSelling" => FALSE
    );

    $request_vehicule = "v3.7/vehicles/";
    $url = "https://www.kepler-soft.net/api/";

    $url_vehicule = $url . "" . $request_vehicule;

    $data = http_build_query($dataArray);

    $getURL = $url_vehicule . '?' . $data;

    // print_r($getURL);
    // sautdeligne();

    $ch = curl_init();
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }

    // var_dump(gettype($result));
    // print_r($result);

    curl_close($ch);

    var_dump($result);

    // créer un objet à partir du retour qui est un string
    $obj_vehicule = json_decode($result);

    //on prend l'object qui est dans l'array
    $return = $obj_vehicule;
    // on retourne un objet

    return $return;
}


function mise_en_array_des_donnees_recup($array_for_csv, $nb_index_vh, $vh)
{
    $no_export = FALSE;
    $no_export_cause_array = array();


    $array_for_csv["partner-vehicle-identifier"] = isset($vh->reference) ? $vh->reference : "";
    $array_for_csv["is-used"] = true;
    $array_for_csv["is-executive"] = false;
    $array_for_csv["seat-capacity"] = isset($vh->seats) ? $vh->seats : 0;
    $array_for_csv["door-number"] = isset($vh->doors) ? $vh->doors : 0;


    if (isset($vh->horsepower) && !is_null($vh->horsepower)) {
        $power_kw = calcul_power_kw($vh->horsepower);
        $array_for_csv["power-kw"] = $power_kw;
        $array_for_csv["power-horsepower"] = $vh->horsepower;
    } else {
        sautdeligne();
        // echo "pas de cv renseigné";
        array_push($no_export_cause_array, "Erreur: pas de cv renseigné");
        $no_export = TRUE;
    }


    $array_for_csv["mileage"] = isset($vh->distanceTraveled) ? $vh->distanceTraveled : 0;
    if ($vh->distanceTraveled < 50) {
        $array_for_csv["is-used"] = false;
    }


    $array_for_csv["origin"] = isset($vh->origin) ? get_origin_vh_for_starterre($vh->origin) : "";
    $array_for_csv["co2-emission"] = isset($vh->extraUrbanKmConsumption) ? $vh->extraUrbanKmConsumption : 0;
    $array_for_csv["co2-emission-type"] = "WLTP";
    $array_for_csv["transmission"] = "FWD";
    $array_for_csv["origin-country"]["code"] = isset($vh->country) ? $vh->country : "";
    $array_for_csv["national-type"] = isset($vh->vehicleType->name) ? $vh->vehicleType->name : "";
    $array_for_csv["body-type"] = isset($vh->bodywork->name) ? set_bodywork_name_for_starterre($vh->bodywork->name) : "SEDAN";
    $array_for_csv["fuel"] = isset($vh->energy->name) ? get_energy_vh_for_starterre($vh->energy->name) : "N/A";
    if ($array_for_csv["fuel"] == "N/A") {
        sautdeligne();
        // echo "Erreur energie faussé ou manquant";
        array_push($no_export_cause_array, "Erreur: energie faussé ou manquant");
        $no_export = TRUE;
    }
    $array_for_csv["gearbox"] = isset($vh->gearbox->name) ? get_gearbox_vh_for_starterre($vh->gearbox->name) : "";
    $array_for_csv["gear-number"] = isset($vh->reportNumber) ? $vh->reportNumber : "N/A";
    if ($array_for_csv["gear-number"] == "N/A") {
        sautdeligne();
        // echo "Erreur nombre de rapport non renseigné";
        array_push($no_export_cause_array, "Erreur: nombre de rapport non renseigné");
        $no_export = TRUE;
    }
    $array_for_csv["brand"]["name"] = isset($vh->brand->name) ? trim(strtolower($vh->brand->name)) : "";


    $array_for_csv["prices"][0]["price-without-taxes"] = isset($vh->priceSellerWithoutTax) ? floatval($vh->priceSellerWithoutTax) : 0;
    $array_for_csv["prices"][0]["constructor-price-without-taxes"] = 0; // pas l'info sur kepler
    $array_for_csv["prices"][0]["estimated-costs-without-taxes"] = isset($vh->estimateCost) ? floatval($vh->estimateCost) : 0;
    $array_for_csv["prices"][0]["price-type"] = "prix_marchand_starterre";
    $array_for_csv["prices"][0]["currency"] = "EUR";
    $array_for_csv["prices"][0]["sold-as-is"] = true;
    $array_for_csv["prices"][0]["sold-under-delegation"] = false;
    $array_for_csv["prices"][0]["sold-with-taxes"] = true;


    $array_for_csv["identification-number"] = isset($vh->vin) ? $vh->vin : "";
    $array_for_csv["release-year"] = isset($vh->year) ? intval($vh->year) : 0;
    $array_for_csv["power-tax"] = isset($vh->taxHorsepower) ? $vh->taxHorsepower : 0;
    $array_for_csv["date-first-registration"] = isset($vh->dateOfDistribution) ? $vh->dateOfDistribution : "0000-00-00";
    $array_for_csv["natcode"] = isset($vh->version->nationalityCode) ? intval($vh->version->nationalityCode) : null;
    $array_for_csv["model"] = isset($vh->model->name) ? $vh->model->name : "";
    $array_for_csv["configuration"] = isset($vh->version->name) ? $vh->version->name : "";

    //la couleur ne doit pas dépasser 50 caracteres
    if (isset($vh->color->name)) {
        $color_name = $vh->color->name;
        if (strlen($color_name) > 50) {
            $array_for_csv["external-commercial-color"] = "N/C";
        } else {
            $array_for_csv["external-commercial-color"] = $color_name;
        }
    }



    // caractéristiques du VH : DIMENSIONS

    //changer la valeur de unloadedweight : ajouter 75kg au poids hors passager pour avoir le poids empty_weight
    if (isset($vh->unloadedWeight)) {
        $array_for_details_dimensions = ["height" => 'HEIGHT', "length" => 'LENGTH', "width" => 'WIDTH', "unloadedWeight" => "EMPTY_WEIGHT"];
        $vh->unloadedWeight = perso_empty_weight($vh->unloadedWeight);
    } else {
        $array_for_details_dimensions = ["height" => 'HEIGHT', "length" => 'LENGTH', "width" => 'WIDTH'];
    }

    $i = 0;
    foreach ($array_for_details_dimensions as $key => $detail) {
        $array_for_csv["details"][0]["language"]["code"] = "fr";
        $array_for_csv["details"][0]["caracteristics"][$i]["value"] = isset($vh->$key) ? strval($vh->$key) : "";
        $array_for_csv["details"][0]["caracteristics"][$i]["category"] = "DIMENSIONS";
        $array_for_csv["details"][0]["caracteristics"][$i]["type"] = $detail;
        $i++;
    }

    // caractéristiques du VH : cylindrée
    if (isset($vh->carEngine) && $vh->carEngine !== "") {
        $array_for_csv["details"][0]["language"]["code"] = "fr";
        //si c'est un vh électrique alors on met la cylindrée à 1
        if ($array_for_csv["fuel"] == 'EL' || $array_for_csv["fuel"] == 'EH') {
            $array_for_csv["details"][0]["caracteristics"][$i]["value"] = '1';
        } else {
            $array_for_csv["details"][0]["caracteristics"][$i]["value"] = strval($vh->carEngine);
        }
        $array_for_csv["details"][0]["caracteristics"][$i]["category"] = "MOTOR";
        $array_for_csv["details"][0]["caracteristics"][$i]["type"] = "ENGINE_CAPACITY";
        $i++;
    } else {
        sautdeligne();
        // echo "Erreur Cylindrée non renseignée";
        array_push($no_export_cause_array, "Erreur: Cylindrée non renseignée");
        $no_export = TRUE;
    }

    //si véhicule électrique , kepler ne sort pas la distance maximale possible à 100% , donc je mets 0
    if ($array_for_csv["fuel"] == 'EL') {
        $array_for_csv["details"][0]["language"]["code"] = "fr";
        $array_for_csv["details"][0]["caracteristics"][$i]["value"] = "0";
        $array_for_csv["details"][0]["caracteristics"][$i]["category"] = "PERFORMANCE_CONSUMPTION";
        $array_for_csv["details"][0]["caracteristics"][$i]["type"] = "BATTERY_RANGE_WLTP";
    }



    $i = 0;
    // equipements du véhicule de série
    if (isset($vh->equipmentStandard) && !empty($vh->equipmentStandard)) {
        $array_equipements_serie = $vh->equipmentStandard;
        foreach ($array_equipements_serie as $equipement) {
            $array_for_csv["details"][0]["equipments"][$i]["label"] = $equipement->name;
            $array_for_csv["details"][0]["equipments"][$i]["price"] = isset($equipement->price) ? floatval($equipement->price) : 0;
            $array_for_csv["details"][0]["equipments"][$i]["is-standard"] = true;
            $array_for_csv["details"][0]["equipments"][$i]["is-missing"] = false;
            $array_for_csv["details"][0]["equipments"][$i]["category"] = "OTHER";
            $i++;
        }
    } else {
        sautdeligne();
        // echo "Erreur pas d'équipements de série";
        array_push($no_export_cause_array, "Erreur: Erreur pas d'équipements de série");
        $no_export = TRUE;
    }

    if (isset($vh->equipmentOptional) && !empty($vh->equipmentOptional)) {
        //equipement en options
        $array_equipements_option = $vh->equipmentOptional;

        foreach ($array_equipements_option as $equipement) {
            $array_for_csv["details"][0]["equipments"][$i]["label"] = $equipement->name;
            $array_for_csv["details"][0]["equipments"][$i]["price"] = isset($equipement->price) ? floatval($equipement->price) : 0;
            $array_for_csv["details"][0]["equipments"][$i]["is-standard"] = false;
            $array_for_csv["details"][0]["equipments"][$i]["is-missing"] = false;
            $array_for_csv["details"][0]["equipments"][$i]["category"] = "OTHER";
            $i++;
        }
    }
    // else {
    //     sautdeligne();
    //     echo "Erreur pas d'équipements optionnels";
    //     $no_export = TRUE;
    // }


    if (isset($vh->gallery) && !empty($vh->gallery)) {
        // PHOTOS
        foreach ($vh->gallery as $id_photo => $photo) {
            $array_for_csv["photos"][$id_photo]["url"] = $photo->photo;
            $array_for_csv["photos"][$id_photo]["camera-work"] = "OTHER";
            $array_for_csv["photos"][$id_photo]["order"] = $id_photo + 1;
            if ($id_photo == 0) {
                $array_for_csv["photos"][$id_photo]["is-main"] = true;
            } else {
                $array_for_csv["photos"][$id_photo]["is-main"] = false;
            }
        }
    } //si pas de photos alors on exporte pas 
    else {
        sautdeligne();
        // echo "Erreur pas de photos";
        array_push($no_export_cause_array, "Erreur: Pas de photos");
        $no_export = TRUE;
    }


    $array_for_csv["documents"][0]["url"] = null;
    $array_for_csv["documents"][0]["type"] = "MAINTENANCE";


    $array_for_csv["avaibility"]["avaibility"] = "STOCK";


    $array_for_csv["warranty"]["type"] = "MANUFACTURER";

    $cvo = $vh->fleet;

    // si ce ne sont pas des vhs stellantis on prend l'adresses des CVO
    if ($cvo !== 'CVO ARRIVAGE') {
        $adresse = get_adress_vh_for_post_starterre($vh->fleet);

        $array_for_csv["address"][0]["type"] = "STORAGE";
        $array_for_csv["address"][0]["street-number"] = $adresse["num_rue"];
        $array_for_csv["address"][0]["street-name"] = $adresse["rue"];
        $array_for_csv["address"][0]["postcode"] = $adresse["cp"];
        $array_for_csv["address"][0]["city"] = $adresse["ville"];
        $array_for_csv["address"][0]["country"]["code"] = $adresse["pays"];
    }
    //sinon on prend l'endroit du stockage avec les Notes sites privé de l'onglet note de kepler
    else {
        if (isset($vh->notes->website)) {
            $notes = $vh->notes->website;
            $adresse = get_adresse_from_notes_vh_kepler($notes);
            $array_for_csv["address"][0]["type"] = "STORAGE";
            $array_for_csv["address"][0]["street-number"] = $adresse['numero_rue'];
            $array_for_csv["address"][0]["street-name"] = $adresse['nom_rue'];
            $array_for_csv["address"][0]["postcode"] = $adresse['code_postal'];
            $array_for_csv["address"][0]["city"] = $adresse['ville'];
            $array_for_csv["address"][0]["country"]["code"] = $adresse['code_pays'];
        }
    }



    // UPDATE : si on une donnée manquante ou non conventionelle pour starterre on post pas vers starterre
    if ($no_export) {
        sautdeligne();
        echo "véhicule $vh->reference no export";
        separateur();
        $return['state'] = 0;
        $return['detail_erreur'] = $no_export_cause_array;
        return $return;
    } else {
        $json_data_vh = json_encode($array_for_csv);
        // sautdeligne();
        // var_dump($json_data_vh);
        $return['state'] = 1;
        $return['datas'] = $json_data_vh;
        return $return;
    }


}

function post_vh_to_starterre($donnees_vh_to_post, $environnement)
{
    // Initialiser une session cURL
    $ch = curl_init();

    $url = set_url_environnement_starterre_vh($environnement);

    $token = use_token_from_base($environnement);

    // Configuration de la requête cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Retourner la réponse sous forme de chaîne
    curl_setopt($ch, CURLOPT_POST, true);            // Indiquer qu'il s'agit d'une requête POST

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $token" // Remplace par ton token d'authentification
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $donnees_vh_to_post);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactiver la vérification SSL (à utiliser en développement seulement)

    // Exécuter la requête et obtenir la réponse
    $response = curl_exec($ch);

    // Fermer la session cURL
    curl_close($ch);

    // Traiter la réponse de l'API
    if ($response === false) {
        echo "Erreur dans la requête cURL.post_vh_to_starterre";
    } else {
        // Convertir la réponse JSON en tableau PHP
        $response_data = json_decode($response, true);

        // Vérifier si le idstarterre est bien présent dans la réponse
        if (isset($response_data['partner-vehicle-identifier'])) {
            $identifier_vh = $response_data['partner-vehicle-identifier'];
            $id_vh_starterre = $response_data['id'];

            // sautdeligne();
            // echo "le véhicule crée porte l'identifiant partner kepler : " . $identifier_vh;
            // sautdeligne();
            // echo "le véhicule crée porte l'id starterre : " . $id_vh_starterre;
            // separateur();

            $retour_array = array(
                "id_partner" => $identifier_vh,
                "id_starterre" => $id_vh_starterre
            );
            return $retour_array;
        } else {
            echo "Erreur : le véhicule n'a pas été crée.";
            var_dump($donnees_vh_to_post);
            sautdeligne();
            print_r($response_data);  // Pour déboguer la réponse
            separateur();
            return null;
        }
    }
}

function post_vh_to_delete_starterre($id_starterre, $environnement)
{

    // Initialiser une session cURL
    $ch = curl_init();

    $url_environement = set_url_environnement_starterre_vh($environnement);

    $url = $url_environement . "/" . $id_starterre;

    $token = use_token_from_base($environnement);
    // var_dump($token);

    // $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MzEwODA3NDMsImV4cCI6MTczMTA4NDM0Mywicm9sZXMiOlsiUk9MRV9QT1NUX1ZFSElDTEUiLCJST0xFX0RFTEVURV9WRUhJQ0xFIiwiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiZ3VpbGxhdW1lLmhvbm5lcnRAbWFzc291dHJlLWxvY2F0aW9ucy5jb20ifQ.PMzcKGxK-Uh1dMZ8cteBJYHVXMAhSMitCMHNIRZoekBjRfVSq1Yck1Qj7RPTJF5tCRdjhjgOJ4Z9lipyE8iDkk2KepO15MR2BdpmPqa9eTtEIPuNTgAz2JGxnfrwmfdZTwlle0-VVK6WwCso_20gGCVd35pxVKWoqnalx2G8cV88kuSYjq_awybc8jSO3vJU-KyysXpKKWaQ8nIkurGBfUf71R-gFZFTxhkpROr8h_76XsrijWcS0LuO4rSyuvtwluAAy3JMBvL8bEIiH-x9UCmxE4V5S0d-SIN9LIgqmNs6XjbSJmmweE0tsKHIigiWQQSttO_SyLeGv-G8bTUs5Q";

    // Configuration de la requête cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Retourner la réponse sous forme de chaîne
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); // Définit la méthode HTTP sur DELETE

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $token" // Remplace par ton token d'authentification
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactiver la vérification SSL (à utiliser en développement seulement)

    // Exécuter la requête et obtenir la réponse
    $response = curl_exec($ch);

    // Fermer la session cURL
    curl_close($ch);

    // Traiter la réponse de l'API
    if ($response === false) {
        return null;
    } else {
        return 1;
    }
}
function create_vh_replica_starterre($id_kepler, $id_starterre, $immatriculation, $vin, $environnement)
{
    $pdo = set_environnement_pdo($environnement);

    // Définir le fuseau horaire à Paris
    date_default_timezone_set('Europe/Paris');
    $currentDateTime = date('Y-m-d H:i:s');

    $data = [
        'id_kepler' => $id_kepler,
        'id_starterre' => $id_starterre,
        'immatriculation' => $immatriculation,
        'vin' => $vin,
        'state' => 1,
        'date_insert_starterre' => $currentDateTime,
    ];

    $sql = "INSERT INTO vehicules 
    (id_kepler, id_starterre, immatriculation,vin,state,date_insert_starterre)
    VALUES (:id_kepler, :id_starterre, :immatriculation, :vin, :state, :date_insert_starterre)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
}

function update_vh_state_replica_starterre($id_kepler, $state, $environnement)
{

    $pdo = set_environnement_pdo($environnement);

    // Définir le fuseau horaire à Paris
    date_default_timezone_set('Europe/Paris');
    $currentDateTime = date('Y-m-d H:i:s');


    switch ($state) {
        // si c'est un véhicule qui passe à l'état vendu donc sur BDC
        case 0:
            $data = [
                'id_kepler' => $id_kepler,
                'date_delete_starterre' => $currentDateTime,
                'state' => $state
            ];

            $sql = "UPDATE vehicules SET state = :state, date_delete_starterre = :date_delete_starterre WHERE id_kepler = :id_kepler";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            break;
        // si c'est un véhicule qui repasse à l'état parc
        case 1:
            $data = [
                'id_kepler' => $id_kepler,
                'state' => $state
            ];

            $sql = "UPDATE vehicules SET state = :state WHERE id_kepler = :id_kepler";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            break;

    }


}


function check_if_vh_exist($reference_kepler, $environnement)
{
    $pdo = set_environnement_pdo($environnement);

    $request = $pdo->query("SELECT * from vehicules WHERE id_kepler = '$reference_kepler'");
    $result = $request->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result;
    } else {
        return null;
    }
}

function check_state_vh($reference_kepler)
{

    $pdo = Connection::getPDO_starterre_prod();

    $request = $pdo->query("SELECT state from vehicules WHERE id_kepler = '$reference_kepler'");
    $result = $request->fetch(PDO::FETCH_COLUMN);

    if ($result == 0) {
        return null;
    } else {
        return true;
    }
}

function get_idStarterre_from_idKepler($id_kepler, $environnement)
{
    $pdo = set_environnement_pdo($environnement);

    $request = $pdo->query("SELECT id_starterre FROM vehicules WHERE id_kepler = '$id_kepler'");
    $result = $request->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result['id_starterre'];
    } else {
        return null;
    }

}


function calcul_power_kw($powerhorse)
{
    $power_kw = $powerhorse * 0.736;
    return intval($power_kw);
}

function get_origin_vh_for_starterre($origin_kepler)
{
    if (strpos($origin_kepler, "LOUEUR") !== false) {
        $origin_starterre = "LEASING";
    } else if (strpos($origin_kepler, "ECOLE") !== false) {
        $origin_starterre = "SCHOOL_VEHICLE";
    } else {
        $origin_starterre = "STANDARD";
    }

    return $origin_starterre;
}

function get_energy_vh_for_starterre($energy_vh_kepler)
{
    switch (strtolower($energy_vh_kepler)) {
        case 'essence sans plomb':
        case 'essence':
        case 'essence plomb':
            $energy_for_starterre = "ES";
            break;
        case 'diesel':
            $energy_for_starterre = "GO";
            break;
        case 'gpl':
            $energy_for_starterre = "GP";
            break;
        case 'courant electrique':
        case 'electrique':
            $energy_for_starterre = "EL";
            break;

        case 'essence / courant electrique':
        case 'hybride':
        case 'hybrid':
            $energy_for_starterre = "EH";
            break;

        default:
            $energy_for_starterre = "N/A";
            break;
    }

    return $energy_for_starterre;
}

function get_gearbox_vh_for_starterre($gearbox_vh_kepler)
{
    switch ($gearbox_vh_kepler) {
        case 'Automate a fonct. Continu':
        case 'Boite automatique':
        case 'Automate sequentiel':
        case 'Boite automatisee':
            $gearbox_for_starterre = "AUTOMATIC";
            break;

        case 'Boite manuelle':
            $gearbox_for_starterre = "MANUAL";
            break;

        default:
            $gearbox_for_starterre = "MANUAL";
            break;
    }

    return $gearbox_for_starterre;
}

function get_adress_vh_for_post_starterre($parc)
{


    switch (strtoupper($parc)) {
        case 'CVO CLERMONT FERRAND':
            $adresse["rue"] = "Rue Bernard Palissy";
            $adresse["num_rue"] = "3";
            $adresse["cp"] = "63000";
            $adresse["ville"] = "Clermont-Ferrand";
            break;
        case 'CVO BOURGES':
            $adresse["rue"] = "Chemin de la Prairie";
            $adresse["num_rue"] = "11";
            $adresse["cp"] = "18000";
            $adresse["ville"] = "Bourges";
            break;
        case 'CVO MASSY':
            $adresse["rue"] = "Rue du buisson aux fraises";
            $adresse["num_rue"] = "1";
            $adresse["cp"] = "91300";
            $adresse["ville"] = "Massy";
            break;
        case 'CVO ORLEANS SUD':
            $adresse["rue"] = "Rue de la Bergeresse";
            $adresse["num_rue"] = "845";
            $adresse["cp"] = "45160";
            $adresse["ville"] = "Olivet";
            break;
        case 'CVO TROYES':
            $adresse["rue"] = "Rue Bonnetières";
            $adresse["num_rue"] = "4";
            $adresse["cp"] = "10600";
            $adresse["ville"] = "La Chapelle Saint Luc";
            break;

        default:
            $adresse["rue"] = "Rue";
            $adresse["num_rue"] = "1";
            $adresse["cp"] = "00000";
            $adresse["ville"] = "Ville";
            break;
    }

    $adresse["pays"] = "FR";
    return $adresse;
}

function separateur()
{
    sautdeligne();
    echo "________________________________________________________________________________________________________";
    sautdeligne();
}


function use_token_from_base($environnement)
{

    $pdo = set_environnement_pdo($environnement);

    $request = $pdo->query("SELECT token,date_creation FROM token WHERE ID = 1");
    $token_starterre = $request->fetch(PDO::FETCH_ASSOC);

    //si on trouve un token 
    if ($token_starterre) {
        //on voit la durée de création du token 
        $time_token_created = new DateTime($token_starterre['date_creation'], new DateTimeZone("Europe/Paris"));
        $current_time = new DateTime("now", new DateTimeZone("Europe/Paris"));

        $interval_time = $current_time->getTimestamp() - $time_token_created->getTimestamp();

        $minute_passed = $interval_time / 60;

        // var_dump($minute_passed);

        //plus de 55 minutes alors on recrée un token dans la base
        if ($minute_passed >= 55) {

            $newTokenStarterre = get_token_starterre($environnement);

            // update du token dans la base
            $data = [
                "token" => $newTokenStarterre
            ];
            $sql = "UPDATE token SET token = :token WHERE ID = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);

            return $newTokenStarterre;
        }


        //si moins de 55 minutes
        else {
            $token = $token_starterre['token'];
        }
    }

    return $token;
}

function perso_empty_weight($unloadedweight)
{
    $a = intval($unloadedweight);
    $result = $a + 75;

    return $result;
}

function set_bodywork_name_for_starterre($bodywork_name)
{
    $bodywork_name = strtolower($bodywork_name);

    switch ($bodywork_name) {
        case 'targa':
        case 'spider':
            $bodytype = 'SPORTS_CAR';
            break;
        case 'roadster':
            $bodytype = 'ROADSTER';
            break;
        case '4x4':
        case 'crossover':
        case 'crossover-suv':
        case 'suv':
            $bodytype = "SUV";
            break;
        case 'break':
            $bodytype = "STATION_WAGON";
            break;
        case 'cabriolet':
            $bodytype = "CONVERTIBLE";
            break;
        case 'citadine':
            $bodytype = "CITY_CAR";
            break;
        case 'coup':
        case 'coupé':
            $bodytype = "COUPE";
            break;
        case 'pick-up':
        case 'pick-up (vp)':
        case 'pick-up acier':
        case 'pick-up cabine double':
        case 'pick-up hardtop':
        case 'pick-up long':
        case 'pick-up-double-cabine':
            $bodytype = "PICKUP_TRUCK";
            break;
        case 'monospace':
        case 'ludospace':
        case 'mini bus':
        case 'minibus':
        case 'minispace':
        case 'monospace compact':
        case 'combi i':
        case 'combin':
        case 'combispace':
        case 'multispace':
            $bodytype = "MINIVAN";
            break;
        case 'châssis':
        case 'châssis cabine':
        case 'châssis cabine double':
        case 'chassis-double Cabine':
        case 'chassis-nu':
        case 'fourgon':
        case 'fourgon surélévé':
        case 'fourgon vitr':
        case 'fourgon vitré':
        case 'fourgonnette':
        case 'camionette':
        case 'utilitaire':
        case 'utilitaires':
        case 'benne':
        case 'benne-double-cabine':
        case 'cabine':
        case 'camion':
            $bodytype = "COMMERCIAL_VEHICLE";
            break;
        case 'berline':
            $bodytype = "SEDAN";
            break;
        default:
            $bodytype = "SEDAN";
            break;
    }

    return $bodytype;
}

function set_environnement_pdo($environnement)
{
    switch ($environnement) {

        case 'dev':
            $pdo = Connection::getPDO_starterre();
            break;

        case 'prod':
            $pdo = Connection::getPDO_starterre_prod();
            break;

        default:
            $pdo = Connection::getPDO_starterre();
            break;
    }
    return $pdo;
}

function get_adresse_from_notes_vh_kepler($notes_public)
{
    $explode_notes = explode(",", $notes_public);

    $adresse['numero_rue'] = $explode_notes[0];
    $adresse['nom_rue'] = $explode_notes[1];
    $adresse['code_postal'] = $explode_notes[2];
    $adresse['ville'] = $explode_notes[3];
    $adresse['code_pays'] = $explode_notes[4];

    return $adresse;
}


/**
 * Emails avec Microsoft Graph & OAuth2
 * - tenantId : Identifiant du tenant Microsoft
 * - clientId : Identifiant client (application)
 * - clientSecret : Secret client (clÃ© d'authentification)
 * - scope : Scopes d'accÃ¨s pour l'API
 * - fromBox : Adresse e-mail de l'expÃ©diteur
 */

function getInfosForAccesToken()
{
    $pdo = Connection::getPDO_Microsoft();

    $request = $pdo->query("SELECT value FROM graphmail where libelle = 'tenantId'");
    $tenantID = $request->fetch(PDO::FETCH_COLUMN);

    $result['tenantId'] = $tenantID;

    $request = $pdo->query("SELECT value FROM graphmail where libelle = 'clientId'");
    $clientId = $request->fetch(PDO::FETCH_COLUMN);

    $result['tenantId'] = $tenantID;

    $request = $pdo->query("SELECT value FROM graphmail where libelle = 'clientSecret'");
    $clientSecret = $request->fetch(PDO::FETCH_COLUMN);

    $result['tenantId'] = $tenantID;
    $result['clientId'] = $clientId;
    $result['clientSecret'] = $clientSecret;

    return $result;
}



function getAccessToken($infos_graph)
{
    $url = "https://login.microsoftonline.com/" . $infos_graph['tenantId'] . "/oauth2/v2.0/token";

    $data = http_build_query([
        'client_id' => $infos_graph['clientId'],
        'client_secret' => $infos_graph['clientSecret'],
        'scope' => 'https://graph.microsoft.com/.default',
        'grant_type' => 'client_credentials',
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);


    //environnement de DEV
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);


    $response = curl_exec($ch);

    // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // echo "Code HTTP : $httpCode\n";

    if ($response === false) {
        echo 'Erreur cURL : ' . curl_error($ch);
    }

    curl_close($ch);

    $tokenData = json_decode($response, true);
    return $tokenData['access_token'] ?? null;
}

function sendEmail($accessToken, $fromBox, $to, $subject, $body)
{
    $graph_url = "https://graph.microsoft.com/v1.0/users/$fromBox/sendMail";

    // Préparer les destinataires (si plusieurs)
    $toRecipients = [];
    foreach ($to as $email) {
        $toRecipients[] = [
            "emailAddress" => ["address" => $email]
        ];
    }

    $emailData = json_encode([
        "message" => [
            "subject" => $subject,
            "body" => [
                "contentType" => "HTML",
                "content" => $body
            ],
            // "toRecipients" => [
            //     ['emailAddress' => ['address' => $to]]
            // ]
            "toRecipients" => $toRecipients
        ]
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $graph_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $emailData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ]);

    //environnement de DEV
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}


function get_vhs_en_vente($environnement)
{

    $pdo = set_environnement_pdo($environnement);

    $request = $pdo->query("SELECT id_kepler,id_starterre FROM vehicules WHERE state = 1");
    $result_vhs = $request->fetchAll(PDO::FETCH_ASSOC);

    return $result_vhs;
}

function get_state_from_reference_vh_kepler($reference)
{
    // le token
    $token = get_token();


    $dataArray = array(
        "reference" => $reference
    );

    $request_vehicule = "v3.7/vehicles/";
    $url = "https://www.kepler-soft.net/api/";

    $url_vehicule = $url . "" . $request_vehicule;

    $data = http_build_query($dataArray);

    $getURL = $url_vehicule . '?' . $data;

    // print_r($getURL);
    // sautdeligne();

    $ch = curl_init();
    $header = array();
    $header[] = 'X-Auth-Token:' . $token;
    $header[] = 'Content-Type:text/html;charset=utf-8';

    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $result = curl_error($ch);
        print_r($result);
        echo "<br/> erreur";
    }

    // var_dump(gettype($result));
    // print_r($result);

    curl_close($ch);

    // créer un objet à partir du retour qui est un string
    $obj_vehicule = json_decode($result);

    return $obj_vehicule[0]->state;
}

function get_idStarterre_from_VIN($vin, $environnement)
{
    $pdo = set_environnement_pdo($environnement);

    $request = $pdo->query("SELECT id_starterre FROM vehicules WHERE vin = '$vin'");
    $id_starterre = $request->fetch(PDO::FETCH_COLUMN);

    return $id_starterre;
}

function set_url_environnement_starterre_vh($environnement)
{
    // Définir l'URL de l'API pour POST VEHICULES
    switch ($environnement) {
        case 'prod':
            $url = "https://cameleon.starterre.fr/api/vehicles";
            break;
        case 'dev':
            $url = "https://cameleon.starterre.dev/api/vehicles";
            break;
    }
    return $url;
}