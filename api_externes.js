$(document).ready(function () {

    let loader = $("#loader");

    // Initialisez la modal
    var modal_api_starterre_get_vh = new bootstrap.Modal($("#modal_api_starterre_get_vh"));


    $("#btn_export_kepler_for_starterre").click(function (e) {
        window.location.href = 'Starterre/recup_vh_kepler_for_starterre.php';
    });

    $("#btn_api_starterre_token").click(function (e) {
        window.location.href = 'Starterre/recup_token.php';
    });

    // $("#btn_api_starterre_post_vh").click(function (e) {
    //     window.location.href = 'Starterre/test_post_vh.php';
    // });
    $("#btn_api_starterre_update_vhs_vendus").click(function (e) {
        window.location.href = 'Starterre/update_vhs_vendus.php';
    });
    $("#btn_api_starterre_delete_vhs").click(function (e) {
        window.location.href = 'Starterre/delete_vhs.php';
    });
    $("#btn_api_starterre_page_test").click(function (e) {
        window.location.href = 'Starterre/test.php';
    });



    $("#starterre_reference_vh_input").change(function (e) {
        let identifier_vh = $(this).val();
        if (identifier_vh !== "") {
            $("#button_rechercher_starterre_vh").prop("disabled", false);
        } else {
            $("#button_rechercher_starterre_vh").prop("disabled", true);
        }
    });

    $("#input_search_reference_kepler").change(function (e) {
        let identifier_vh = $(this).val();
        if (identifier_vh !== "") {
            $("#button_rechercher_kepler_vh").prop("disabled", false);
        } else {
            $("#button_rechercher_kepler_vh").prop("disabled", true);
        }
    });


    $("#btn_close_modal_recherche_vehicule_starterre").click(function (e) {
        e.preventDefault();
        remove_modal($("#modal_api_starterre_get_vh"));
    });

    $("#btn_close_modal_recherche_vehicule_kepler").click(function (e) {
        e.preventDefault();
        remove_modal($("#modal_test_post_vh_to_starterre"));
    });


    $("#button_rechercher_starterre_vh").click(function (e) {
        e.preventDefault();
        loader.show();
        let identifier_vh = $("#starterre_reference_vh_input").val();
        let type_recherche = $("#type_recherche_starterre_vh").val();

        console.log(identifier_vh);
        console.log(type_recherche);

        $.ajax({
            url: "recherche_api.php",
            type: "POST",
            data: { identifier_vh: identifier_vh, type_recherche: type_recherche },
            success: function (data) {
                loader.hide();
                remove_modal($("#modal_api_starterre_get_vh"))
                $("#area_result").html(data);
            },
            error: function () {
                $("#area_result").html("VH NON TROUVE");
            }
        });
    });

    $("#button_rechercher_kepler_vh").click(function (e) {
        e.preventDefault();
        loader.show();
        let identifier_vh = $("#input_search_reference_kepler").val();
        let type_recherche = $("#type_recherche_kepler_vh").val();

        console.log(identifier_vh);
        console.log(type_recherche);

        $.ajax({
            url: "recherche_api.php",
            type: "POST",
            data: { identifier_vh: identifier_vh, type_recherche: type_recherche },
            success: function (data) {
                loader.hide();
                remove_modal($("#modal_test_post_vh_to_starterre"))
                $("#area_result").html(data);
            },
            error: function () {
                $("#area_result").html("VH NON TROUVE");
            }
        });
    });






    function remove_modal(instance) {
        var modal_facture = bootstrap.Modal.getInstance(instance);
        modal_facture.hide();
        $('.modal').modal('hide');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();

        // Sélectionnez le corps
        var body = $("body");
        // Enlevez l'attribut style
        body.removeAttr("style");
    }



});



