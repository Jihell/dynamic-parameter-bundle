<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\Model;

/**
 * Class DynamicParameterCacheInterface
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
interface DynamicParameterCacheInterface
{
    /**
     * @return array
     */
    function getParameters();
}
