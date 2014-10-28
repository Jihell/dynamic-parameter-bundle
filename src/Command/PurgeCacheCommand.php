<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PurgeCacheCommand
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class PurgeCacheCommand extends ContainerAwareCommand
{
    const cacheSubDir = 'Jihel\\Cache';

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('jihel:parameter:purge-cache')
            ->setDescription('Remove all DynamicParameter cache')
            ->setHelp(<<<EOT
The <info>jihel:parameter:purge-cache</info> remove all DynamicParameter cache.

<comment>php app/console jihel:parameter:purge-cache</comment>

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cacheDir = $this->getContainer()->getParameter('kernel.cache_dir')
            .DIRECTORY_SEPARATOR
            .str_replace('\\', DIRECTORY_SEPARATOR, static::cacheSubDir)
        ;
        $output->writeln(sprintf('Cleaning %s ...', realpath($cacheDir)));
        $this->rmdirRecursive($cacheDir);
        $output->writeln('<info>Done</info>');
    }

    /**
     * Remove a directory recursively
     *
     * @param string $dir
     */
    protected function rmdirRecursive($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        $this->rmdirRecursive($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}
