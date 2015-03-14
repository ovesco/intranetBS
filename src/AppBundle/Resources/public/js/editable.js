
var editable = {


    /**
     * Appelée lorsque l'on a cliqué sur un champs modifiable
     * @param l'élément cliqué
     */
    init: function(property) {

        if(!$(property).hasClass('currently-edited')) {
            $(property).addClass('currently-edited');
            $(property).attr('data-saved', $(property).text());

            $(property).html(editable.getInput(property));

            $(property).find('input').focus();

            /* Apply modification on ENTER */
            $(property).bind("enterKey", function(e){

                if ($(property).find('.edited-input').is('textarea')) // omit texfields, ENTER is new line
                    return;

                editable.apply(property);
            });

            /* Abort modification on ESC */
            $(property).bind("escKey", function(e){
                editable.abort(property);
            });

            /* Raise event depending on key pressed */
            $(property).keyup(function(e){
                var keyCode = (event.keyCode ? event.keyCode : event.which);

                switch(keyCode) {
                    case 13:
                        $(this).trigger("enterKey");
                        break;

                    case 27:
                        $(this).trigger("escKey");
                        break;
                }
            });

            $(property).focusout(function(e) {
                //TODO: il faut gérer le focus out : demander à l'utilisateur s'il veut annuler ou sauver
            });
        }
    },



    /**
     * Va valider les changements réalisés. Appelé lorsqu'on appuie sur le bouton vert
     * L'appel ajax retournera un array contenant les path qui sont requis pour pouvoir réaliser la validation. On les met
     * en valeur sur la page
     * @param btn le bouton cliqué
     */
    applyClick: function(btn) {

        editable.apply($(btn).parent().parent());
    },


    apply: function(property) {

        var saved = $(property).attr('data-saved'),
            path  = $(property).attr('data-path'),
            current = $(property).find('.edited-input').val();


        if(current != undefined && saved !== current) {

            var dataType  = $(property).parent().parent().attr('data-type');

            $.ajax({
                url: Routing.generate('interne_ajax_app_modify_property', {path: path, value: current}),
                type: 'GET',
                success: function(data) {

                    //On regarde l'array qui nous a été transmis
                    if(data.length == undefined)
                        alerte.send('Modification effectuée avec succès !', 'success');

                    else {

                        alerte.send('La modification est enregistrée, mais il manque des informations pour pouvoir la réaliser.', 'info');
                        for(var i = 0; i < data.length; i++)
                            $("[data-path='" + data[i] + "']").css('background', '#ccf8ff');

                    }

                    $(property).attr('data-saved', current);

                    editable.restore(property);
                },
                error: function (data) {
                    alerte.send("Erreur lors de la modification, essayez d'actualiser la page et réessayez.", 'error');
                }
            });
        }

        else
            editable.abort(property);
    },


    /**
     * Permet d'annuler une modification (appelée lorsqu'on a cliqué sur le bouton annulé).
     * Va annuler l'état de modification et remettre la valeur initiale
     * @param btn le bouton cliqué
     */
    abortClick: function(btn) {
      editable.abort($(btn).parent().parent());
    },



    abort: function(property) {

        /*
         * le setTimeOut est nécessaire, car le bouton annuler se trouve à l'intérieure du TD, du coup, si on le mettait
         * pas, l'état de modification serait instantanément appelé, et on ne pourrait ainsi rien annuler
         */
        setTimeout(function() {editable.restore(property);}, 100);
    },




    /**
     * Backup permet de faire revenir le TD à son état initial A PARTIR DES DONNEES QU'IL CONTIENT. Dans le cas d'une modification
     * on va modifier l'attribut data-save puis appeler restore.
     * @param property l'élément editable
     */
    restore: function(property) {

        $(property).empty();
        $(property).text($(property).attr('data-saved'));
        $(property).removeClass('currently-edited');
    },





    /**
     * Retourne l'input qui sera affiché pour modifier la donnée
     * @param property l'objet cliqué duquel on récupère les informations
     * @return string l'objet input
     */
    getInput : function(property) {

        var type  = $(property).attr('data-type'),                   //Récupération du type
            brut  = $(property).text(),                              //récupération du contenu initial
            input = '<div class="editable-content-container">'; //initialisation de l'input

        switch (type) {

            /*
             * Dans le cas d'un simple champ de texte, on renvoie un input text basique avec la valeur initiale stockée
             * à l'intérieur
             */
            case 'text':
                input += '<input class="edited-input" type="text" value="' + brut + '" />';
                break;

            /*
             * Dans le cas d'un select, on va génerer un select. Ce champs prend en option supplémentaire le data-source
             * qui indique la source de ses 'options'. Plusieurs paramètres prédéfinis sont possibles :
             * - genre, qui fournis en option les deux possibilité homme ou femme
             * - boolean qui fournis les options oui ou non
             * Sinon doit se trouver dans le data-source un array json des options à afficher
             */
            case 'select':

                var options      = $(property).attr('data-source'),
                    options_text = '';

                if(options == 'genre')
                    options_text = '<option value="m">Homme</option><option value="f">Femme</option>';

                else if(options == 'boolean')
                    options_text = '<option value="1">Oui</option><option value="0">Non</option>';


                else if(options == 'choices')
                    options_text = $(property).find('.select_choices').html();

                else {

                    options = JSON.parse(options);
                    for (var i = 0; i < options.length; i++)
                        options_text += '<option value="' + options[i].id + '">' + options[i].value + '</option>';
                }

                input += '<select class="edited-input">' + options_text + '</select>';
                break;

            /*
             * Dans le cas d'un datepicker, on va afficher un input avec le type à date pour afficher le widget google
             * ou mozilla, car les datepickers jquery ne s'affichent que quand l'input est affiché avec la page, or la il
             * est affiché de manière dymanique
             */
            case 'datepicker':

                input += '<input class="edited-input" type="date" value="' + brut + '" />';
                break;

            /*
             * dans le cas d'un textarea on affiche une textarea avec sa valeur originelle à l'intérieur
             */
            case 'textarea':
                input += '<textarea class="edited-input">' + brut + '</textarea>';
                break;

            /*
             * Par défaut on affiche le type du texte, un input basique
             */
            default:
                input += '<input class="edited-input" type="text" value="' + brut + '" />';
                break;
        }

        /*
         * On termine ensuite l'input avec les boutons valider et annuler
         */
        input += '<a class="ui circular mini red icon button" onclick="editable.abortClick(this);"><i class="remove icon"></i></a>' +
                 '<a class="ui circular mini green icon button" onClick="editable.applyClick(this);"><i class="checkmark icon"></i></a>' +
                 '</div>';

        return input;
    }
};