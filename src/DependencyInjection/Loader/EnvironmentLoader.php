<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Loader;

/**
 * Class EnvironmentLoader
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class EnvironmentLoader
{
    /**
     * @var array
     */
    static protected $data = array();

    /**
     * Because you may have no environment var at all
     *
     * @var bool
     */
    static protected $dataLoaded = false;

    public function __construct()
    {
        // Load on construct the first instance
        if (false === static::$dataLoaded) {
            foreach ($_SERVER as $key => $value) {
                if (0 === strpos($key, 'SYMFONY__')) {
                    static::$data[strtolower(str_replace('__', '.', substr($key, 9)))] = $value;
                }
            }
            static::$dataLoaded = true;
        }
    }

    /**
     * Gets an environment parameter.
     *
     * @return mixed
     */
    public function get($name)
    {
        return isset(static::$data[$name]) ? static::$data[$name] : null;
    }

    /**
     * Gets an environment parameters.
     *
     * Only the parameters starting with "SYMFONY__" are considered.
     *
     * @return array An array of parameters
     */
    public function getAll()
    {
        return static::$data;
    }
}
