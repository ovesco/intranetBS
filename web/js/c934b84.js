jQuery(document).ready(function() {

    //activation du menu
    $('#structure-groupe-context .menu .item').tab({
        context: $('#structure-groupe-context')
    });

});


function addChild(id,name) {

    jQuery('#groupe-name-parent').val(name);
    jQuery('#groupe-id-parent').val(id);
    showModal('#ajouter-groupe-modale');
}