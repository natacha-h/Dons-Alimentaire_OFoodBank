$(document).ready(function(){
    // console.log('hello')
    // On cible l'input
    $('input[type="file"]').change(function(e){
        // On récupere le nom du fichier
        var fileName = e.target.files[0].name;
        // console.log(fileName)
        // On fournit en attribut le nom de l'image
        // Pour l'utiliser dans le css
        $('.custom-file-label').attr('imgName', fileName);
    });

    // Je positionne mon bouton publier a droite
    $('#donation_Publier').parent().addClass('set-right');

});