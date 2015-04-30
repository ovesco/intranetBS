/**
 * Supprimer une attribution
 */
$('.button-remove-distinction').click(function() {

    var id = $(this).attr("data-id");

    noty({
        text: 'Etes-vous sur de vouloir retirer cette distinction ?',
        type: 'confirm',
        buttons: [
            {
                addClass: 'ui green button', text: 'Oui', onClick: function ($noty) {
                $noty.close();

                $.ajax({
                    url: Routing.generate('interne_structure_remove_distinction', {distinction: id}),
                    type: 'GET',
                    success: function() {
                        noty({text: 'Distinction retirée', type: 'information'});

                        $('.button-remove-distinction[data-id="' + id + '"]').parent().parent().remove();
                    }
                });
            }
            },
            {
                addClass: 'ui button', text: 'Annuler', onClick: function ($noty) {
                $noty.close();
            }
            }
        ]
    });
});


/**
 * cliqué lorsque l'on veut ajouter une attribution à 1 membre donné
 */
$('.button-single-add-distinction').click(function() {

    var id = $(this).attr("data-id");

    var st = [id];
    addDistinction(st);
});

/**
 * Ajoute une distinction à des membres
 * Les membres doivent être rangés sous la forme d'un array
 * @param membres
 */
function addDistinction(membres) {

    if(Object.prototype.toString.call( membres ) != '[object Array]' )
        noty({text:"Erreur système, contacter le webmaster. La fonction nécessite un array, " + membres + " donné", type: "error"});

    else {

        alert(membres);

        $('#add-distinction-amount-concerned').text(membres.length);
        $('#AppBundle_obtention_distinction_membres').val(membres.join());
        showModal('#modal-distinction-add-obtention');
    }
}

/**
 * Cliqué pour ajouter une attribution unique
 */
$('.add-single-attribution').click(function() {

    var id = $(this).attr("data-id");
    addAttribution([id]);
});

/**
 * Permet de terminer une attribution unique
 */
$('.button-terminer-single-attribution').click(function() {

    var id = $(this).attr("data-attribution-id");
    terminerAttributions([id]);
});

/**
 * Ouvre la modale pour terminer des attributions
 * @param attributions
 */
function terminerAttributions(attributions) {

    if(Object.prototype.toString.call( attributions ) != '[object Array]' )
        noty({text:"Erreur système, contacter le webmaster. La fonction nécessite un array, " + attributions + " donné", type: "error"});

    else {

        $('#attributions-ids').val(attributions.join());
        showModal('#modal-terminer-attribution');
    }
}

/**
 * Ajoute des attributions à des membres
 * Le travail à faire ici est un peu plus conséquent, car on ne peut pas génerer de formulaire statique
 * On doit donc demander au serveur de nous génerer un formulaire a l'ancienne et de nous le filer
 * a la bien
 * @param membres
 */
function addAttribution(membres) {

    if(Object.prototype.toString.call( membres ) != '[object Array]' )
        noty({text:"Erreur système, contacter le webmaster. La fonction nécessite un array, " + membres + " donné", type: "error"});

    else {

        $.ajax({
            url: Routing.generate("interne_attribution_render_formulaire_modal"),
            data:  {membres: membres},
            type: 'GET',
            success: function(data) {

                $('#add_attribution_custom_form_container').html(data);
                showModal('#modal-add-attribution');
            },

            error: function(d) {
                alert(JSON.stringify(d));
            }
        })
    }
}