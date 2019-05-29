<?php

namespace App\DataFixtures\Faker;

class CategoryProvider extends \Faker\Provider\Base{

    protected static $categoryName = [
        'Viande',
        'Poisson',
        'Boisson',
        'Legumes',
        'Fruits',
        'Céréales',
        'Charcuterie',
        'Épicerie sucrée',
        'Huile/Vinaigre',
        'Pâtes/Riz/Féculents',
        'Produits laitiers',
        'Pain',
        'Desserts',
        'Autres',
        ];

    public static function categoryName(){
    return static::randomElement(static::$categoryName);
    }

}