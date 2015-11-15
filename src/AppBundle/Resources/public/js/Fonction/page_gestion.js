$('.addFonction').click(function(){

    getModal(null,Routing.generate('app_fonction_add'));

});

$('.editFonction').click(function(){

    var id = $(this).data('id');
    getModal(null,Routing.generate('app_fonction_edit',{fonction: id}));

});

