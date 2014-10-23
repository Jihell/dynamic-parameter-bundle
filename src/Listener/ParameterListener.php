<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Jihel\Plugin\DynamicParameterBundle\Entity\Parameter;
use Jihel\Plugin\DynamicParameterBundle\Reference\ParameterTypeReference;

/**
 * Class ParameterListener
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class ParameterListener
{
    /**
     * @param Parameter $parameter
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Parameter $parameter, LifecycleEventArgs $event)
    {
        $this->saveDataTransform($parameter);
    }

    /**
     * @param Parameter $parameter
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(Parameter $parameter, LifecycleEventArgs $event)
    {
        $this->saveDataTransform($parameter);
    }

    /**
     * @param Parameter $parameter
     */
    protected function saveDataTransform(Parameter $parameter)
    {
        if (ParameterTypeReference::JSON === $parameter->getType() && is_array($parameter->getValue())) {
            $parameter->setValue(json_encode($parameter->getValue()));
        }
    }

    /**
     * @param Parameter $parameter
     * @param LifecycleEventArgs $event
     */
    public function postLoad(Parameter $parameter, LifecycleEventArgs $event)
    {
        $value = $parameter->getValue();
        switch ($parameter->getType()) {
            case ParameterTypeReference::INT:
                $value = (int) $value;
                break;
            case ParameterTypeReference::FLOAT:
                $value = (float) $value;
                break;
            case ParameterTypeReference::BOOL:
                $value = (bool) $value;
                break;
            case ParameterTypeReference::JSON:
                $value = json_decode($value, true);
                break;
        }

        $parameter->setValue($value);
    }
}
