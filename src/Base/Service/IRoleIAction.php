<?php

namespace StreakSymfony\Base\Service;

interface IRoleIAction
{
    public function getAccessToActionsByRoles() : array;
    public function checkRoleAccessToAction(string $actionMethodName) : bool;
}
