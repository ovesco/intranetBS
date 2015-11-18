

/*
$('#membre_famille_famille').change(function() {

    if($(this).val() == 'NOEXIST') $('#famille-form-segment').fadeIn();

    else {

        /*
         * première chose, on met à jour les éléments visuels
         * Ensuite, on va envoyer une requête au serveur pour récupérer l'adresse de la famille déjà enregistrée,
         * puis on va l'afficher
         *
        var famille = $(this).val();

        $('#adresse-segment').attr("class", 'ui loading segment');

        $.ajax({

            url: Routing.generate('interne_ajax_famille_get_adresses', {famille: famille}),
            type: 'GET',
            success:function(data) {

                //On nettoie les valeurs
                $('#adresses-context input').val('');
                $('#adresses-context input[type=checkbox]').prop('checked', false);

                if(data != null) {
                    /*
                     * On va remplir les adresses disponibles pour la famille avec les données récupérées
                     *
                    //Famille
                    var famille = JSON.parse(data.famille);

                    if(famille != null) {
                        $('#AppBundle_famille_adresse_rue').val(famille.rue);
                        $('#AppBundle_famille_adresse_npa').val(famille.npa);
                        $('#AppBundle_famille_adresse_localite').val(famille.localite);
                        $('#AppBundle_famille_adresse_facturable').prop('checked', famille.facturable);
                        $('#AppBundle_famille_adresse_remarques').text(famille.remarques);
                    }

                    //Pere
                    var pere = JSON.parse(data.pere);
                    if(pere != null) {
                        $('#AppBundle_famille_pere_adresse_rue').val(pere.rue);
                        $('#AppBundle_famille_pere_adresse_npa').val(pere.npa);
                        $('#AppBundle_famille_pere_adresse_localite').val(pere.localite);
                        $('#AppBundle_famille_pere_adresse_facturable').prop('checked', pere.facturable);
                        $('#AppBundle_famille_pere_adresse_remarques').text(pere.remarques);
                    }

                    //Mere
                    var mere = JSON.parse(data.mere);
                    if(mere != null) {
                        $('#AppBundle_famille_mere_adresse_rue').val(mere.rue);
                        $('#AppBundle_famille_mere_adresse_npa').val(mere.npa);
                        $('#AppBundle_famille_mere_adresse_localite').val(mere.localite);
                        $('#AppBundle_famille_mere_adresse_facturable').prop('checked', mere.facturable);
                        $('#AppBundle_famille_mere_adresse_remarques').text(mere.remarques);
                    }

                    $('#adresse-segment').attr("class", 'ui segment');

                }
            },
            error: function(data) {
                alert(JSON.stringify(data));
            }
        });

        //On cache le formulaire de famille
        $('#famille-form-segment').fadeOut();
    }
});

*/