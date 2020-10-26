<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Product::class);
        $this->manager = $manager;
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findProducts($page, $limit, $filters)
    {
        try {
            $totalCount = $this->createQueryBuilder('p')
                ->select('count(p.id)')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            $totalCount = 0;
        }
        $limit = in_array($limit, [5, 10, 20, 50, 100]) ? $limit : 10;
        $page = (int)$page > 0 ? (int)$page : 1;
        if(($page - 1) * $limit > $totalCount) {
            $page = (int) $totalCount / $limit;
        }
        $offset = ($page - 1) * $limit;
        $qb = $this->createQueryBuilder('p');
        foreach ($filters as $key => $filter) {
            if (!empty($filter)) {
                if($key == 'status'){
                    $qb->andWhere('p.status = :status');
                    $qb->setParameter('status', $filter == 'inactive' ? 0 : 1);
                } elseif ($key == 'attributes') {
                    $qb->leftJoin('p.attributes','a');
                    foreach($filter as $attribute) {
                        $qb->andWhere("a.attributeKey = :k");
                        $qb->andWhere("a.attributeValue = :v");
                        $qb->setParameter('k', $attribute['key']);
                        $qb->setParameter('v', $attribute['value']);
                    }
                }
                else {
                    $qb->andWhere("p.$key LIKE :$key");
                    $qb->setParameter($key, "%$filter%");
                }
            }
        }
        $qb->orderBy('p.id', 'ASC');
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        $result = $query->getResult();
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $filteredCount = count($paginator);
        $data = [];
        foreach ($result as $product) {
            $data[] = $product->toArray();
        }
        return [
            'products' => $data,
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
        ];

    }

    public function saveProduct($name, $sku, $category, $brand)
    {
        $newProduct = new Product();

        $newProduct
            ->setName($name)
            ->setSku($sku)
            ->setCategory($category)
            ->setBrand($brand);

        $this->manager->persist($newProduct);
        $this->manager->flush();
    }

    public function updateProduct(Product $product): Product
    {
        $this->manager->persist($product);
        $this->manager->flush();

        return $product;
    }
}
