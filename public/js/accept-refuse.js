// je récupère le bouton "accepter"
// var acceptButton = document.getElementById('accept');
var $acceptButton = $('#accept');
// je récupère le bouton "refuser"
var refuseButton = document.getElementById('refuse');

// j'ajoute des écouteurs d'évènement sur ces 2 boutons
// acceptButton.addEventListener('click', handleAcceptButton);
$acceptButton.on('click', handleAcceptButton)

refuseButton.addEventListener('click', handleRefuseButton);

function handleAcceptButton(event){

    // je supprime le comportement par défaut)
    event.preventDefault();
    console.log('click on accept');

    // je récupère le formulaire
    var $form = $acceptButton.parent();
    var $donationId = $form.data('id');
    console.log($donationId);

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
      console.log(response); // debug
      if(1 == response.code) {

        // on retire le don du tableau
        var $lineToDelete = $form.parent().parent();
        console.log($lineToDelete);
        $lineToDelete.remove();
      }
      
      // TODO faire les actions souhaitées après la récupération de la réponse
  }).fail(function() { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec erreur"
      alert('Réponse ajax incorrecte');
  });

}

function handleRefuseButton(event){
    
    // je supprime le comportement par défaut)
    event.preventDefault();
    console.log('click on refuse');
}