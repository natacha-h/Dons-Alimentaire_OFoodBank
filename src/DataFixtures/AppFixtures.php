<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Nelmio\Alice\Loader\NativeLoader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // création ds rôles
        $roleGiver = new Role();
        $roleGiver->setCode('ROLE_GIVER');
        $roleGiver->setName('Donateur');
        
        $roleAssoc = new Role();
        $roleAssoc->setCode('ROLE_ASSOC');
        $roleAssoc->setName('Association');
        
        $roleAdmin = new Role();
        $roleAdmin->setCode('ROLE_ADMIN');
        $roleAdmin->setName('Admin');

        $manager->persist($roleGiver);
        $manager->persist($roleAssoc);
        $manager->persist($roleAdmin);
        

                // $populator->addEntity('App\Entity\Category', 14, array(
                    //     'name' => function() {     
                    // $length = count($this->categoryName);
                    // unset($this->categoryName[$length]);
                    // $length = count($this->categoryName);
                    // $currentIndex = $length-1;
                    // if($currentIndex >= 0 && isset($this->categoryName[0])){
                    //     return $this->categoryName[$currentIndex];
                    // }
                    // }
                    // ), [
                    // function($category){
                    //     $length = count($this->categoryName);
                    //     $currentIndex = $length-1;
                    //     if($currentIndex >= 0 && isset($this->categoryName[0])){
                    //         $category->setName($this->categoryName[$currentIndex]);
                    //         unset($this->categoryName[$currentIndex]);
                    //     }
                        
                    // }
                // $inserted = $populator->execute();
                        
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
