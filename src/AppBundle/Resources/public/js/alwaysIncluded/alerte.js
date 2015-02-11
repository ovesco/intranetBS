/**
 * Permet de générer un identifiant unique
 * @returns {string}
 */
function guid() {
    var d = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (d + Math.random()*16)%16 | 0;
        d = Math.floor(d/16);
        return (c=='x' ? r : (r&0x3|0x8)).toString(16);
    });
    return uuid;
};

/**
 * Alert.js
 * petite classe permettant de balancer des alertes sur la page de manière assez simple. Les alertes sont affichées dans
 * la barre header
 */
var alerte = {

    /**
     * On stocke ici toutes les alertes affichées pour pouvoir les supprimer, savoir combien y'en a...
     */
    alerts: [],


    /**
     * Envoie une alerte simple.
     * @param message string le message
     * @param type string le type parmi 'info', 'warning', 'danger', 'success'
     * @param delay la durée d'affichage (en ms)
     * @return integer l'id de l'alerte
     */
    send: function (message, type, delay) {

        var id = guid(),
            html = '<div id="' + id + '" class="ui ' + type + ' message"><i onclick="alerte.dismiss(' + id + ');" class="close icon"></i><div class="header">Alerte</div>' +
                '<p>' + message + '</p></div></div>';

        this.alerts.push(id);

        $('#alerts-bag').append(html);


        //delay de disparition de l'alerte
        if (typeof(delay) != "undefined") {
            setTimeout(function () {
                $('#' + id).remove();
            }, delay);
        }

        return id;
    },

    /**
     * Supprimme une alerte
     * @param id integer l'id de l'alerte
     */
    dismiss: function (id) {

        var index = this.alerts.indexOf(id);

        this.alerts.splice(index, 1);

        $('#' + id).remove();
    }
};