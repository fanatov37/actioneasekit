<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
    #[ORM\Id]
    #[ORM\Column(type: "bigint")]
    #[ORM\GeneratedValue(strategy: "AUTO")]

    protected ?int $id = null;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId() : ?int
    {
        return $this->id;
    }
}
