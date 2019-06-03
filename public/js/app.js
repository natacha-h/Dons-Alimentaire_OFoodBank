// var app = {
//     init: function(){
//         console.log('app : init');
        
//         // Je récupere le bouton d'ajout de produit
//         var $button = $('#add-new-product'); 

//         // // Je lui affecte un ecouteur d'événement
//         // $button.one('click', app.handleClickNewProduct);
//         app.displayButtonAddProduct();

//         $('.publish-don').on('click', app.handlePublishDon);

//     },
//     productArray: [],
//     handleClickNewProduct: function(evt) {
//         evt.preventDefault();
//         evt.stopPropagation();
//         console.log('click button')

//         // Je dois afficher le formulaire au click
//         var $formElement = $('#form-template').contents().clone();
//         $('#new-product-form').append($formElement);

//         console.log(evt.currentTarget);

//         // Je retire le bouton
//         evt.currentTarget.remove();
//         $('#new-product-form').removeClass('hide-my-form');
//         // Au click sur le bouton d'ajout de produit
//         // J'ajoute le produit dans un tableau
//         $validProduct = $('#btn-add-product');
//         // Je lui attache un evenement
//         $validProduct.on('click', app.handleValidProduct);
//     },
//     handleValidProduct: function(evt){
//         evt.preventDefault();
//         console.log(app.productArray);

//         var expiryDate = [];
//         expiryDate.push({
//             day: $('#productDateDay').val(),
//             month: $('#productDateMonth').val(),
//             year: $('#productDateYear').val()
//         })

//         // Tableau d'infos d'un produit
//         app.productArray.push(
//             {
//                 productName: $("#productName").val(),
//                 productQuantity: $("#productQuantity").val(),
//                 productCategory: $("#productCategory").val(),
//                 productDate: expiryDate,
//                 productDescription: $("#productDescription").val(),
//             }
//         );
        
//         app.displayListProduct();
//         app.displayButtonAddProduct();
//         $('#new-product-form').addClass('hide-my-form');

//         console.log('Le produit est validé');
//     },
//     displayListProduct: function(){
//         // Affiche la liste des produits dynamiquement
//         var $productData = app.productArray[app.productArray.length-1].productName + ' - Quantité : ' + app.productArray[app.productArray.length-1].productQuantity + ' - ' +app.productArray[app.productArray.length-1].productCategory + ' - ' + app.productArray[app.productArray.length-1].productDescription;
//         var $liElement = $('<li>').addClass('list-group-item').text($productData);
//         $('.list-group').append($liElement);
//     },
//     displayButtonAddProduct: function(){
//         // Réaffiche le bouton pour ajouter un nouveau produit lorsque
//         // La soumission du produit précédent est validée
//         var $button = $('<button>').addClass('btn btn-info').attr('type', 'submit').attr('value', 'Ajouter un produit').html('Ajouter un produit');
//         $button.on('click', app.handleClickNewProduct);
//         $('#btn-zone').append($button);
//     },
// };

// $(app.init);

// Déclare variable de stockage des forms
var $collectionHolder;

// Prépare un bouton d'ajout
var $addNewProduct = $('<a href="#" class="btn btn-info">Ajouter un nouveau produit</a>');

// Quand le document est pret
$(document).ready(function(){
    // Je récupere mes formulaires
    $collectionHolder = $('#product_list');

    // J'ajoute le bouton d'ajout de produit au dom
    $collectionHolder.append($addNewProduct);

    // Je crée un index dans chaque formulaire en fonction de leur nombre
    $collectionHolder.data('index', $collectionHolder.find('.panel').length);

    // J'ajoute un bouton de suppression de produit
    $collectionHolder.find('.panel').each(function(){
        addRemoveBtn($(this));
    });  

    // Je gere le click sur l'ajout d'un nouveau produit
    $addNewProduct.on('click', handleClickAddNewProduct)
});

function addRemoveBtn ($panel) {
    // Je prépare mon bouton de suppression
    var $removeBtn = $('<a href="#" class="btn btn-danger">Supprimer le produit de la liste</a>');

    // Je crée le footer du panel
    var $panelFooter = $('<div class="panel-footer"></div>').append($removeBtn);

    // J'ajoute un event sur le click de ce bouton
    $removeBtn.on('click', handleRemoveBtnClick);

    // J'ajoute le footer au panel
    $panel.append($panelFooter);
}

function handleRemoveBtnClick (event) {
    // console.log('suppression');
    event.preventDefault();
    // Je cible toute la div et sa parente de .panel
    $(event.target).parents('.panel').slideUp(500, function(){
        // Je supprime cet element a la fin de l'animation
        $(this).remove();
    });
}

function handleClickAddNewProduct (event) {
    // Cette méthode me permet de créer un nouveau formulaire
    // Elle me permet aussi d'ajouter le bouton remove
    event.preventDefault();
    console.log('ajout nouveau form');
    // Je récupere le prototype
    var prototype = $collectionHolder.data('prototype');

    // Je récupere l'index du prototype
    var index = $collectionHolder.data('index');

    // Je crée le formulaire 
    var newForm = prototype;

    // On va remplace le __name__ par l'index du formulaire
    // Il prend un regex (g => globally a chaque fois qu'il va rencontrer __name__
    // il va le changer par l'index)
    newForm = newForm.replace(/__name__/g, index);

    // J'implémente l'index pour le nouveau form
    $collectionHolder.data('index', index++);

    // Je peux maintenant crée le panel
    var $panel = $('<div class="panel panel-warning"><div class="panel-heading"></div></div>');

    // Je crée le body et je lui ajoute le formulaire
    var $panelBody = $('<div class="panel-body"></div>').append(newForm);

    // J'ajoute le panelBody dans le panel
    $panel.append($panelBody);

    // J'ajoute le remove button
    addRemoveBtn($panel);
    
    // J'ajoute le panel dans le DOM
    // J'ajoute avant le btn car je souhaite qu'il soit toujours en bas de mon site
    $addNewProduct.before($panel);
}