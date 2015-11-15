$('.addModelForm').click(function(){
    getModal(null,Routing.generate('app_model_add'));
});

$('.editModel').click(function(){
    var id = $(this).data('id');
    getModal(null,Routing.generate('app_model_edit',{model: id}));
});

