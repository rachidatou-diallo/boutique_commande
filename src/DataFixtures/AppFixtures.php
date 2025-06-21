<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\CommandeProduit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@boutique.com');
        $admin->setNom('Admin');
        $admin->setPrenom('Super');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Créer un utilisateur normal
        $user = new User();
        $user->setEmail('user@boutique.com');
        $user->setNom('Utilisateur');
        $user->setPrenom('Test');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        // Créer des catégories
        $categories = [
            ['nom' => 'Électronique', 'description' => 'Appareils électroniques et gadgets'],
            ['nom' => 'Vêtements', 'description' => 'Vêtements pour homme et femme'],
            ['nom' => 'Maison & Jardin', 'description' => 'Articles pour la maison et le jardin'],
            ['nom' => 'Sports & Loisirs', 'description' => 'Équipements sportifs et de loisirs'],
        ];

        $categorieEntities = [];
        foreach ($categories as $catData) {
            $categorie = new Categorie();
            $categorie->setNom($catData['nom']);
            $categorie->setDescription($catData['description']);
            $manager->persist($categorie);
            $categorieEntities[] = $categorie;
        }

        // Créer des produits
        $produits = [
            ['nom' => 'Smartphone Samsung Galaxy', 'description' => 'Smartphone dernière génération avec écran AMOLED', 'prix' => '699.99', 'stock' => 25, 'categorie' => 0],
            ['nom' => 'Laptop Dell XPS 13', 'description' => 'Ordinateur portable ultra-fin et performant', 'prix' => '1299.99', 'stock' => 15, 'categorie' => 0],
            ['nom' => 'T-shirt Nike', 'description' => 'T-shirt de sport confortable en coton', 'prix' => '29.99', 'stock' => 50, 'categorie' => 1],
            ['nom' => 'Jean Levi\'s 501', 'description' => 'Jean classique coupe droite', 'prix' => '89.99', 'stock' => 30, 'categorie' => 1],
            ['nom' => 'Aspirateur Dyson V11', 'description' => 'Aspirateur sans fil haute performance', 'prix' => '449.99', 'stock' => 12, 'categorie' => 2],
            ['nom' => 'Raquette de tennis Wilson', 'description' => 'Raquette professionnelle pour joueurs confirmés', 'prix' => '159.99', 'stock' => 8, 'categorie' => 3],
        ];

        $produitEntities = [];
        foreach ($produits as $prodData) {
            $produit = new Produit();
            $produit->setNom($prodData['nom']);
            $produit->setDescription($prodData['description']);
            $produit->setPrix($prodData['prix']);
            $produit->setStock($prodData['stock']);
            $produit->setCategorie($categorieEntities[$prodData['categorie']]);
            $manager->persist($produit);
            $produitEntities[] = $produit;
        }

        // Créer quelques commandes
        for ($i = 0; $i < 5; $i++) {
            $commande = new Commande();
            $commande->setUser($i % 2 === 0 ? $admin : $user);
            $commande->setDateCommande(new \DateTime('-' . rand(1, 30) . ' days'));
            
            $statuts = ['en_attente', 'confirmee', 'expediee', 'livree'];
            $commande->setStatut($statuts[array_rand($statuts)]);

            // Ajouter des produits à la commande
            $nbProduits = rand(1, 3);
            for ($j = 0; $j < $nbProduits; $j++) {
                $produit = $produitEntities[array_rand($produitEntities)];
                $commandeProduit = new CommandeProduit();
                $commandeProduit->setCommande($commande);
                $commandeProduit->setProduit($produit);
                $commandeProduit->setQuantite(rand(1, 3));
                $commandeProduit->setPrixUnitaire($produit->getPrix());
                
                $commande->addCommandeProduit($commandeProduit);
                $manager->persist($commandeProduit);
            }
            
            $commande->calculateTotal();
            $manager->persist($commande);
        }

        $manager->flush();
    }
}