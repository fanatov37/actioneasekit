<?php

namespace StreakSymfony\Base\Entity\CoreTrait;

use Doctrine\ORM\Mapping as ORM;

trait UpdatedTrait
{
    /**
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    protected \DateTime $updated;

    public function __construct()
    {
        $this->updated = new \DateTime();
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $createdAt): self
    {
        $this->updated = $createdAt;

        return $this;
    }
}
