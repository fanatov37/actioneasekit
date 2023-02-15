<?php

namespace App\Base\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/** @codeCoverageIgnore  */
class RunCustomCommandService
{
    public function __construct(private KernelInterface $kernel)
    {}

    public function runCmd(array $parameters, bool $catchExceptions = true): ?string
    {
        $application = new Application($this->kernel);
        $application->setCatchExceptions($catchExceptions);
        $application->setAutoExit(false);

        $input = new ArrayInput($parameters);

        $output = new BufferedOutput();
        $application->run($input, $output);

        return $output->fetch();
    }
}
