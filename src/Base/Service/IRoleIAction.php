<?php

namespace ActionEaseKit\Base\Service;

interface IRoleIAction
{
    public function getAccessToActionsByRoles() : array;
    public function checkRoleAccessToAction(string $actionMethodName) : bool;
}
