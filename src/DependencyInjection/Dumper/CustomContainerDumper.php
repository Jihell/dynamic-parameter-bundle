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
class CustomContainerDumper
{
    const templateLocation = '/../../Resources/views/Cache/ContainerDumpDecoratorCache.php.twig';

    /**
     * Dumps the service container as a PHP class.
     *
     * Available options:
     *
     *  * prefix:     The class prefix
     *  * base_class: The base class name
     *
     * @param array $options An array of options
     * @return string A PHP class representing of the service container
     */
    public function dump(array $options = array())
    {
        $content = file_get_contents(__DIR__.static::templateLocation);
        $keyMap = array(
            '{{ prefix }}'      => $options['prefix'],
            '{{ parentClass }}' => $options['base_class'],
        );

        return str_replace(array_keys($keyMap), array_values($keyMap), $content);
    }
}
