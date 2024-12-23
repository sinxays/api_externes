<?php

use App\Connection;

include __DIR__ . '/../pdo.php';




ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);





function starterre_recup_vh_by_identifier($identifier_vh)
{
    $url = "https://cameleon.starterre.dev/api/vehicles";

    $data = array(
        "id" => $identifier_vh,
    );

    // d'apres la doc cameleon, pour récupérer un véhicule il faut faire /api/vehicles/{id} donc on colle direct l'id derriere l'url
    $full_url = $url . "/" . $identifier_vh;

    // Initialiser une session cURL
    $ch = curl_init();

    // le token
    $token = use_token_from_base();

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

function get_token_starterre()
{
    // Définir l'URL de l'API pour l'authentification
    $url = "https://cameleon.starterre.dev/auth";

    // Préparer les données du body (le payload JSON)
    $data = array(
        "email" => "guillaume.honnert@massoutre-locations.com",
        "password" => "KXpOmwlRolW8%mAl"
    );

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
        echo "Erreur dans la requête cURL.";
        die();
    } else {
        // Convertir la réponse JSON en tableau PHP
        $response_data = json_decode($response, true);

        // Vérifier si le token est bien présent dans la réponse
        if (isset($response_data['token'])) {
            $token = $response_data['token'];
            echo "Token JWT obtenu : " . $token;
            sautdeligne();
            sautdeligne();
            separateur();
            return $token;
        } else {
            echo "Erreur : le token n'a pas été obtenu.";
            print_r($response_data);  // Pour déboguer la réponse
            die();
        }
    }
}


function get_vhs_starterre_from_base($id_kepler = '')
{
    $pdo = Connection::getPDO_starterre();

    $request = $pdo->query("SELECT id_kepler,id_starterre FROM vehicules");
    $result_vhs = $request->fetchAll(PDO::FETCH_ASSOC);

    return $result_vhs;
}






function recup_vhs_kepler_for_starterre($parc, $page)
{
    // le token
    $token = get_token();


    $dataArray = array(
        "state" => 'vehicle.state.parc',
        "isNotAvailableForSelling" => FALSE,
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

function recup_vhs_vendus_kepler_for_starterre($parc, $page)
{
    // le token
    $token = get_token();

    $dataArray = array(
        // "state" => 'vehicle.state.sold,vehicle.state.sold_ar,vehicle.state.pending,vehicle.state.out',
        "state" => 'vehicle.state.sold',
        "isNotAvailableForSelling" => FALSE,
        "dateUpdatedStart" => "2024-11-14",
        "dateUpdatedEnd" => "2024-11-15",
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

    curl_close($ch);

    // var_dump(gettype($result));
    print_r($result);

    // créer un objet à partir du retour qui est un string
    $obj_vehicule = json_decode($result);

    //on prend l'object qui est dans l'array
    $return = $obj_vehicule;

    // on retourne un objet
    // var_dump($return);
    return $return;
}

function recup_vhs_vendus_kepler_for_starterre_bis()
{
    // le token
    $token = get_token();

    // date du jour
    $currentDateTime = date('Y-m-d');

    $dataArray = array(
        "state" => 'vehicle.state.sold',
        "isNotAvailableForSelling" => FALSE,
        "dateUpdatedStart" => $currentDateTime,
        "count" => 100
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

    // var_dump($result);

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
        echo "pas de cv renseigné";
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
    $array_for_csv["body-type"] = "CITY_CAR";
    $array_for_csv["fuel"] = isset($vh->energy->name) ? get_energy_vh_for_starterre($vh->energy->name) : "";
    if ($array_for_csv["fuel"] == "NA") {
        sautdeligne();
        echo "pas de energie renseigné";
        sautdeligne();
        $no_export = TRUE;
    }
    $array_for_csv["gearbox"] = isset($vh->gearbox->name) ? get_gearbox_vh_for_starterre($vh->gearbox->name) : "";
    $array_for_csv["gear-number"] = isset($vh->reportNumber) ? $vh->reportNumber : "N/A";
    if ($array_for_csv["gear-number"] == "N/A") {
        sautdeligne();
        echo "pas de nombre de rapport renseigné";
        sautdeligne();
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
    $array_for_details_dimensions = ["height" => 'HEIGHT', "length" => 'LENGTH', "width" => 'WIDTH', "unloadedWeight" => "EMPTY_WEIGHT"];
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
        $array_for_csv["details"][0]["caracteristics"][$i]["value"] = strval($vh->carEngine);
        $array_for_csv["details"][0]["caracteristics"][$i]["category"] = "MOTOR";
        $array_for_csv["details"][0]["caracteristics"][$i]["type"] = "ENGINE_CAPACITY";
        $i++;
    } else {
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
    $array_equipements_serie = $vh->equipmentStandard;
    foreach ($array_equipements_serie as $equipement) {
        $array_for_csv["details"][0]["equipments"][$i]["label"] = $equipement->name;
        $array_for_csv["details"][0]["equipments"][$i]["price"] = isset($equipement->price) ? floatval($equipement->price) : 0;
        $array_for_csv["details"][0]["equipments"][$i]["is-standard"] = true;
        $array_for_csv["details"][0]["equipments"][$i]["is-missing"] = false;
        $array_for_csv["details"][0]["equipments"][$i]["category"] = "OTHER";
        $i++;
    }

    if (isset($vh->equipmentOptional) && !empty($vh->equipmentOptional)) {
        //equipement en options
        $array_equipements_serie = $vh->equipmentOptional;

        foreach ($array_equipements_serie as $equipement) {
            $array_for_csv["details"][0]["equipments"][$i]["label"] = $equipement->name;
            $array_for_csv["details"][0]["equipments"][$i]["price"] = isset($equipement->price) ? floatval($equipement->price) : 0;
            $array_for_csv["details"][0]["equipments"][$i]["is-standard"] = false;
            $array_for_csv["details"][0]["equipments"][$i]["is-missing"] = false;
            $array_for_csv["details"][0]["equipments"][$i]["category"] = "OTHER";
            $i++;
        }
    }


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
        $no_export = TRUE;
    }


    $array_for_csv["documents"][0]["url"] = null;
    $array_for_csv["documents"][0]["type"] = "MAINTENANCE";


    $array_for_csv["avaibility"]["avaibility"] = "STOCK";


    $array_for_csv["warranty"]["type"] = "MANUFACTURER";

    $adresse = get_adress_vh_for_post_starterre($vh->fleet);

    $array_for_csv["address"][0]["type"] = "STORAGE";
    $array_for_csv["address"][0]["street-number"] = $adresse["num_rue"];
    $array_for_csv["address"][0]["street-name"] = $adresse["rue"];
    $array_for_csv["address"][0]["postcode"] = $adresse["cp"];
    $array_for_csv["address"][0]["city"] = $adresse["ville"];
    $array_for_csv["address"][0]["country"]["code"] = $adresse["pays"];



    // UPDATE : si on une donnée manquante ou non conventionelle pour starterre on post pas vers starterre
    if ($no_export) {
        sautdeligne();
        echo "véhicule $vh->reference no export";
        sautdeligne();
        return null;
    } else {
        $json_data_vh = json_encode($array_for_csv);
        // sautdeligne();
        // var_dump($json_data_vh);
        return $json_data_vh;
    }


}

function post_vh_to_starterre($donnees_vh_to_post)
{
    // Initialiser une session cURL
    $ch = curl_init();

    // Définir l'URL de l'API pour POST VEHICULES
    $url = "https://cameleon.starterre.dev/api/vehicles";

    $token = use_token_from_base();
    // var_dump($token);

    // $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MzEwODA3NDMsImV4cCI6MTczMTA4NDM0Mywicm9sZXMiOlsiUk9MRV9QT1NUX1ZFSElDTEUiLCJST0xFX0RFTEVURV9WRUhJQ0xFIiwiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiZ3VpbGxhdW1lLmhvbm5lcnRAbWFzc291dHJlLWxvY2F0aW9ucy5jb20ifQ.PMzcKGxK-Uh1dMZ8cteBJYHVXMAhSMitCMHNIRZoekBjRfVSq1Yck1Qj7RPTJF5tCRdjhjgOJ4Z9lipyE8iDkk2KepO15MR2BdpmPqa9eTtEIPuNTgAz2JGxnfrwmfdZTwlle0-VVK6WwCso_20gGCVd35pxVKWoqnalx2G8cV88kuSYjq_awybc8jSO3vJU-KyysXpKKWaQ8nIkurGBfUf71R-gFZFTxhkpROr8h_76XsrijWcS0LuO4rSyuvtwluAAy3JMBvL8bEIiH-x9UCmxE4V5S0d-SIN9LIgqmNs6XjbSJmmweE0tsKHIigiWQQSttO_SyLeGv-G8bTUs5Q";

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
        echo "Erreur dans la requête cURL.";
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

function post_vh_to_delete_starterre($id_starterre)
{
    // Initialiser une session cURL
    $ch = curl_init();

    // Définir l'URL de l'API pour POST VEHICULES
    $url = "https://cameleon.starterre.dev/api/vehicles/" . $id_starterre;

    $token = use_token_from_base();
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
function create_vh_replica_starterre($id_kepler, $id_starterre, $immatriculation, $vin)
{
    $pdo = Connection::getPDO_starterre();

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

function update_vh_replica_starterre($id_kepler, $state)
{

    $pdo = Connection::getPDO_starterre();

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


function check_if_vh_exist($reference_kepler)
{
    $pdo = Connection::getPDO_starterre();

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

    $pdo = Connection::getPDO_starterre();

    $request = $pdo->query("SELECT state from vehicules WHERE id_kepler = '$reference_kepler'");
    $result = $request->fetch(PDO::FETCH_COLUMN);

    if ($result == 0) {
        return null;
    } else {
        return true;
    }
}

function get_idStarterre_from_idKepler($id_kepler)
{
    $pdo = Connection::getPDO_starterre();
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
    switch ($energy_vh_kepler) {
        case 'Essence sans plomb':
        case 'ESSENCE':
        case 'Essence plomb':
            $energy_for_starterre = "ES";
            break;
        case 'Diesel':
            $energy_for_starterre = "GO";
            break;
        case 'GPL':
            $energy_for_starterre = "PLG";
            break;
        case 'Courant electrique':
        case 'Electrique':
            $energy_for_starterre = "EL";
            break;

        case 'Essence / Courant électrique':
        case 'Hybride':
        case 'HYBRID':
            $energy_for_starterre = "EH";
            break;

        default:
            $energy_for_starterre = "NA";
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


    switch ($parc) {
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
        case 'CVO ORLEANS sud':
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


function use_token_from_base()
{

    $pdo = Connection::getPDO_starterre();

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

            $newTokenStarterre = get_token_starterre();

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
