<?php

namespace ActionEaseKit\Base\Service;

use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstracRoletActionService extends AbstractActionService implements IRoleIAction
{
    protected ?UserInterface $user;

    abstract function initUser(string $actionMethodName);

    public function checkRoleAccessToAction(string $actionMethodName) : bool
    {
        $this->initUser($actionMethodName);

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