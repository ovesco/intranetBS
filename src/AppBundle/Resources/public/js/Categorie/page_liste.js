/**
 * Created by NicolasUffer on 21.07.15.
 */

$('.addCategorieForm').click(function(){
    var url = Routing.generate('categorie_new');
    getModal(null, url)
});

$('.editCategorie').click(function(){
    var id = $(this).data('id');
    var url = Routing.generate('categorie_edit',{categorie: id});
    getModal(null, url)
});