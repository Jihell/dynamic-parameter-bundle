<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Loader\Bean;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

use Jihel\Plugin\DynamicParameterBundle\Listener\DoctrineListener;
use Jihel\Plugin\DynamicParameterBundle\Manager\CacheManager;

/**
 * Class ParameterLoaderBean
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
abstract class ParameterLoaderBean
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @var string
     */
    protected $allowedNamespaces;

    /**
     * @var string
     */
    protected $deniedNamespaces;

    /**
     * @var bool|string
     */
    protected $cachingStrategy;

    /**
     * @var string
     */
    protected $kernelEnvironment;

    /**
     * @var array
     */
    protected $dynamicParameters = array();

    /**
     * @param EntityManager $entityManager
     * @param CacheManager $cacheManager
     * @param string $allowedNamespaces
     * @param string $deniedNamespaces
     * @param string|bool $cachingStrategy Can be ['env'|true|false]
     * @param string $kernelEnvironment
     */
    public function __construct(
        EntityManager $entityManager,
        CacheManager $cacheManager,
        $allowedNamespaces,
        $deniedNamespaces,
        $cachingStrategy,
        $kernelEnvironment
    ) {
        $this->entityManager     = $entityManager;
        $this->cacheManager      = $cacheManager;
        $this->allowedNamespaces = $allowedNamespaces;
        $this->deniedNamespaces  = $deniedNamespaces;
        $this->cachingStrategy   = $cachingStrategy;
        $this->kernelEnvironment = $kernelEnvironment;
    }


    /**
     * Check if a given table name exist in the database
     *
     * @param string $table
     * @return bool
     */
    protected function checkTableExist($table)
    {
        $schemaManager = $this->entityManager->getConnection()->getSchemaManager();
        return $schemaManager->tablesExist(array(
            $this->entityManager->getClassMetadata($table)->getTableName(),
        ));
    }

    /**
     * @return bool
     */
    protected function isCacheEnabled()
    {
        return 'env' === $this->cachingStrategy
                && 'prod' === $this->kernelEnvironment
            || $this->cachingStrategy
        ;
    }

    /**
     * @return array
     */
    public function getDynamicParameters()
    {
        return $this->dynamicParameters;
    }

    /**
     * @param array $dynamicParameters
     * @return $this
     */
    public function setDynamicParameters(array $dynamicParameters = array())
    {
        $this->dynamicParameters = $dynamicParameters;
        return $this;
    }

    /**
     * Close doctrine connection on destruction
     */
    public function __destruct()
    {
        $this->entityManager->close();
    }
}
