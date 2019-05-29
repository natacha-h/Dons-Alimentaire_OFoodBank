<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\Status;
use App\Entity\Category;
use App\Entity\Product;
use Faker\ORM\Doctrine\Populator;
use Nelmio\Alice\Loader\NativeLoader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Datafixtures\Faker\CategoryProvider;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $encoder;

    protected $categoryName = [
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

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        // DONNÉES FIXES => Je crée mes users et leur rôle à la main pour leur donner la valeur que je veux
        $faker = \Faker\Factory::create();

        // Status - Disponible
        $status = new Status();
        $status->setName('disponible');
        $manager->persist($status);
        
        // Status - Réservé
        $status = new Status();
        $status->setName('réservé');
        $manager->persist($status);
        
        // Status - Donné
        $status = new Status();
        $status->setName('donné');
        $manager->persist($status);

        // On crée nos fausses données de nourriture pour le product
        
        $faker->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($faker));    

        $populator = new Faker\ORM\Doctrine\Populator($faker, $manager);

        $populator->addEntity('App\Entity\Category', 14, array(
            'name' => function() { 
                
                $length = count($this->categoryName);
                unset($this->categoryName[$length]);
                $length = count($this->categoryName);
                $currentIndex = $length-1;
                if($currentIndex >= 0 && isset($this->categoryName[0])){
                    return $this->categoryName[$currentIndex];
                }
            }
        ), [
            // function($category){
            //     $length = count($this->categoryName);
            //     $currentIndex = $length-1;
            //     if($currentIndex >= 0 && isset($this->categoryName[0])){
            //         $category->setName($this->categoryName[$currentIndex]);
            //         unset($this->categoryName[$currentIndex]);
            //     }
                
            // }
        ]);
        $inserted = $populator->execute();
        // dump($inserted); die;

        $populator->addEntity('App\Entity\Product', 100, array(
            'name' =>function() use ($faker) {return $faker->foodName();},
            'quantity' => function() use ($faker) { return $faker->numberBetween(0, 15); },
            'expiry_date' => function() use ($faker) { return $faker->dateTime(); },
        ));
        
        $populator->execute();
                
        
        $loader = new NativeLoader();

        //importe le fichier de fixtures et récupère les entités générés
        $entities = $loader->loadFile(__DIR__.'/fixtures.yml')->getObjects();
        
        //empile la liste d'objet à enregistrer en BDD
        foreach ($entities as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
