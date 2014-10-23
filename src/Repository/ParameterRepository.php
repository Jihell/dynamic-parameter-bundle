<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Jihel\Plugin\DynamicParameterBundle\Entity\Parameter;

/**
 * Class ParameterRepository
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class ParameterRepository extends EntityRepository
{
    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array|Parameter[]
     */
    public function findOnlyEditable(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->findBy(array_merge(array('isEditable' => true), $criteria), $orderBy, $limit, $offset);
    }

    /**
     * @param string $allowedNamespace
     * @param string $deniedNamespace
     * @param bool $isEditable default true
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array|Parameter[]
     */
    public function findByNamespace($allowedNamespace = '', $deniedNamespace = '', $isEditable = true, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $this->createQueryBuilder('p');

        if (!empty($allowedNamespace)) {
            $query->andWhere($qb->expr()->in('p.namespace', explode(',', $allowedNamespace)));
        }
        if (!empty($deniedNamespace)) {
            $query->andWhere($qb->expr()->notIn('p.namespace', explode(',', $deniedNamespace)));
        }

        $query->orWhere('p.namespace IS NULL');
        if ($isEditable) {
            $query
                ->andWhere('p.isEditable = :isEditable')
                ->setParameter('isEditable', $isEditable)
            ;
        }

        if (count($orderBy)) {
            foreach ($orderBy as $key => $sort) {
                $query->addOrderBy(sprintf('p.%s', $key), $sort);
            }

        }

        $query
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * @param string $prefix
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     * @return array|Parameter[]
     */
    public function findByPrefix($prefix, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.name LIKE :prefix')
            ->setParameter('prefix', $prefix.'%')
            ->andWhere('p.isEditable = 1')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        ;

        if (!empty($orderBy)) {
            foreach($orderBy as $key => $sort) {
                $qb->addOrderBy('p.'.$key, $sort);
            }
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $prefix
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     * @return array|Parameter[]
     */
    public function findByExcludePrefix(array $prefix, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        ;

        foreach ($prefix as $row => $pre) {
            $qb
                ->andWhere('p.name NOT LIKE :pre'.$row)
                ->setParameter('pre'.$row, $pre.'%');
            ;
        }


        if (!empty($orderBy)) {
            foreach($orderBy as $key => $sort) {
                $qb->addOrderBy('p.'.$key, $sort);
            }
        }

        return $qb->getQuery()->getResult();
    }
}
