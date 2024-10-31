<?php

use App\Connection;

include $_SERVER['DOCUMENT_ROOT'] . "/pdo.php";


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

    $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MzAxMDc0NjYsImV4cCI6MTczMDExMTA2Niwicm9sZXMiOlsiUk9MRV9QT1NUX1ZFSElDTEUiLCJST0xFX0RFTEVURV9WRUhJQ0xFIiwiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiZ3VpbGxhdW1lLmhvbm5lcnRAbWFzc291dHJlLWxvY2F0aW9ucy5jb20ifQ.BRrNE1cUWy-MIvtBxYzA_gorhmifLr0O-ZwoGOmHUVqTL3-LyThxm-RNXJ91kTiADK0YFHXvXz_vzTl4DlZo-VhMzQUoxciabyLnfQjXt9IrxdrieKdWp6BhyBP0m4x5YKxKoQHg102lF46yqGo1bTE7VUDmMxpPJCaYyjL03A40c920bdiukJ4Je3GdeogfwKK58DMPt4VKPx-cfkozEL4vy8UUvXmGJyRxoRq_OxSiubE1bESsvx5T3hDMWq4M7gik-DfzOcAnuz5sglnxMH4JYCkvP15YVHyXsgPWriBvSPWBaoqRFl4O9UvF1I3snicF5uDiNtKfNVmPTiBroA";

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






function recup_starterre($parc, $page)
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



function mise_en_forme_result($type_recherche, $data)
{
    $return = "";
    $nb_ligne = 1;
    switch ($type_recherche) {
        case "bdc":
            $return .= "<table class='my_table_bdc'>";
            $return .= "<thead>";
            $return .= creation_header_thead(array(" ", "Uuid", "Numéro BDC", "Etat", "Date", "Vendeur", "Prix total TTC"));
            $return .= "</thead>";
            foreach ($data as $bdc) {
                $return .= "<tr>";
                $return .= "<td> " . $nb_ligne . " </td>";
                $return .= "<td> " . $bdc->uuid . " </td>";
                $return .= "<td> " . $bdc->number . " </td>";
                $return .= "<td> " . $bdc->state . " </td>";
                $date = new DateTime($bdc->date);
                $date_bdc = $date->format('d/m/Y');
                $return .= "<td> " . $date_bdc . " </td>";
                $return .= "<td> " . $bdc->seller . " </td>";
                $return .= "<td> " . $bdc->sellPriceWithTax . " </td>";
                $return .= "</tr>";
                $nb_ligne++;
            }
            $return .= "</table>";
            break;

        case "facture":
            $return .= "<table class='my_table_facture'>";
            $return .= "<thead>";
            $return .= creation_header_thead(array(" ", "Uuid", "Numéro facture", "Etat", "Date", "Vendeur", "Prix total TTC", "BDC lié"));
            $return .= "</thead>";
            foreach ($data as $facture) {

                //on prend que les VO***
                if (strpos($facture->number, 'VO') !== FALSE) {
                    $return .= "<tr>";
                    $return .= "<td> " . $nb_ligne . " </td>";
                    $return .= "<td> " . $facture->uuid . " </td>";
                    $return .= "<td> " . $facture->number . " </td>";
                    $return .= "<td> " . $facture->state . " </td>";
                    $date = new DateTime($facture->invoiceDate);
                    $date_facturation = $date->format('d/m/Y');
                    $return .= "<td> " . $date_facturation . " </td>";
                    $return .= "<td> " . $facture->seller . " </td>";
                    $return .= "<td> " . $facture->sellPriceWithTax . " </td>";
                    $return .= "<td> " . $facture->orderForm->number . " </td>";
                    $return .= "</tr>";
                    $nb_ligne++;
                }
            }
            $return .= "</table>";
            break;

        case "vh":
            $return .= "<table class='my_table_vh'>";
            $return .= "<thead>";
            $return .= creation_header_thead(array("Uuid", "réference kepler", "Etat", "Marque", "Modele", "Version", "Type VO/VN", "Type", "VIN", "Immatriculation"));
            $return .= "</thead>";

            $vh = $data;
            $return .= "<tr>";
            $return .= "<td> " . $vh->uuid . " </td>";
            $return .= "<td> " . $vh->reference . " </td>";
            $return .= "<td> " . $vh->state . " </td>";
            $return .= "<td> " . $vh->brand->name . " </td>";
            $return .= "<td> " . $vh->model->name . " </td>";
            $return .= "<td> " . $vh->version->name . " </td>";
            $return .= "<td> " . $vh->typeVoVn->name . " </td>";
            $return .= "<td> " . $vh->vehicleType->name . " </td>";
            $return .= "<td> " . $vh->vin . " </td>";
            $return .= "<td> " . $vh->licenseNumber . " </td>";
            $return .= "</tr>";
            $return .= "</table>";
            break;
    }

    return $return;
}


function mise_en_array_des_donnees_recup($array_for_csv, $nb_index_vh, $vh)
{
    $array_for_csv["partner-vehicle-identifier"] = isset($vh->reference) ? $vh->reference : "";
    $array_for_csv["is_used"] = true;
    $array_for_csv["is-executive"] = false;
    $array_for_csv["seat-capacity"] = isset($vh->seats) ? $vh->seats : 0;
    $array_for_csv["door-number"] = isset($vh->doors) ? $vh->doors : 0;

    if (isset($vh->horsepower) && !is_null($vh->horsepower)) {
        $power_kw = calcul_power_kw($vh->horsepower);
        $array_for_csv["power-kw"] = $power_kw;
        $array_for_csv["power-horsepower"] = $vh->horsepower;
    } else {
        $array_for_csv["power-kw"] = 0;
        $array_for_csv["power-horsepower"] = 0;
    }

    $array_for_csv["mileage"] = isset($vh->distanceTraveled) ? $vh->distanceTraveled : 0;

    $array_for_csv["origin"] = isset($vh->origin) ? get_origin_vh_for_starterre($vh->origin) : "";
    $array_for_csv["co2-emission"] = isset($vh->extraUrbanKmConsumption) ? $vh->extraUrbanKmConsumption : 0;
    $array_for_csv["co2-co2-emission-type"] = "WLTP";
    $array_for_csv["transmission"] = "FWD";
    $array_for_csv["origin-country"]["code"] = isset($vh->country) ? $vh->country : "";
    $array_for_csv["national-type"] = isset($vh->vehicleType->name) ? $vh->vehicleType->name : "";
    $array_for_csv["body-type"] = "CITY_CAR";
    $array_for_csv["fuel"] = isset($vh->energy->name) ? get_energy_vh_for_starterre($vh->energy->name) : "";
    $array_for_csv["gearbox"] = isset($vh->gearbox->name) ? get_gearbox_vh_for_starterre($vh->gearbox->name) : "";
    $array_for_csv["gear-number"] = isset($vh->reportNumber->name) ? $vh->reportNumber->name : "";
    $array_for_csv["brand"]["name"] = isset($vh->brand->name) ? $vh->brand->name : "";

    $array_for_csv["price"]["price-without-taxes"] = isset($vh->pricePublicWithoutTax) ? $vh->pricePublicWithoutTax : 0;
    $array_for_csv["price"]["constructor-price-without-taxes"] = 0; // pas l'info sur kepler
    $array_for_csv["price"]["estimated-costs-without-taxes"] = isset($vh->estimateCost) ? $vh->estimateCost : 0;
    $array_for_csv["price"]["price-type"] = "prix_marchand_starterre";
    $array_for_csv["price"]["currency"] = "EUR";
    $array_for_csv["price"]["sold-as-is"] = true;
    $array_for_csv["price"]["sold-under-delegation"] = false;

    $array_for_csv["identification-number"] = isset($vh->vin) ? $vh->vin : "";
    $array_for_csv["release-year"] = isset($vh->year) ? $vh->year : 0;
    $array_for_csv["power-tax"] = isset($vh->taxHorsepower) ? $vh->taxHorsepower : 0;
    $array_for_csv["date-first-registration"] = isset($vh->dateOfDistribution) ? $vh->dateOfDistribution : "0000-00-00";
    $array_for_csv["model"] = isset($vh->model->name) ? $vh->model->name : "";
    $array_for_csv["configuration"] = isset($vh->version->name) ? $vh->version->name : "";
    $array_for_csv["external-commercial-color"] = isset($vh->color->name) ? $vh->color->name : "";

    $array_for_details_dimensions = ["height" => 'HEIGHT', "length" => 'LENGTH', "width" => 'WIDTH'];
    $i = 0;

    foreach ($array_for_details_dimensions as $key => $detail) {
        $array_for_csv["details"][$i]["language"]["code"] = "fr";
        $array_for_csv["details"][$i]["caracteristics"]["value"] = isset($vh->$key) ? $vh->$key : "";
        $array_for_csv["details"][$i]["caracteristics"]["category"] = "DIMENSIONS";
        $array_for_csv["details"][$i]["caracteristics"]["type"] = $detail;
        $i++;
    }













    // array_push($array_for_csv, isset($vh->vo) ? $vh->vo : "");
    // array_push($array_for_csv, isset($vh->state) ? $vh->state : "");
    // array_push($array_for_csv, isset($vh->vin) ? $vh->vin : "");
    // array_push($array_for_csv, isset($vh->licenseNumber) ? $vh->licenseNumber : "");
    // array_push($array_for_csv, isset($vh->brand->name) ? $vh->brand->name : "");
    // array_push($array_for_csv, isset($vh->modele->name) ? $vh->modele->name : "");
    // array_push($array_for_csv, isset($vh->version->name) ? $vh->version->name : "");
    // array_push($array_for_csv, isset($vh->energy->name) ? $vh->energy->name : "");
    // array_push($array_for_csv, isset($vh->dateOfDistribution) ? $vh->dateOfDistribution : "");
    // array_push($array_for_csv, isset($vh->year) ? $vh->year : "");
    // array_push($array_for_csv, isset($vh->color->name) ? $vh->color->name : "");
    // array_push($array_for_csv, isset($vh->distanceTraveled) ? $vh->distanceTraveled : "");
    // array_push($array_for_csv, isset($vh->fleet) ? $vh->fleet : "");
    // array_push($array_for_csv, isset($vh->country) ? $vh->country : "");
    // array_push($array_for_csv, isset($vh->reportNumber) ? $vh->reportNumber : "");
    // array_push($array_for_csv, isset($vh->bodywork->name) ? $vh->bodywork->name : "");
    // array_push($array_for_csv, isset($vh->vehicleType->name) ? $vh->vehicleType->name : "");
    // array_push($array_for_csv, isset($vh->gearbox->name) ? $vh->gearbox->name : "");
    // array_push($array_for_csv, "");
    // array_push($array_for_csv, isset($vh->horsePower) ? $vh->horsePower : "");
    // array_push($array_for_csv, isset($vh->taxHorsepower) ? $vh->taxHorsepower : "");
    // array_push($array_for_csv, isset($vh->carEngine) ? $vh->carEngine : "");
    // array_push($array_for_csv, isset($vh->pricePublicWithoutTax) ? $vh->pricePublicWithoutTax : "");
    // array_push($array_for_csv, isset($vh->pricePublic) ? $vh->pricePublic : "");
    // array_push($array_for_csv, isset($vh->priceSellerWithoutTax) ? $vh->priceSellerWithoutTax : "");
    // array_push($array_for_csv, isset($vh->priceSeller) ? $vh->priceSeller : "");
    // array_push($array_for_csv, isset($vh->estimateCost) ? $vh->estimateCost : "");
    // array_push($array_for_csv, isset($vh->extraUrbanKmConsumption) ? $vh->extraUrbanKmConsumption : "");
    // array_push($array_for_csv, isset($vh->mixteConsumption) ? $vh->mixteConsumption : "");
    // array_push($array_for_csv, isset($vh->unloadedWeight) ? $vh->unloadedWeight : "");
    // array_push($array_for_csv, isset($vh->doors) ? $vh->doors : "");
    // array_push($array_for_csv, isset($vh->seats) ? $vh->seats : "");
    // array_push($array_for_csv, isset($vh->warrantyEndDate) ? $vh->warrantyEndDate : "");
    // array_push($array_for_csv, isset($vh->valueNewOption) ? $vh->valueNewOption : "");
    // array_push($array_for_csv, isset($vh->valueOption) ? $vh->valueOption : "");
    // array_push($array_for_csv, isset($vh->valueNew) ? $vh->valueNew : "");
    // array_push($array_for_csv, isset($vh->purchaseZone) ? $vh->purchaseZone : "");
    // array_push($array_for_csv, isset($vh->purchaseTax) ? $vh->purchaseTax : "");
    // array_push($array_for_csv, isset($vh->origin) ? $vh->origin : "");
    // array_push($array_for_csv, "Non");

    // //photos des véhicules
    // $array_photos = array();
    // foreach ($vh->gallery as $photo_vh) {
    //     array_push($array_photos, $photo_vh->photo);
    //     // $string_photos .= $photo_vh->photo . "||";
    // }
    // $string_photos = implode("||", $array_photos);
    // array_push($array_for_csv, $string_photos);

    // //équipements des véhicules
    // $array_equipements = array();
    // foreach ($vh->equipmentStandard as $equipement_vh) {
    //     array_push($array_equipements, $equipement_vh->name);
    //     // $string_equipement .= $equipement_vh->name . "||";
    // }
    // $string_equipements = implode("||", $array_equipements);
    // array_push($array_for_csv, $string_equipements);

    var_dump($array_for_csv);

    die();

    return $array_for_csv;
}

function calcul_power_kw($powerhorse)
{
    $power_kw = $powerhorse * 0.736;
    return $power_kw;
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