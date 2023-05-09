<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Utilisateur;
use App\Entity\Prestataire;
use App\Entity\Proposer;
use App\Entity\CategorieDeServices;
use App\Entity\Images;
use App\Entity\Commune;
use App\Entity\Localite;
use App\Entity\CodePostal;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Récupération de toutes les communes, villes et codes postaux dans la base de données
        $communes = $manager->getRepository(Commune::class)->findAll();
        $localites = $manager->getRepository(Localite::class)->findAll();
        $codesPostaux = $manager->getRepository(CodePostal::class)->findAll();

        // Instance de Faker
        $faker = Factory::create('fr_BE');

        // Boucle pour créer 20 utilisateurs
        for ($i = 0; $i < 30; $i++) {
            // Récupération aléatoire d'une commune, d'une ville et d'un code postal parmi ceux existants
            $commune = $faker->randomElement($communes);
            $localite = $faker->randomElement($localites);
            $codePostal = $faker->randomElement($codesPostaux);
            $isVerified = $faker->boolean(100);

            // Création d'un nouvel utilisateur
            $utilisateur = new Utilisateur();

            // Assignation des propriétés avec des données aléatoires générées par Faker
            $utilisateur->setEmail($faker->email);
            $utilisateur->setPassword($faker->password);
            $utilisateur->setAdresseRue($faker->streetName);
            $utilisateur->setAdresseNum($faker->buildingNumber);
            $utilisateur->setCommune($commune);
            $utilisateur->setLocalite($localite);
            $utilisateur->setCodePostal($codePostal);
            $utilisateur->setIsVerified($isVerified);
            $utilisateur->setInscription(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));

            // Enregistrement de l'utilisateur dans la base de données
            $manager->persist($utilisateur);
        }

        for ($i = 0; $i < 21; $i++) {
            $prestataire = new Prestataire();
            $site = 'www.google.com';
            $filename = md5(uniqid($faker->randomNumber(), true)) . '.webp';


            $prestataire->setNom($faker->company);
            $prestataire->setSiteInternet($site);
            $prestataire->setNumTel($faker->phoneNumber);
            $prestataire->setNumTva($faker->vat);

            $image = new Images();

            $image->setPrestataire($prestataire);
            $image->setNom($filename);

            $manager->persist($prestataire);
        }

        $prestataires = $manager->getRepository(Prestataire::class)->findAll();
        $categories = $manager->getRepository(CategorieDeServices::class)->findAll();

        foreach ($prestataires as $prestataire) {

            // Nombre aléatoire de catégories pour chaque prestataire
            $nbCategories = $faker->numberBetween(1, count($categories));

            // Récupération aléatoire de plusieurs catégories
            $propositions = $faker->randomElements($categories, $nbCategories);

            // Création d'une proposition pour chaque catégorie
            foreach ($propositions as $proposition) {
                $proposer = new Proposer();
                $proposer->setPrestataire($prestataire);
                $proposer->setCategorieDeServices($proposition);
                $manager->persist($proposer);
            }
        }

        $manager->flush();
    }
}
