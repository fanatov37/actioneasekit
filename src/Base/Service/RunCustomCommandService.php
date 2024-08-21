<?php

namespace ActionEaseKit\Base\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @codeCoverageIgnore
 * Service for running custom console commands.
 *
 * This service allows running custom console commands programmatically.
 */
class RunCustomCommandService
{
    public function __construct(private KernelInterface $kernel)
    {}

    public function runCmd(array $parameters, bool $catchExceptions = true): ?string
    {
        $application = new Application($this->kernel);
        $application->setCatchExceptions($catchExceptions);
        $application->setAutoExit(false);

        $parameters = $this->prepareParameters($parameters);
        $input = new ArrayInput($parameters);
        $output = new BufferedOutput();

        $application->run($input, $output);

        return $output->fetch();
    }

    protected function prepareParameters(array $parameters) : array
    {
        foreach ($parameters as $key=>$value) {
            if ($key === 'command') continue;

            // modify only params without --
            if (str_starts_with($key, '--')) continue;

            $newKey = "--$key";
            $parameters[$newKey] = $value;
            unset($parameters[$key]);
        }

        return $parameters;
    }
}
