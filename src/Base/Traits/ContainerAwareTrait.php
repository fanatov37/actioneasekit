<?php

namespace ActionEaseKit\Base\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @codeCoverageIgnore
 *
 * This is for Migration
 */
trait ContainerAwareTrait
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;

    public function setContainer(ContainerInterface $container = null) : void
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }
}
