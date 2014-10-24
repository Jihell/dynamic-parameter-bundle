<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\Listener;

use Doctrine\Common\EventManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Class DoctrineListener
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class DoctrineListener implements \Doctrine\Common\EventSubscriber
{
    const entity = 'Jihel\Plugin\DynamicParameterBundle\Entity\Parameter';

    /**
     * @var string
     */
    protected $prefix;

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
        );
    }

    /**
     * @param string $prefix
     */
    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * For standalone usage
     *
     * @param EventManager $eventManager
     */
    public function registerEventManager(EventManager $eventManager)
    {
        $eventManager->addEventListener($this->getSubscribedEvents(), $this);
    }

    /**
     * @param LoadClassMetadataEventArgs $args
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();
        if (self::entity !== $classMetadata->getName()) {
            return;
        }

        $classMetadata->setPrimaryTable(array(
            'name' => $this->prefix.$classMetadata->getTableName(),
        ));
    }
}
