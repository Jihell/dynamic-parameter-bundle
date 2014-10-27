<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Dumper;

/**
 * Class PhpDumper
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class DynamicParameterDumper
{
    const templateLocation = '/../../Resources/views/Cache/DynamicParameterCache.php.twig';

    /**
     * Dumps the dynamic parameters cache as a PHP class.
     *
     * Available options:
     *
     *  * parameters:     The parameters
     *  * namespaceHash:  The namespace names hashed
     *
     * @param array $options An array of options
     * @return string A PHP class representing of the dynamic parameters for the given namespace
     */
    public function dump(array $options = array())
    {
        $content = file_get_contents(__DIR__.static::templateLocation);
        $keyMap = array(
            '{{ parameters }}'      => var_export($options['parameters'], true),
            '{{ namespaceHash }}'   => '\\'.$options['namespaceHash'],
        );

        return str_replace(array_keys($keyMap), array_values($keyMap), $content);
    }
}
