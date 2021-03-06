<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

use Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Cache\ParameterCache;
use Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Loader\EnvironmentLoader;
use Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Loader\FailSafeParameterLoader;
use Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Loader\ParameterLoader;
use Jihel\Plugin\DynamicParameterBundle\Listener\DoctrineListener;

/**
 * Class ParameterLoaderFactory
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class ParameterLoaderFactory
{
    /**
     * Constructor
     *
     * @param array $parameters = array()
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
        $this->environmentLoader = new EnvironmentLoader();
    }

    /**
     * @return ParameterLoader
     */
    public function get()
    {
        try {
            $loader = new ParameterLoader(
                $this->getEntityManager(),
                $this->getParameterCache(),
                $this->environmentLoader,
                $this->getParameter('jihel.plugin.dynamic_parameter.dynamic_parameter_cache'),
                $this->getParameter('kernel.environment')
            );
        } catch (\Exception $e) {
            $loader = new FailSafeParameterLoader();
        }

        return $loader;
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function getParameter($name)
    {
        if (!isset($this->parameters[$name])) {
            return;
        }
        return $this->parameters[$name];
    }

    /**
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    protected function getEntityManager()
    {
        $params = array(
            'driver'    => $this->getParameter('database_driver'),
            'port'      => $this->getParameter('database_port'),
            'host'      => $this->getParameter('database_host'),
            'dbname'    => $this->getParameter('database_name'),
            'user'      => $this->getParameter('database_user'),
            'password'  => $this->getParameter('database_password'),
        );

        $entityPath = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Entity');
        $entityManager = EntityManager::create($params, Setup::createAnnotationMetadataConfiguration(
            array($entityPath), true, null, null, false
        ));

        // Register table prefix name
        if (null === $this->getParameter('jihel.plugin.dynamic_parameter.table_prefix')) {
            $this->parameters['jihel.plugin.dynamic_parameter.table_prefix'] = 'jihel_';
        }
        $doctrineListener = new DoctrineListener($this->getParameter('jihel.plugin.dynamic_parameter.table_prefix'));
        $doctrineListener->registerEventManager($entityManager->getEventManager());

        // Test connection
        $entityManager->getConnection()->connect();

        return $entityManager;
    }

    /**
     * @return ParameterCache
     */
    protected function getParameterCache()
    {
        return new ParameterCache($this->getParameter('kernel.cache_dir'));
    }
}
