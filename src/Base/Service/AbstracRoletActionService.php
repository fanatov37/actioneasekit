<?php

namespace StreakSymfony\Base\Service;

use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstracRoletActionService implements IRoleIAction
{
    protected ?UserInterface $user;

    public function checkRoleAccessToAction(string $actionMethodName) : bool
    {
        $accessToAction = false;

        if (!$this->getAccessToActionsByRoles()) {
            $accessToAction = true;

        } elseif (!array_key_exists($actionMethodName, $this->getAccessToActionsByRoles())) {
            $accessToAction = true;
        } else if ($this->user) {
            foreach ($this->user->getRoles() as $role) {
                $availableRoles = $this->getAccessToActionsByRoles()[$actionMethodName];
                if (in_array($role, $availableRoles)) {
                    $accessToAction = true;
                    break;
                }
            }
        }

        return $accessToAction;
    }
}