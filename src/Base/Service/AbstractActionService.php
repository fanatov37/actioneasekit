<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Service;

use ActionEaseKit\Base\Attribute\RequiresRoleAttribute;
use ActionEaseKit\Base\Attribute\ValidationAttribute;
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
        $attribute = $this->getFunctionAttributes($action, RequiresRoleAttribute::class);

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

    public function checkValidation(string $action, array $arguments) : array
    {
        $validationAttribute = $this->getClassAttributes(ValidationAttribute::class);

        if ($arguments && $validationAttribute) {

            /** @var ValidationAttribute $validationAttributeInstance */
            $validationAttributeInstance = $validationAttribute->newInstance();
            $validationClassName = new $validationAttributeInstance->validationClass();
            $validationClass = new $validationClassName();

            $validationMethod = $action . ValidationAttribute::POSTFIXUS;

            if (method_exists($validationClass, $validationMethod)) {
                $validationResult = call_user_func_array([
                    $validationClass, $action . ValidationAttribute::POSTFIXUS],
                    array_values($arguments)
                );

                if (is_array($validationResult)) {
                    $arguments = [];
                    $arguments[] = $validationResult;
                }
            }
        }

        return $arguments;
    }
}
