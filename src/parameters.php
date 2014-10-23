<?php
/**
 * @package dynamic-parameter-bundle
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use Jihel\Plugin\DynamicParameterBundle\Manager\CacheManager;
use Jihel\Plugin\DynamicParameterBundle\Listener\DoctrineListener;

if (!$container->hasParameter('jihel.plugin.dynamic_parameter.allowed_namespaces')) {
    $container->setParameter('jihel.plugin.dynamic_parameter.allowed_namespaces', null);
}
if (!$container->hasParameter('jihel.plugin.dynamic_parameter.denied_namespaces')) {
    $container->setParameter('jihel.plugin.dynamic_parameter.denied_namespaces', null);
}
/** @var \Symfony\Component\DependencyInjection\Container */
$env                = $container->getParameter('kernel.environment');
$allowedNamespaces  = $container->getParameter('jihel.plugin.dynamic_parameter.allowed_namespaces');
$deniedNamespaces   = $container->getParameter('jihel.plugin.dynamic_parameter.denied_namespaces');
$cacheManager = new CacheManager($container, $allowedNamespaces, $deniedNamespaces);
if (!$cacheManager->loadFromCache()) {
    echo 'Create Cache<br/>';

    // Start doctrine
    $entityManager = EntityManager::create(array(
        'driver'    => $container->getParameter('database_driver'),
        'port'      => $container->getParameter('database_port'),
        'host'      => $container->getParameter('database_host'),
        'dbname'    => $container->getParameter('database_name'),
        'user'      => $container->getParameter('database_user'),
        'password'  => $container->getParameter('database_password'),
    ), Setup::createAnnotationMetadataConfiguration(
        array(__DIR__.DIRECTORY_SEPARATOR.'Entity'), true, null, null, false
    ));

    // Register table prefix name
    $doctrineListener = new DoctrineListener($container->getParameter('jihel.plugin.dynamic_parameter.table_prefix'));
    $doctrineListener->registerEventManager($entityManager->getEventManager());

    $schemaManager = $entityManager->getConnection()->getSchemaManager();
    // Don't crash before creating the table
    if ($schemaManager->tablesExist(array(
        $entityManager->getClassMetadata(DoctrineListener::entity)->getTableName(),
    ))) {
        /** @var Jihel\Plugin\DynamicParameterBundle\Repository\ParameterRepository $parameterRepository */
        $parameterRepository = $entityManager->getRepository('Jihel\Plugin\DynamicParameterBundle\Entity\Parameter');
        $parameters          = $parameterRepository->findByNamespace($allowedNamespaces, $deniedNamespaces, false);
        $cached = array();
        if (!empty($parameters)) {
            foreach ($parameters as $parameter) {
                $container->setParameter($parameter->getName(), $parameter->getValue());
                $cached[$parameter->getName()] = $parameter->getValue();
            }
        }

        var_dump($container->getParameter('jihel.plugin.dynamic_parameter.dynamic_parameter_cache'));
        echo '<br/>';
        if (true === $container->getParameter('jihel.plugin.dynamic_parameter.dynamic_parameter_cache')
            || 'env' === $container->getParameter('jihel.plugin.dynamic_parameter.dynamic_parameter_cache')
            && 'prod' === $env
        ) {
            echo 'Save Cache<br/>';
            $cacheManager->createCache($cached);
        }
    }

    // Cleanup and close
    $entityManager->close();
    unset($entityManager);
    unset($doctrineListener);
    unset($schemaManager);
    unset($cached);
    unset($cacheManager);
} else {
    echo 'Load cache<br/>';
}
