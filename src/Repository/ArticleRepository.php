<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @param string $maxArticleResults
     * @param int|null $id
     * @return Article[] Returns an array of Article objects
     */
    public function findArticles(string $maxArticleResults, int $id = null)
    {
        $qb = $this->createQueryBuilder('article');

        if (null !== $id) {
            $qb->where('article.id < :id')
                ->setParameter('id', $id);
        }

        return $qb->orderBy('article.id', 'DESC')
            ->setMaxResults($maxArticleResults)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNumberOfArticles()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT COUNT(a.id) FROM App\Entity\Article a')
            ->getSingleScalarResult();
    }

    public function getSearchResults($value)
    {
        $qb = $this->createQueryBuilder('a')
//            ->select('a.title')
//            ->distinct(true)
            ->join('a.images', 'images')
            ->join('a.categories', 'categories');
        $qb->where($qb->expr()->orX(
            $qb->expr()->like('images.content', ':value'),
            $qb->expr()->like('categories.name', ':value')
        ));
        $qb->setParameter('value', '%'.$value);

        return $qb->getQuery()->getResult();
    }
}
