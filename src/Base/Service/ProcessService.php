<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Psr\Log\LoggerInterface;

final class ProcessService
{
    private $processLimit = 50;
    private int $delay = 5;
    private array $pending = [];
    private array $active = [];
    private array $finished = [];
    private bool $debug = false;
    private ?OutputInterface $output;
    private ?OutputInterface $logger;

    public function __construct(OutputInterface $output = null, LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->output = $output;
    }

    public function setProcessLimit(int $proccessLimit = 50) : self
    {
        $this->processLimit = $proccessLimit;
        return $this;
    }

    public function setDebug(bool $debug = false) : self
    {
        $this->debug = $debug;
        return $this;
    }

    public function addProccess(Process $process) : self
    {
        $this->pending[] = $process;
        return $this;
    }

    private function logException(\Exception $exception) : void
    {
        if ($this->logger) {
            $message = sprintf(
                '%s: %s (uncaught exception) at %s line %s while running console command',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );

            $this->logger->error($message, ['exception' => $exception]);
        }

        if($this->output) {
            $this->output->writeln(PHP_EOL.'<error>Exception detected: '.$exception->getMessage().'</>');
        }
    }

    public function execute() : bool
    {
        gc_enable();

        if ($this->output){
            $progress = new ProgressBar($this->output, count($this->pending));
        }

        if (count($this->pending) > 0) {

            if (isset($progress)) {
                $progress->start();
            }

            do {
                // We are checking the processes being executed for completion. We move the completed ones to $finished.
                if( count($this->active) > 0) {
                    foreach ($this->active as $key => $activeProcess) {

                        try {
                            // check if the timeout is reached
                            $activeProcess->checkTimeout();
                        } catch (ProcessTimedOutException $e) {
                            $this->logException($e);
                        }

                        if ($activeProcess->isTerminated()) {
                            $this->finished[] = $activeProcess;
                            unset($this->active[$key]);
                            if (isset($progress)) {
                                $progress->advance();
                            }
                        }
                    }
                }

                // add proceess for run
                if ((count($this->active) < $this->processLimit) and (count($this->pending) > 0)) {
                    while ((count($this->active) < $this->processLimit)
                        and(count($this->pending) > 0)) {
                        $this->active[] = array_pop($this->pending);
                        // initializa process
                        end($this->active)->start();
                    }
                }

                sleep($this->delay);
                gc_collect_cycles();

            } while ((count($this->pending) > 0) or (count($this->active) != 0));

            if(isset($progress)){
                $progress->finish();
            }

            if ($this->debug) {
                foreach ($this->finished as $process) {

                    // is wrong process output error
                    if (!$process->isSuccessful()) {
                        try {
                            throw new ProcessFailedException($process);
                        } catch (ProcessFailedException $e) {
                            $this->logException($e);
                        }
                    }
                }
            }

            return true;
        }
        return false;
    }
}
