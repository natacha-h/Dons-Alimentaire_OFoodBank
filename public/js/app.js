var app = {
    init: function(){
        console.log('app : init');
        
        // Je récupere le bouton d'ajout de produit
        var $button = $('#add-new-product'); 

        // // Je lui affecte un ecouteur d'événement
        // $button.one('click', app.handleClickNewProduct);
        app.displayButtonAddProduct();

    },
    productArray: [],
    handleClickNewProduct: function(evt) {
        evt.preventDefault();
        evt.stopPropagation();
        console.log('click button')

        // Je dois afficher le formulaire au click
        var $formElement = $('#form-template').contents().clone();
        $('#new-product-form').append($formElement);

        console.log(evt.currentTarget);

        // Je retire le bouton
        evt.currentTarget.remove();

        // Au click sur le bouton d'ajout de produit
        // J'ajoute le produit dans un tableau
        $validProduct = $('#btn-add-product');
        // Je lui attache un evenement
        $validProduct.on('click', app.handleValidProduct);
    },
    handleValidProduct: function(evt){
        evt.preventDefault();
        console.log(app.productArray);
        app.productArray.push(
            {
                // product: 'product',
                productValue: $("#product").val(),
                // quantity: 'quantity',
                quantityValue: $("#quantity").val(),
                // category: 'category',
                categoryValue: $("#category").val(),
                // description: 'description',
                descriptionValue: $("#description").val(),
            }
        );

        app.displayListProduct();
        app.displayButtonAddProduct();
        $('.form-product').remove();

        console.log('Le produit est validé');
    },
    displayListProduct: function(){
        var $productData = app.productArray[app.productArray.length-1].productValue + ' - Quantité : ' + app.productArray[app.productArray.length-1].quantityValue + ' - ' +app.productArray[app.productArray.length-1].categoryValue + ' - ' + app.productArray[app.productArray.length-1].descriptionValue;
        var $liElement = $('<li>').addClass('list-group-item').text($productData);
        $('.list-group').append($liElement);
    },
    displayButtonAddProduct: function(){
        var $button = $('<button>').addClass('btn btn-info').attr('type', 'submit').attr('value', 'Ajouter un produit').html('Ajouter un produit');
        $button.on('click', app.handleClickNewProduct);
        $('#btn-zone').append($button);
    }
};

$(app.init);