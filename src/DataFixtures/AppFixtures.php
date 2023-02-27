<?php

namespace App\DataFixtures;

use App\Entity\TypeCompte;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $type1 = (new TypeCompte())
            ->setNom("Epargne")
            ->setDescription("Compte epargne")
            ->setFraisOuverture(0)
            ->setSoldeMin(5000)
            ->setPlafondRetrait(1000000)
            ->setTauxInteret(0.06)
            ->setTauxInteretDecouvert(0);

        $type2 = (new TypeCompte())
            ->setNom("Courant")
            ->setDescription("Compte courant")
            ->setFraisOuverture(5000)
            ->setSoldeMin(10000)
            ->setPlafondRetrait(1000000)
            ->setTauxInteret(0.06)
            ->setTauxInteretDecouvert(0,1);

        $type1->setCode("EP");
        $type2->setCode("CR");

        $manager->persist($type2);
        $manager->persist($type1);
        $manager->flush();
    }
}
