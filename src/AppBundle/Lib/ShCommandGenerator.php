<?php

namespace AppBundle\Lib;

use Symfony\Component\Filesystem\Filesystem;

class ShCommandGenerator extends Generator
{
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function generateScript($skeletonDirs, $root)
    {
        $rootDir = $root.'/../';

        $this->setSkeletonDirs($skeletonDirs);
        $this->renderFile('command/commander.sh.twig', $rootDir.'commander.sh', array(
            'root' => $rootDir,
        ));
    }
}
