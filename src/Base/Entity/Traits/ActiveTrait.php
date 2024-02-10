<?php

namespace ActionEaseKit\Base\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ActiveTrait
{
    /**
     * @ORM\Column(name="active", type="boolean", options={"default" : true})
     */
    protected bool $active;

    public function setActive(bool $active) : self
    {
        $this->active = $active;

        return $this;
    }

    public function getActive() : bool
    {
        return $this->active;
    }
}