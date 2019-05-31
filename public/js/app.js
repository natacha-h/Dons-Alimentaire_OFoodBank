var app = {
    init: function(){
        console.log('app : init');
        
        // Je récupere le bouton d'ajout de produit
        var $button = $('#add-new-product'); 

        // // Je lui affecte un ecouteur d'événement
        // $button.one('click', app.handleClickNewProduct);
        app.displayButtonAddProduct();

        $('.publish-don').on('click', app.handlePublishDon);

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

        console.log($('#productDate').children('option:selected'));

        // Tableau d'infos d'un produit
        app.productArray.push(
            {
                productName: $("#productName").val(),
                productQuantity: $("#productQuantity").val(),
                productCategory: $("#productCategory").val(),
                productDate: $("#productDate").html(),
                productDescription: $("#productDescription").val(),
            }
        );
        
        app.displayListProduct();
        app.displayButtonAddProduct();
        $('#new-product-form').empty();

        console.log('Le produit est validé');
    },
    displayListProduct: function(){
        // Affiche la liste des produits dynamiquement
        var $productData = app.productArray[app.productArray.length-1].productName + ' - Quantité : ' + app.productArray[app.productArray.length-1].productQuantity + ' - ' +app.productArray[app.productArray.length-1].productCategory + ' - ' + app.productArray[app.productArray.length-1].productDescription;
        var $liElement = $('<li>').addClass('list-group-item').text($productData);
        $('.list-group').append($liElement);
    },
    displayButtonAddProduct: function(){
        // Réaffiche le bouton pour ajouter un nouveau produit lorsque
        // La soumission du produit précédent est validée
        var $button = $('<button>').addClass('btn btn-info').attr('type', 'submit').attr('value', 'Ajouter un produit').html('Ajouter un produit');
        $button.on('click', app.handleClickNewProduct);
        $('#btn-zone').append($button);
    },
    handlePublishDon: function(evt){
        evt.preventDefault();

        // Tableau d'infos d'une addresse
        var addressArray = [];
        addressArray.push({
            number: $('#donation_address_number').val(),
            street: $('#donation_address_street').val(),
            zipCode: $('#donation_address_zip_code').val(),
            city: $('#donation_address_city').val()
        })

        // Je recupere les infos du dons en général ainsi que les produits
        var donationArray = [];

        // Je récupere les données
        donationArray.push({
            donationTitle: $('#title-donation').val(),
            donationPic: $('#donation-pic').val(),
            products: app.productArray,
            address: addressArray
        })

        console.log("tableau rempli");
        $.ajax(
            // la variable 'ajaxDeleteURL' est définie via Twig
            // list.html.twig (via une variable JS)   
            {
                url: 'http://127.0.0.1:8000/dons/new/ajax',
                method: 'POST',
                dataType: 'JSON',
                data: {
                    'donation': donationArray // je recupere l'attribut data-id dans la balise <form>
                }
            }
        // Ecouteur du retour de la requête en cas de succès
        ).done(function(result) {

              // data correspond au contenu renvoyé par la réponse
            console.log(result);
            // On supprime la ligne du DOM (la tâche n'existe plus en back)
                // window.location.href = 'http://127.0.0.1:8000/dons'; 
        }).fail(function() {
            alert('ajax failed');
        });
    }
};

$(app.init);