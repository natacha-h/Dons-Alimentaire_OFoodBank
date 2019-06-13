var myMap = L.map('map');


$(document).ready(function(){
    console.log('app.init-map')
    // /dons/216
    console.log(window.location.pathname);

    // Etape 1: récupérer l'id qui nous interesse
    var url = window.location.pathname;
    var id = sliceUrl(url);
    console.log(id);

    // Etape 2: transmettre ces données a PHP
    ajaxRequestCoordonates(id);



});

function sliceUrl(url){
    // var url = "http://www.mysite.com/#!/edit/2695";
    // var pieces = url.split("/#!/");
    // pieces = pieces[1].split("/");

    // pieces[0] == "edit"
    // pieces[1] == "2695"

    // On récupere un tableau avec ce qui se trouve apres '/dons/'
    var id = url.split('/dons/');
    // On retourne l'index qui nous interesse
    return id['1'];
}

function ajaxRequestCoordonates(id){
     // Appel ajax vers getCoordonates()
    $.ajax({
        url: '/dons/api/address/' + id +'/coordonates', // URL sur notre API
        method: 'post',
        dataType: 'json' // Le type de données attendu en réponse (text, html, xml, json)
      }).done(function(response) { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec succès" et je récupère le code de réponse en paramètre
        // console.log(response.code);

        // Si ma réponse code vaut 0 je n'affiche pas d'adresse
        if(response.code == 0){
            console.log("reponse vide");
            $('#map').remove();
        } else {
            // Sinon je récupere la
            var lat = response.coordonates[0]["lat"];
            var long = response.coordonates[0]["lon"]

            // Je demande le rendu de la map avec le curseur
            renderMap(lat, long);
        }
      }).fail(function() { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec erreur"
        alert('Réponse ajax incorrecte');
      });
}


function renderMap(lat, long){
    // console.log(long);
    // Je set la vue de la carte sur les coordonées du don
    myMap.setView([lat, long], 12);
    // J'ajoute un lien vers les api
    myMap.attributionControl.addAttribution('<a href="https://www.jawg.io" target="_blank">&copy; Jawg</a> - <a href="https://www.openstreetmap.org" target="_blank">&copy; OpenStreetMap</a>&nbsp;contributors')
    L.tileLayer('https://tile.jawg.io/jawg-sunny/{z}/{x}/{y}.png?access-token=4o3u18qKw699mgtNH6Q47G9L8c3nMJfrSAZhqQIm2Q7PaNA5QpNpeyitPgS1GZx8', {}).addTo(myMap);
    // Je place mon marker
    L.marker([lat, long]).addTo(myMap);
}


// 4o3u18qKw699mgtNH6Q47G9L8c3nMJfrSAZhqQIm2Q7PaNA5QpNpeyitPgS1GZx8