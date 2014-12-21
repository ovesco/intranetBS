/**
 * Alert.js
 * petite classe permettant de balancer des alertes sur la page de manière assez simple. Les alertes sont affichées dans
 * la barre header
 */
var alerte = {

    /**
     * On stocke ici toutes les alertes affichées pour pouvoir les supprimer, savoir combien y'en a...
     */
    alerts : [],


    /**
     * Envoie une alerte simple.
     * @param title string le titre de l'alerte
     * @param message string le message
     * @param type string le type parmi 'info', 'warning', 'danger', 'success'
     * @param icon string si on veut un icone avec
     * @return integer l'id de l'alerte
     */
    balance : function(message, type) {

        var id   = Math.floor((Math.random() * 1000) + 1),
            html = '<div id="' + id + '" class="ui ' + type + ' message"><i onclick="alerte.dismiss(' + id + ');" class="close icon"></i><div class="header">Alerte</div>' +
                   '<p>' + message + '</p></div></div>';

        this.alerts.push(id);

        $('#alerts-bag').append(html);

        return id;
    },

    /**
     * Supprimme une alerte
     * @param id integer l'id de l'alerte
     */
    dismiss : function(id) {

        var index = this.alerts.indexOf(id);

        this.alerts.splice(index, 1);

        $('#' + id).remove();
    }
};