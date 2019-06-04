<?php

namespace App\Utils;

class Rewarder
{

    public function rewarder($points) // la fonction prend en paramètre le nombre de point de l'utilisateur
    {
        /*
            - Le nombre de points détermine le reward
            - on augmente de reward par palliers de 200 points
            - il y a 6 palliers
                
         */
        // si le nombre de points est > 1000, on est à reward = 5
        if ($points >= 1000){
            $reward = 6;
        } 
        elseif ($points <1000 && $points >= 800) {
            $reward = 5;
        }
         elseif ($points <800 && $points >= 600) {
            $reward = 4;
        } 
        elseif ($points < 600 && $points >= 400) {
            $reward = 3;
        } 
        elseif ($points < 400 && $points >= 200) {
            $reward = 2;
        } else {
            $reward = 1;
        }

        // on renvoit le reward à insérer
        return $reward;

    }


}