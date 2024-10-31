<?php

include 'fonctions_starterre.php';


// Définir l'URL de l'API pour l'authentification
$url = "https://cameleon.starterre.dev/api/vehicles";

// Préparer les données du body (le payload JSON)
$data_vh = [
    "partner-vehicle-identifier" => "95otsatw",
    "is-used" => true,
    "is-executive" => false,
    "seat-capacity" => 5,
    "door-number" => 5,
    "power-kw" => 67,
    "power-horsepower" => 90,
    "mileage" => 23862,
    "origin" => "STANDARD",
    "co2-emission" => 119,
    "co2-emission-type" => "WLTP",
    "transmission" => "FWD",
    "origin-country" => [
        "code" => "HR"
    ],
    "national-type" => "VP",
    "body-type" => "CITY_CAR",
    "fuel" => "ES",
    "gearbox" => "MANUAL",
    "gear-number" => 6,
    "brand" => [
        "name" => "renault"
    ],
    "prices" => [
        [
            "price-without-taxes" => 14998.33,
            "constructor-price-without-taxes" => 22000,
            "estimated-costs-without-taxes" => 0,
            "ecological-malus" => 0,
            "price-type" => "prix_marchand_starterre",
            "currency" => "EUR",
            "sold-as-is" => false,
            "sold-under-delegation" => false,
            "sold-with-taxes" => true
        ]
    ],
    "manufacturer-price-including-options-without-tax" => 22000,
    "identification-number" => "TMAJ3817ALJ114648",
    "release-year" => 2021,
    "release-month" => 0,
    "power-tax" => 5,
    "date-first-registration" => "2021-06-24",
    "registration-number" => "67476155",
    "main-color" => "BLACK",
    "upholstery" => "LEATHER",
    "natcode" => 229300,
    "deleted-at" => NULL,
    "model" => "CLIO V",
    "configuration" => "Clio TCe 90 - 21 Intens",
    "internal-commercial-color" => "Gray seats with blue stitching",
    "external-commercial-color" => "Gris Titanium",
    "details" => [
        [
            "language" => ["code" => "en"],
            "caracteristics" => [
                [
                    "value" => "1603",
                    "category" => "GENERALITY",
                    "type" => "LENGTH",
                    "id-ref-caracteristic" => "37200818"
                ]
            ],
            "equipments" => [
                [
                    "label" => "Heated seats",
                    "price" => 550,
                    "is-standard" => false,
                    "is-missing" => false,
                    "category" => "COMFORT",
                    "id-eurotax" => "Z1A",
                    "id-ref-equipment" => "37200818"
                ]
            ]
        ]
    ],
    "photos" => [
        [
            "url" => "https://stx.starterre.net/vehicules/xlarge/renault-clio-v-54ca1f105800db40b739cbe43c7fe473.jpg",
            "camera-work" => "OTHER",
            "order" => 1,
            "is-main" => true
        ]
    ],
    "documents" => [
        [
            "url" => "https://www.your-cdn.com/vehicules/1/documents/1",
            "type" => "EXPERTISE"
        ]
    ],
    "avaibility" => [
        "avaibility" => "STOCK",
        "avaibility-date" => "2023-09-03T00:00:00+00:00",
        "shipping-days" => 0,
        "retention-days" => 0,
        "is-ready-to-go" => true
    ],
    "warranty" => [
        "type" => "MANUFACTURER",
        "start-date" => "2024-01-15",
        "duration" => 36,
        "mileage" => 60000
    ],
    "address" => [
        [
            "type" => "STORAGE",
            "street-number" => "95",
            "street-extension" => "bis",
            "street-type" => "place",
            "street-name" => "place",
            "additional-information" => "Transport Abc",
            "postcode" => "69190",
            "city" => "Lyon",
            "country" => [
                "code" => "HR"
            ]
        ]
    ]
];

// Convertir le tableau en format JSON
$json_data_vh = json_encode($data_vh);

// print_r($json_data_vh);

// die();

// Initialiser une session cURL
$ch = curl_init();

// Configuration de la requête cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Retourner la réponse sous forme de chaîne
curl_setopt($ch, CURLOPT_POST, true);            // Indiquer qu'il s'agit d'une requête POST

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MzAxMDc0NjYsImV4cCI6MTczMDExMTA2Niwicm9sZXMiOlsiUk9MRV9QT1NUX1ZFSElDTEUiLCJST0xFX0RFTEVURV9WRUhJQ0xFIiwiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiZ3VpbGxhdW1lLmhvbm5lcnRAbWFzc291dHJlLWxvY2F0aW9ucy5jb20ifQ.BRrNE1cUWy-MIvtBxYzA_gorhmifLr0O-ZwoGOmHUVqTL3-LyThxm-RNXJ91kTiADK0YFHXvXz_vzTl4DlZo-VhMzQUoxciabyLnfQjXt9IrxdrieKdWp6BhyBP0m4x5YKxKoQHg102lF46yqGo1bTE7VUDmMxpPJCaYyjL03A40c920bdiukJ4Je3GdeogfwKK58DMPt4VKPx-cfkozEL4vy8UUvXmGJyRxoRq_OxSiubE1bESsvx5T3hDMWq4M7gik-DfzOcAnuz5sglnxMH4JYCkvP15YVHyXsgPWriBvSPWBaoqRFl4O9UvF1I3snicF5uDiNtKfNVmPTiBroA' // Remplace par ton token d'authentification
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data_vh);
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

    // Vérifier si le token est bien présent dans la réponse
    if (isset($response_data['partner-vehicle-identifier'])) {
        $identifier_vh = $response_data['partner-vehicle-identifier'];
        $id_vh_starterre = $response_data['id'];
        echo "le véhicule crée porte l'identifiant partner : " . $identifier_vh;
        sautdeligne();
        echo "le véhicule crée porte l'id starterre : " . $id_vh_starterre;
    } else {
        echo "Erreur : le véhicule n'a pas été crée.";
        print_r($response_data);  // Pour déboguer la réponse
    }
}
