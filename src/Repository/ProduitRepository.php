<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findByCategorie($categorieId)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.categorie = :categorieId')
            ->setParameter('categorieId', $categorieId)
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}