<?php

include $_SERVER['DOCUMENT_ROOT'] . "/StarTerre/fonctions_starterre.php";


if (isset($_POST)) {

    // le token
    // $token = get_token();

    $type_recherche = $_POST['type_recherche'];
    switch ($type_recherche) {
        //RECHERCHE VH sur STARTERRE
        case "identifier_vh":
            $datas_vh_starterre = starterre_recup_vh_by_identifier($_POST['identifier_vh']);

            if (!empty($datas_vh_starterre)) {
                $result_final = $datas_vh_starterre;
            } else {
                $result_final = "Pas de résultat";
            }
            // echo $result_final;
            echo $result_final;

            break;

    }
}

