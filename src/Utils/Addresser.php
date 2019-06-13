<?php

namespace App\Utils;

class Addresser
{
    public function addresser($street)
    {
        // On remplace les espaces par des +
        $streetPlus = preg_replace( '/[^a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*/', '+', trim(strip_tags($street)));

        // On retourne la chaine de caractere modifiée
        return $streetPlus;
    }
}