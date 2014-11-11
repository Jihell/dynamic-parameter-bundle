<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\Model;

/**
 * Interface ParameterLoaderInterface
 *
 * @author Joseph Lemoine <j.lemoine@gmail.com>
 * @link http://www.joseph-lemoine.fr
  */
interface ParameterLoaderInterface
{
    /**
     * @return array
     */
    function load();
}
