// je récupère le bouton "accepter"
var $acceptButton = $('#accept');
// je récupère le bouton "refuser"
var $refuseButton = $('#refuse');

// j'ajoute des écouteurs d'évènement sur ces 2 boutons
$acceptButton.on('click', handleAcceptButton)

$refuseButton.on('click', handleRefuseButton)

function handleAcceptButton(event){

    // je supprime le comportement par défaut)
    event.preventDefault();
    //console.log('click on accept');

    // je récupère le formulaire
    var $form = $acceptButton.parent();
    var $donationId = $form.data('id');
    //console.log($donationId);

    // Appel Ajax vers la route /dons/{id}/accept
    $.ajax(
    {
      url: '/dons/' + $donationId + '/accept', // url de la méthode accept
      method: 'POST', // La méthode HTTP souhaité pour l'appel Ajax (GET ou POST)
      data: { // l'id du don à envoyer avec la requête
          'donationId' : $donationId
      }
    }
  ).done(function(response) { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec succès" et je récupère le code de réponse en paramètre
      //console.log(response); // debug
      if(1 == response.code) {

        // on retire le don du tableau
        var $lineToDelete = $form.parent().parent();
        //console.log($lineToDelete);
        $lineToDelete.remove();
        // on affiche un FlashMessage
        var $flashMessage = $('<div class="alert alert-success mt-3"> Vous avez accepté la demande de l\'association, elle va être notifiée et prendra contact avec vous</div>');
        $('#manage-dons').before($flashMessage);
      }
      
      // TODO faire les actions souhaitées après la récupération de la réponse
  }).fail(function() { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec erreur"
      alert('Réponse ajax incorrecte');
  });

}

function handleRefuseButton(event){
    
    // je supprime le comportement par défaut)
    event.preventDefault();
    // console.log('click on refuse');

    // je récupère le formulaire
    var $form = $acceptButton.parent();
    var $donationId = $form.data('id');
    // console.log($donationId);

    // Appel Ajax vers la route /dons/{id}/accept
    $.ajax(
    {
      url: '/dons/' + $donationId + '/refuse', // url de la méthode accept
      method: 'POST', // La méthode HTTP souhaité pour l'appel Ajax (GET ou POST)
      data: { // l'id du don à envoyer avec la requête
          'donationId' : $donationId
      }
    }
  ).done(function(response) { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec succès" et je récupère le code de réponse en paramètre
      // console.log(response); // debug
      if(1 == response.code) {

        // on retire le don du tableau
        var $lineToDelete = $form.parent().parent();
        // console.log($lineToDelete);
        $lineToDelete.remove();

        // on affiche un FlashMessage
        var $flashMessage = $('<div class="alert alert-success mt-3"> Vous avez refusé la demande de l\'association, votre don est à nouveau disponible à la réservation</div>');
        $('#manage-dons').before($flashMessage);
      }
      
      // TODO faire les actions souhaitées après la récupération de la réponse
  }).fail(function() { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec erreur"
      alert('Réponse ajax incorrecte');
  });
}