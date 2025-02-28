<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>API STARTERRE</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- perso css -->
    <link rel='stylesheet' type='text/css' media='screen' href='style_api_externes.css'>


</head>



<?php

// echo __DIR__ . '\pdo.php';

?>

<body>
    <div class="header">
        <h1>API STARTERRE</h1>
    </div>
    <div class="body_filtres">
        <div class="elements_row">
            <button type="button" class="btn btn-info" id="btn_export_kepler_for_starterre"
                style="max-width:200px;min-width: 200px;color:antiquewhite;font-weight: bold;">Creation VH pour
                Starterre</button>

            <button type="button" class="btn btn-info" id="btn_api_starterre_update_vhs_vendus"
                style="max-width:200px;min-width: 200px;color:antiquewhite;font-weight: bold;">UPDATE vhs
                vendus</button>

            <button type="button" class="btn btn-success" id="btn_api_starterre_token"
                style="max-width:200px;min-width: 200px;color:antiquewhite;font-weight: bold;">Recup Token</button>

            <button type="button" class="btn btn-success" id="btn_api_starterre_post_vh" data-bs-toggle="modal"
                data-bs-target="#modal_test_post_vh_to_starterre"
                style="max-width:200px;min-width: 200px;color:antiquewhite;font-weight: bold;">Test POST VH</button>

            <button type="button" class="btn btn-warning" id="btn_api_starterre_get_vh" data-bs-toggle="modal"
                data-bs-target="#modal_api_starterre_get_vh"
                style="max-width:200px;min-width: 200px;color:antiquewhite;font-weight: bold;">Test GET VH</button>

            <button type="button" class="btn btn-danger" id="btn_api_starterre_delete_vhs"
                style="max-width:200px;min-width: 200px;color:antiquewhite;font-weight: bold;">DELETE VHS</button>

            <button type="button" class="btn btn-primary" id="btn_api_starterre_page_test"
                style="max-width:200px;min-width: 200px;color:antiquewhite;font-weight: bold;">page test</button>
        </div>
    </div>

    <div class="body_results">
        <!-- <button id="button_fade_sql" style="display: none;">Résultat Brut</button>
            <span id="area_result_brut" style="display: none;"></span> -->
        <span id="area_result"></span>
    </div>




    <!-- MODAL GET VH FROM STARTERRE -->
    <div class="modal" id="modal_api_starterre_get_vh" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="my_modal_header_bdc">
                    <h5 class="modal-title" id="exampleModalLongTitle">Rechercher un VH Starterre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal_body">
                    <form id="form_api_starterre_get_vh">
                        <!-- <span class="info_modal_body">Soit par un numéro, ou tous les BDC d'une date spécifique</span> -->
                        <div class="form-group">
                            <label for="starterre_reference_vh_input"><b>identifiant VH</b> (reference kepler du
                                véhicule)</label>
                            <input type="text" class="form-control" id="starterre_reference_vh_input"
                                name="starterre_reference_vh_input" style="width: 200px;">
                        </div>
                        <input type="text" name="type_recherche_starterre_vh" id="type_recherche_starterre_vh"
                            value="identifier_vh_kepler_from_starterre" hidden>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        id="btn_close_modal_recherche_vehicule_starterre"> Annuler</button>
                    <button type="button" class="btn btn-primary" id="button_rechercher_starterre_vh"
                        disabled>Rechercher</button>
                </div>


            </div>
        </div>
    </div>




    <!-- MODAL TEST POST VH VERS STARTERRE -->
    <div class="modal" id="modal_test_post_vh_to_starterre" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="my_modal_header_bdc">
                    <h5 class="modal-title" id="exampleModalLongTitle">TEST POST VH vers Starterre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal_body">
                    <form id="form_api_starterre_get_vh">
                        <!-- <span class="info_modal_body">Soit par un numéro, ou tous les BDC d'une date spécifique</span> -->
                        <div class="form-group">
                            <label for="input_search_reference_kepler"><b>identifiant VH</b> (reference kepler du
                                véhicule)</label>
                            <input type="text" class="form-control" id="input_search_reference_kepler"
                                name="input_search_reference_kepler" style="width: 200px;">
                        </div>
                        <input type="text" name="type_recherche_kepler_vh" id="type_recherche_kepler_vh"
                            value="identifier_vh_kepler_to_post_starterre" hidden>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        id="btn_close_modal_recherche_vehicule_kepler"> Annuler</button>
                    <button type="button" class="btn btn-primary" id="button_rechercher_kepler_vh" disabled>TEST
                        POST</button>
                </div>


            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src='api_externes.js'></script>





</body>

</html>