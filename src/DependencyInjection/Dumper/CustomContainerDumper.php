<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Dumper;

use Jihel\Plugin\DynamicParameterBundle\HttpKernel\Kernel;

/**
 * Class PhpDumper
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class CustomContainerDumper
{
    /**
     * Dumps the service container as a PHP class.
     *
     * @param string $parentClass
     * @return string A PHP class representing of the service container
     *
     * @api
     */
    public function dump($parentClass)
    {
        $code =
            $this->startClass($parentClass).
            $this->getOverload().
            $this->endClass()
        ;

        return $code;
    }

    /**
     * @param string $parentClass
     * @return string
     */
    protected function startClass($parentClass)
    {
        $prefix = Kernel::customContainerOverloadClassPrefix;
        return <<<EOF
<?php
use Jihel\Plugin\DynamicParameterBundle\Loader\ParameterLoader;

/**
 * $prefix$parentClass
 *
 * This class has been auto-generated
 * and is a decorator for the container builder
 */
class $prefix$parentClass extends $parentClass
{

EOF;
    }

    protected function getOverload()
    {
        return <<<EOF
    protected function getDefaultParameters()
    {
        \$parameterLoader = new ParameterLoader(parent::getDefaultParameters());
        return \$parameterLoader->loadParameters();
    }

EOF;
    }

    /**
     * Ends the class definition.
     *
     * @return string
     */
    protected function endClass()
    {
        return <<<EOF
}

EOF;
    }
}
