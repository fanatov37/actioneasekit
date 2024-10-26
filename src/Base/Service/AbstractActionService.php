<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Service;

use ActionEaseKit\Base\Attribute\RequiresRole;
use ActionEaseKit\Base\Exception\App403Exception;
use ActionEaseKit\Base\Traits\ClassNameTrait;
use ActionEaseKit\Base\Traits\ReflectionHelperTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractActionService implements ActionServiceInterface
{
    use ClassNameTrait, ReflectionHelperTrait;

    protected Request $request;


    public function __construct(protected TokenStorageInterface $tokenStorage)
    {
    }

    public function setRequest(Request $request) : void
    {
        $this->request = $request;
    }

    public function checkAccess(string $action): void
    {
        $attribute = $this->getFunctionAttributes($action, RequiresRole::class);

        if (!$attribute) return;

        $instance = $attribute->newInstance();
        $user = $this->getUser();

        if ($user && !array_intersect($user->getRoles(), $instance->getRoles())) {
            throw new App403Exception('Access Denied.');
        }
  }

    protected function getUser() : ?UserInterface
    {
        $token = $this->tokenStorage->getToken();

        if ($token && ($user = $token->getUser()) instanceof UserInterface) {
            return $user;
        }

        return null;
    }
}
