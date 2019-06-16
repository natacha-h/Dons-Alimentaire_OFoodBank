// je récupère le bouton "accepter"
var acceptButton = document.getElementById('accept');
// je récupère le bouton "refuser"
var refuseButton = document.getElementById('refuse');

// j'ajoute des écouteurs d'évènement sur ces 2 boutons
acceptButton.addEventListener('click', handleAcceptButton);

refuseButton.addEventListener('click', handleRefuseButton);

function handleAcceptButton(event){

    // je supprime le comportement par défaut)
    event.preventDefault();
    console.log('click on accept');

}

function handleRefuseButton(event){
    
    // je supprime le comportement par défaut)
    event.preventDefault();
    console.log('click on refuse');
}