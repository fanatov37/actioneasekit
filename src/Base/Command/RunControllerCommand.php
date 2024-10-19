<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Command;

use ActionEaseKit\Kernel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\Request;

#[AsCommand(name: 'base:run:controller')]
final class RunControllerCommand extends Command
{
    public function __construct(private Kernel $kernel)
    {
        parent::__construct();
    }

    /** @codeCoverageIgnore */
    protected function configure() : void
    {
        $this->addOption('controller', mode: InputOption::VALUE_REQUIRED)
            ->addOption('request', mode: InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $controller = $input->getOption('controller');
        $requestData = json_decode($input->getOption('request'), true) ?? [];

        $requestController = $this->kernel->getContainer()->get($controller);

        $request = new Request(request: $requestData);

        $result= $requestController->indexAction($request);

        $output->writeln("Status=>{$result->getStatusCode()}");
        $output->writeln("Result=>{$result->getContent()}");

        return self::SUCCESS;
    }
}
