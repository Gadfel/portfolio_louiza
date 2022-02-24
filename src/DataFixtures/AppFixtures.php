<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Apropos;
use App\Entity\Projets;
use App\Entity\Competences;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        
        $faker = Factory::create();
        
        for($i=1; $i<=3; $i++){
            $projet = new Projets();
            $projet->setNom($faker->name());
            $projet->setTitre($faker->title());
            $projet->setImg('panier.jpeg');
            $manager->persist($projet);   
        }
        for($i=1; $i<=10; $i++){
            $competence = new Competences();
            $competence->setNom($faker->name());
            $competence->setTitre($faker->title());
            $manager->persist($competence);   
        }
        for($i=1; $i<=1; $i++){
            $apropo = new Apropos();
            $apropo->setTitre($faker->title());
            $apropo->setDescription($faker->description());
            $manager->persist($apropo);
        }
        

        $manager->flush();
    }
}