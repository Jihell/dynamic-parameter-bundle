<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Loader;

use Jihel\Plugin\DynamicParameterBundle\Listener\DoctrineListener;

/**
 * Class ParameterLoader
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class ParameterLoader extends Bean\ParameterLoaderBean
{
    /**
     * Load the dynamics parameters from cache.
     * If the cache don't exist, load from database.
     *
     * Generate a new cache if the caching strategy is enabled
     *
     * @param bool $force Do load from database
     * @return $this
     */
    public function load($force = false)
    {
        if (empty($this->dynamicParameters) || $force) {
            if ($this->parameterCache->isCached() && !$force) {
                $this->setDynamicParameters($this->parameterCache->loadFromCache());
            } elseif ($this->checkTableExist(DoctrineListener::entity)) {
                /** @var \Jihel\Plugin\DynamicParameterBundle\Repository\ParameterRepository $parameterRepository */
                $parameterRepository = $this->entityManager->getRepository('Jihel\Plugin\DynamicParameterBundle\Entity\Parameter');
                $parameters          = $parameterRepository->findByNamespace(
                    $this->allowedNamespaces,
                    $this->deniedNamespaces,
                    false
                );

                $out = array();
                if (count($parameters)) {
                    foreach ($parameters as $parameter) {
                        $out[$parameter->getName()] = $parameter->getValue();
                    }
                }

                $this->setDynamicParameters($out);

                // rebuild cache
                !$this->isCacheEnabled() or $this->parameterCache->createCache($out);
            }
        }

        return $this->getDynamicParameters();
    }
}
