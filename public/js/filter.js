// récupérer le bouton
var $filterBtn = $('#filter');
// ajouter un écouteur d'évènement
$filterBtn.on('click', handleFilterBtn);

function handleFilterBtn($evt){
    //bloquer l'envoi
    $evt.preventDefault();
    console.log('click on Filter');
    // récupérer l'id de la catégorie choisie
    var $catId = $('#choice').data('id');
    console.log($catId);
}