// Déclare variable de stockage des forms
var $collectionHolder;

var currentIndex = 0;

// Prépare un bouton d'ajout
var $addNewProduct = $('<a href="#" class="btn my-4 px-3">Ajouter un nouveau produit</a>');
$addNewProduct.css('background-color', "#7ad166");
$addNewProduct.css('color', 'white');


// Quand le document est pret
$(document).ready(function(){
    // console.log('app : init');

    // Je récupere mes formulaires
    $collectionHolder = $('#product_list');

    // J'ajoute le bouton d'ajout de produit au dom
    $collectionHolder.append($addNewProduct);

    // Je crée un index dans chaque formulaire en fonction de leur nombre
    $collectionHolder.data('index', $collectionHolder.find('.card').length);

    // J'ajoute un bouton de suppression de produit
    $collectionHolder.find('.card').each(function(){
        addRemoveBtn($(this));
    });  

    // Je gere le click sur l'ajout d'un nouveau produit
    $addNewProduct.on('click', handleClickAddNewProduct);

    // Permet de verifier si l'input est vide ou non en fonction 
    // de son contenu
    $('input').on('focusout', checkIfEmptyInput);
});

function addRemoveBtn ($panel) {
    // console.log('ajout du bouton remove');
    // Je prépare mon bouton de suppression
    var $removeBtn = $('<a href="#" class="btn btn-danger">Supprimer le produit de la liste</a>');

    // Je crée le footer du panel
    var $panelFooter = $('<div class="card-footer"></div>').append($removeBtn);

    // J'ajoute un event sur le click de ce bouton
    $removeBtn.on('click', handleRemoveBtnClick);

    // J'ajoute le footer au panel
    $panel.append($panelFooter);
}

function handleRemoveBtnClick (event) {
    // console.log('suppression');
    event.preventDefault();
    // Je cible toute la div et sa parente de .panel
    $(event.target).parents('.card').slideUp(500, function(){
        // Je supprime cet element a la fin de l'animation
        $(this).remove();
    });
}

function handleClickAddNewProduct (event) {
    // Cette méthode me permet de créer un nouveau formulaire
    // Elle me permet aussi d'ajouter le bouton remove
    event.preventDefault();
    // console.log('ajout nouveau form');
    // Je récupere le prototype
    var prototype = $collectionHolder.data('prototype');

    // Je récupere l'index du prototype
    var index = $collectionHolder.data('index');

    // Je crée le formulaire 
    var newForm = prototype;
    
    // J'implémente l'index pour le nouveau form
    // Me permet de récuperer toutes les valeurs de tous les form
    $collectionHolder.data('index', index+1);

    currentIndex = currentIndex + 1;

    // On va remplace le __name__ par l'index du formulaire
    // Il prend un regex (g => globally a chaque fois qu'il va rencontrer __name__
    // il va le changer par l'index)
    newForm = newForm.replace(/__name__/g, currentIndex);


    // Je peux maintenant crée le panel
    var $panel = $('<div class="card bg-light mt-2"><div class="card-header"><h5>Description du produit</h5></div></div>');

    // Je crée le body et je lui ajoute le formulaire
    var $panelBody = $('<div class="card-body"></div>').append(newForm);

    // J'ajoute le panelBody dans le panel
    $panel.append($panelBody);

    // J'ajoute le remove button
    addRemoveBtn($panel);
    
    // J'ajoute le panel dans le DOM
    // J'ajoute avant le btn car je souhaite qu'il soit toujours en bas de mon site
    $addNewProduct.before($panel);
}

function checkIfEmptyInput(event) {

    // On cherche les inputs qui sont obligatoires
    if(event.target.required){
        // Si ils sont vide on leur donne un bordure rouge
        if(event.target.value.length == 0){
            $(event.target).removeClass('border border-success');
            $(event.target).addClass('border border-danger');
        } 
        // Sinon une bordure verte
        else {
            $(event.target).removeClass('border border-danger');
            $(event.target).addClass('border border-success');
        }
    }
}

// récupération du bouton qui affiche le formulaire d'adresse
var showChangeAdress = document.getElementById('showChangeAdress');
// console.log(showChangeAdress)
if(showChangeAdress != null){
    showChangeAdress.addEventListener('click', handleClickChangeAddress);
}

function handleClickChangeAddress(event){
    event.preventDefault();
    // console.log('click on change adress')
    // je récupère la div qui contient le formulaire d'adresse
    var $addressForm = $('#changeAdress');
    // je retire la class CSS qui cache le formulaire
    $addressForm.removeClass('hide-my-form');
}