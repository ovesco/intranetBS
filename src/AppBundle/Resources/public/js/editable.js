
var editable = {


    /**
     * Appelée lorsque l'on a cliqué sur un champs modifiable
     * @param obj le td cliqué
     */
    init: function(obj) {

        if(!$(obj).hasClass('currently-edited')) {
            $(obj).addClass('currently-edited');
            $(obj).attr('data-saved', $(obj).text());

            $(obj).html(editable.getInput(obj));
        }
    },





    /**
     * Va valider les changements réalisés. Appelé lorsqu'on appuie sur le bouton vert
     * L'appel ajax retournera un array contenant les path qui sont requis pour pouvoir réaliser la validation. On les met
     * en valeur sur la page
     * @param obj le bouton cliqué
     */
    apply: function(obj) {

        var saved = $(obj).parent().parent().attr('data-saved'),
            path  = $(obj).parent().parent().attr('data-path'),
            current = $(obj).prev().prev().val();

        if(current != undefined && saved !== current) {

            var dataType  = $(obj).parent().parent().attr('data-type');

            $.ajax({
                url: Routing.generate('interne_ajax_app_modify_property', {path: path, value: current}),
                type: 'GET',
                success: function(data) {

                    //On regarde l'array qui nous a été transmis
                    if(data.length == undefined)
                        alerte.balance('Modification effectuée avec succès !', 'success');

                    else {

                        alerte.balance('La modification est enregistrée, mais il manque des informations pour pouvoir la réaliser.', 'info');
                        for(var i = 0; i < data.length; i++)
                            $("td[data-path='" + data[i] + "']").css('background', '#ccf8ff');

                    }



                    var td = $(obj).parent().parent();
                    $(td).attr('data-saved', current);

                    editable.backup($(obj).parent().parent());
                },
                error: function (data) {
                    alerte.balance("Erreur lors de la modification, essayez d'actualiser la page et réessayez.", 'error');
                }
            });
        }

        else
            editable.abort(obj);
    },





    /**
     * Permet d'annuler une modification (appelée lorsqu'on a cliqué sur le bouton annulé).
     * Va annuler l'état de modification et remettre la valeur initiale
     * @param btn le bouton cliqué
     */
    abort: function(btn) {

        var td = $(btn).parent().parent(); //On vise le TD

        /*
         * le setTimeOut est nécessaire, car le bouton annuler se trouve à l'intérieure du TD, du coup, si on le mettait
         * pas, l'état de modification serait instantanément appelé, et on ne pourrait ainsi rien annuler
         */
        setTimeout(function() {editable.backup(td);}, 100);
    },




    /**
     * Backup permet de faire revenir le TD à son état initial A PARTIR DES DONNEES QU'IL CONTIENT. Dans le cas d'une modification
     * on va modifier l'attribut data-save puis appeler backup.
     * @param td l'élément td
     */
    backup: function(td) {

        $(td).empty();
        $(td).text($(td).attr('data-saved'));
        $(td).removeClass('currently-edited');
    },





    /**
     * Retourne l'input qui sera affiché pour modifier la donnée
     * @param obj l'objet cliqué duquel on récupère les informations
     * @return string l'objet input
     */
    getInput : function(obj) {

        var type  = $(obj).attr('data-type'),                   //Récupération du type
            brut  = $(obj).text(),                              //récupération du contenu initial
            input = '<div class="editable-content-container">'; //initialisation de l'input

        switch (type) {

            /*
             * Dans le cas d'un simple champ de texte, on renvoie un input text basique avec la valeur initiale stockée
             * à l'intérieur
             */
            case 'text':
                input += '<input type="text" value="' + brut + '" />';
                break;

            /*
             * Dans le cas d'un select, on va génerer un select. Ce champs prend en option supplémentaire le data-source
             * qui indique la source de ses 'options'. Plusieurs paramètres prédéfinis sont possibles :
             * - genre, qui fournis en option les deux possibilité homme ou femme
             * - boolean qui fournis les options oui ou non
             * Sinon doit se trouver dans le data-source un array json des options à afficher
             */
            case 'select':

                var options      = $(obj).attr('data-source'),
                    options_text = '';

                if(options == 'genre')
                    options_text = '<option value="m">Homme</option><option value="f">Femme</option>';

                else if(options == 'boolean')
                    options_text = '<option value="1">Oui</option><option value="0">Non</option>';

                else {

                    options = JSON.parse(options);
                    for (var i = 0; i < options.length; i++)
                        options_text += '<option value="' + options[i].id + '">' + options[i].value + '</option>';
                }

                input += '<select>' + options_text + '</select>';
                break;

            /*
             * Dans le cas d'un datepicker, on va afficher un input avec le type à date pour afficher le widget google
             * ou mozilla, car les datepickers jquery ne s'affichent que quand l'input est affiché avec la page, or la il
             * est affiché de manière dymanique
             */
            case 'datepicker':

                input += '<input type="date" value="' + brut + '" />';
                break;

            /*
             * dans le cas d'un textarea on affiche une textarea avec sa valeur originelle à l'intérieur
             */
            case 'textarea':
                input += '<textarea>' + brut + '</textarea>';
                break;

            /*
             * Par défaut on affiche le type du texte, un input basique
             */
            default:
                input += '<input type="text" value="' + brut + '" />';
                break;
        }

        /*
         * On termine ensuite l'input avec les boutons valider et annuler
         */
        input += '<a class="ui circular mini red icon button" onclick="editable.abort(this);"><i class="remove icon"></i></a>' +
                 '<a class="ui circular mini green icon button" onClick="editable.apply(this);"><i class="checkmark icon"></i></a>' +
                 '</div>';

        return input;
    }
};