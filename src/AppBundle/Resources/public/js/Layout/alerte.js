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
     * @param type string le type parmi 'info', 'warning', 'error', 'success'
     * @param delay la durée d'affichage (en ms)
     * @return integer l'id de l'alerte
     */
    send: function (message, type, delay) {

        delay = (delay === undefined || delay === null) ? 10000000 : delay;
        noty({
            text : message,
            type : type
        });
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


$(document).ready(function(){
    //Noty defaults
    $.noty.defaults = {
        layout: 'topRight',
        theme: 'defaultTheme', // or 'relax'
        type: 'alert',
        text: '', // can be html or string
        dismissQueue: true, // If you want to use queue feature set this true
        template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
        animation: {
            open: {height: 'toggle'}, // or Animate.css class names like: 'animated bounceInLeft'
            close: {height: 'toggle'}, // or Animate.css class names like: 'animated bounceOutLeft'
            easing: 'swing',
            speed: 500 // opening & closing animation speed
        },
        timeout: false, // delay for closing event. Set false for sticky notifications
        force: false, // adds notification to the beginning of queue when set to true
        modal: false,
        maxVisible: 5, // you can set max visible notification for dismissQueue true option,
        killer: false, // for close all notifications before show
        closeWith: ['click'], // ['click', 'button', 'hover', 'backdrop'] // backdrop click will close all notifications
        callback: {
            onShow: function() {},
            afterShow: function() {},
            onClose: function() {},
            afterClose: function() {},
            onCloseClick: function() {}
        },
        buttons: false // an array of buttons
    };
});


