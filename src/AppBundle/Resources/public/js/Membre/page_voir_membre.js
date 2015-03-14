$('#membre-infos-context .menu .item').tab({
    context: $('#membre-infos-context')
});

$('#ajouter-distinction').click(function() {
    var idMembre = { idMembre: $(this).data('idMembre') };
    getModal(idMembre, Routing.generate('date-distinction_get_modal'));
})

$('#ajouter-attribution').click(function() {
    var idMembre = { idMembre: $(this).data('idMembre') };
    getModal(idMembre, Routing.generate('attribution_get_modal'));
})

$('#modifier-famille').click(function() {
    modifyFamilleTriggered();
});

$('#modifier-numero-bs').click(function() {
    showModal('#membre-edit-numero-bs');
});

$('.editable').click(function() {

    editable.init($(this));
});

/**
 * permet simplement de cacher le dimmer de l'adresse du membre si celle-ci est vide
 * @param btn le bouton qui a triggeré l'action
 */
function createAdresse(btn) {

    $(btn).parent().parent().parent().removeClass('active');

}

/**
 * Suppression d'attributions ou de distinctions pour le membre donné
 * @param btn le bouton qui a triggeré l'action
 * @param membre l'id du membre
 * @param type 'distinction' ou 'attribution'
 * @param id l'id de l'attribution ou distinction
 */
function removeAttributionOrDistinction(btn, membre, type, id) {

    if(confirm('Etes-vous sur de vouloir supprimer ça ?')) {
        $.ajax({
            url: Routing.generate('interne_ajax_membre_remove_attr_dist', {
                membre: membre,
                type: type,
                obj: id
            }),
            type: 'GET',
            success: function () {

                $(btn).parent().parent().remove();
            },
            error: function (data) {
                //alert('Impossible de supprimer l\'objet');
                alert(JSON.stringify(data));
            }
        });
    }
}

/**
 * Cette méthode permet de vérifier si un numéro BS est occupé ou non
 * @param numero le numéro à vérifier
 */
function verifyNumeroBs() {

    var numero = $('#new-numero-bs-input').val();

    $.ajax({
        url: Routing.generate('interne_membre_ajax_verify_numero_bs', {numero:numero}),
        type: 'GET',
        success:function(data) {

            if(data == true) {
                $('#button-verify-numero-bs').attr("class", 'ui red right labeled icon button');
                $('#validate-modf-numero-bs').attr("class", "ui green disabled button");
            }
            else {
                $('#button-verify-numero-bs').attr("class", 'ui green right labeled icon button');
                $('#validate-modf-numero-bs').attr("class", "ui green button");
            }
        },
        error:function(data) {
            alert('Il y a eu une erreur lors de la vérification. Veuillez réessayer plus tard');
        }
    })
}

/**
 * Lance la procédure de modification du numéro BS du membre
 * @param membre l'id du membre
 */
function modifiyNumeroBs(membre) {

    var numero = $('#new-numero-bs-input').val(),
        path = 'membre.' + membre + '.numeroBs';

    $.ajax({

        url: Routing.generate('interne_ajax_app_modify_property', {path:path, value:numero}),
        type: 'GET',
        success:function() {

            var refresh = confirm('Numéro BS modifié. Actualisez la page pour voir les modifications');
            if(refresh)
                location.reload();
        },
        error:function(d) {
            alert(JSON.stringify(d));
            //alert("Erreur lors de la modification du numéro BS");
        }
    })
}

/**
 * Procédure de modification de la famille. En premier lieu, on récupère l'ensemble des familles pour pouvoir
 * les afficher, puis on affiche la modale
 */
function modifyFamilleTriggered() {

    $.ajax({
        url: Routing.generate('interne_ajax_get_familles_as_json'),
        type:'POST',
        success:function(data) {

            var html = '';
            for (var i = 0; i < data.length; i++)
                html += '<div class="item" data-value="' + data[i].id + '">' + data[i].nom + '</div>';

            $('#update-famille-membre-select').html(html);
            showModal('#membre-edit-famille');
        },

        error:function(data){
            alert(JSON.stringify(data));
        }
    });
}

/**
 * Permet de modifier la famille du membre
 */
function modifyFamille(){

    //todo: trouver un moyen de récupéré le id du membre
    var id = null;
famille = $('#famille-membre-modif-dropdown').dropdown('get value');

$.ajax({
    url:Routing.generate('membre_modify_famille', {membre:id, famille:famille}),
    type:'GET',
    success:function(){

        var reload = confirm("Famille modifiée. Actualisez la page pour voir les modifications");
        if(reload)
            location.reload();
    },
    error:function(d){alert(JSON.stringify(d));}
})
}

/**
 * Permet d'ajouter le membre à un listing
 * @param id int l'id du membre
 * @param token string le token de la liste
 * @param btn obj le bouton cliqué
 */
function addToListing(id, token, btn) {

    listing.addElements(token, id);
    $(btn).firstChild().attr("class", 'ui green empty circular label');
}