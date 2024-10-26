<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Entity;

use Doctrine\ORM\Mapping as ORM;

trait DataPropertyTrait
{
    #[ORM\Column(name: "data", type: "json", nullable: true, options: ["jsonb" => true])]
    protected ?array $data = null;
}
