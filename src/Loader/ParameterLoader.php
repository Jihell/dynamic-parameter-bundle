<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\Loader;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

use Jihel\Plugin\DynamicParameterBundle\Listener\DoctrineListener;

/**
 * Class ParameterLoader
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class ParameterLoader
{
    protected $parameters;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var DoctrineListener
     */
    protected $doctrineListener;

    /**
     * @var array
     */
    protected $dynamicParameters = array();

    /**
     * Constructor
     *
     * @param array $parameters = array()
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    /**
     * Load parameters from cache if present, else from database
     *
     * @return $this
     */
    public function loadParameters()
    {
        return $this
            ->initNamespaces()
            ->initDoctrine()
            ->registerParameters($this->getDynamicParameters())
            ->closeAll()
            ->getParameters()
        ;
    }

    /**
     * @return array
     */
    protected function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function getParameter($name)
    {
        return $this->parameters[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * Initialise
     *
     * @return $this
     */
    protected function initNamespaces()
    {
        if (!$this->hasParameter('jihel.plugin.dynamic_parameter.allowed_namespaces')) {
            $this->setParameter('jihel.plugin.dynamic_parameter.allowed_namespaces', null);
        }
        if (!$this->hasParameter('jihel.plugin.dynamic_parameter.denied_namespaces')) {
            $this->setParameter('jihel.plugin.dynamic_parameter.denied_namespaces', null);
        }

        return $this;
    }

    /**
     * Initializes entityManager
     *
     * @return $this
     */
    protected function initDoctrine()
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
        $this->entityManager = EntityManager::create($params, Setup::createAnnotationMetadataConfiguration(
            array($entityPath), true, null, null, false
        ));

        // Register table prefix name
        $this->doctrineListener = new DoctrineListener($this->getParameter('jihel.plugin.dynamic_parameter.table_prefix'));
        $this->doctrineListener->registerEventManager($this->entityManager->getEventManager());

        return $this;
    }

    /**
     * Closes the database connection and unset temp vars
     *
     * @return $this
     */
    protected function closeAll()
    {
        if ($this->entityManager->getConnection()->isConnected()) {
            $this->entityManager->close();
        }

        unset($this->entityManager);
        unset($this->doctrineListener);
        unset($this->dynamicParameters);

        return $this;
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
     * @return array
     */
    protected function getDynamicParameters()
    {
        if (empty($this->dynamicParameters)) {
            if ($this->checkTableExist(DoctrineListener::entity)) {
                $allowedNamespaces   = $this->getParameter('jihel.plugin.dynamic_parameter.allowed_namespaces');
                $deniedNamespaces    = $this->getParameter('jihel.plugin.dynamic_parameter.denied_namespaces');

                /** @var \Jihel\Plugin\DynamicParameterBundle\Repository\ParameterRepository $parameterRepository */
                $parameterRepository = $this->entityManager->getRepository('Jihel\Plugin\DynamicParameterBundle\Entity\Parameter');
                $parameters          = $parameterRepository->findByNamespace($allowedNamespaces, $deniedNamespaces, false);

                if (!empty($parameters)) {
                    foreach ($parameters as $parameter) {
                        $this->dynamicParameters[$parameter->getName()] = $parameter->getValue();
                    }
                }
            }
        }

        return $this->dynamicParameters;
    }

    /**
     * Adds the parameters from the database to the container's parameterBag
     * Override previous ones
     *
     * @return $this
     */
    protected function registerParameters(array $dynamicParameters)
    {
        if (count($dynamicParameters)) {
            foreach($dynamicParameters as $key => $value) {
                $this->setParameter($key, $value);
            }
        }

        return $this;
    }
}
