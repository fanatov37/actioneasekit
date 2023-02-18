<?php

namespace StreakSymfony\Base\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CreatedTrait
{
    /**
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    protected \DateTime $created;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }
}
