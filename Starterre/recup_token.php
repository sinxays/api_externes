<?php

include 'fonctions_starterre.php';

// Définir l'URL de l'API pour l'authentification
// $url = "https://cameleon.starterre.dev/auth";
$url = "https://cameleon.starterre.fr/auth";

// Préparer les données du body (le payload JSON)
// $data = array(
//     "email" => "guillaume.honnert@massoutre-locations.com",
//     "password" => "KXpOmwlRolW8%mAl"
// );

$data = array(
    "email" => "sinxay.souvannavong@massoutre-locations.com",
    "password" => "wUK8aqXcTlxqQ8!M"
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
} else {
    // Convertir la réponse JSON en tableau PHP
    $response_data = json_decode($response, true);

    // Vérifier si le token est bien présent dans la réponse
    if (isset($response_data['token'])) {
        $token = $response_data['token'];
        echo "Token JWT obtenu : " . $token;
    } else {
        echo "Erreur : le token n'a pas été obtenu.";
        print_r($response_data);  // Pour déboguer la réponse
    }
}
