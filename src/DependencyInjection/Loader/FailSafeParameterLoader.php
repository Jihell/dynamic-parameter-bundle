<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Loader;

use Jihel\Plugin\DynamicParameterBundle\Model\ParameterLoaderInterface;

/**
 * Class FailSafeParameterLoader
 *
 * @author Joseph Lemoine <j.lemoine@gmail.com>
 * @link http://www.joseph-lemoine.fr
  */
class FailSafeParameterLoader implements ParameterLoaderInterface
{
    /**
     * @return array
     */
    public function load()
    {
        return array();
    }
}
