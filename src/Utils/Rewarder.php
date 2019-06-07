<?php

namespace App\Utils;

use App\Repository\RewardRepository;

class Rewarder
{
    private $rewardRepository;

    public function __construct(RewardRepository $rewardRepository)
    {
        $this->rewardRepository = $rewardRepository;
    }

    public function rewarder($points) // la fonction prend en paramètre le nombre de point de l'utilisateur
    {
        /*
            - Le nombre de points détermine le reward
            - on augmente de reward par palliers de 200 points
            - il y a 6 palliers
                
         */
        // si le nombre de points est > 1000, on est à reward = 6
        if ($points >= 1000){
            $rewardId = 6;
        } 
        elseif ($points <1000 && $points >= 800) {
            $rewardId = 5;
        }
         elseif ($points <800 && $points >= 600) {
            $rewardId = 4;
        } 
        elseif ($points < 600 && $points >= 400) {
            $rewardId = 3;
        } 
        elseif ($points < 400 && $points >= 200) {
            $rewardId = 2;
        } else {
            $rewardId = 1;
        }

        // on crée un objet Reward
        $reward = $this->rewardRepository->findRewardById($rewardId);

        // on renvoit l'objet reward à insérer
        return $reward;

    }


}