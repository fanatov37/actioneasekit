<?php

namespace ActionEaseKit\Base\Controller;

use ActionEaseKit\Base\Exception\App404Exception;
use ActionEaseKit\Base\Service\IActionService;
use ActionEaseKit\Base\Service\IRoleIAction;
use ActionEaseKit\Base\Service\ValidationInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbstractRequestController extends AbstractFOSRestController
{
    protected const SERVICE_ARGUMENT_NAME = 'service';
    protected const ACTION_ARGUMENT_NAME = 'action';
    protected const ARGUMENT_NAME = 'arguments';

    private array $actionServices = [];

    public function __construct(IActionService ...$actionServices)
    {
        foreach ($actionServices as $actionService) {
            $this->actionServices[$actionService->getClassName()] = $actionService;
        }
    }

    public function indexAction(Request $request): JsonResponse
    {
        try {
            $content = $this->resolveArguments($request->request->all());

            if ($this instanceof ILoggerController) {
                $this->getLogger()->info(static::class, $content);
            }

            $service = $content[static::SERVICE_ARGUMENT_NAME];
            $actionService = $this->actionServices[$service] ?? throw new App404Exception("Action $service not exist");
            $actionService->setRequest($request);

            if ($content[static::ARGUMENT_NAME] && $actionService instanceof ValidationInterface) {
                $validation = $actionService->getValidationClass();

                if (!is_object($validation)) {
                    $validation = new $validation();
                }

                $validationResult = call_user_func_array([
                    $validation, $content[static::ACTION_ARGUMENT_NAME] . ValidationInterface::POSTFIXUS],
                    array_values($content[static::ARGUMENT_NAME])
                );

                if (is_array($validationResult)) {
                    $content[static::ARGUMENT_NAME] = [];
                    $content[static::ARGUMENT_NAME][] = $validationResult;
                }
            }

            $actionMethodName = $content[static::ACTION_ARGUMENT_NAME];

            if ($actionService instanceof IRoleIAction && !$actionService->checkRoleAccessToAction($actionMethodName)) {
                throw new App404Exception('Not access to action');
            }

            $responseData = call_user_func_array([$actionService, $actionMethodName], array_values($content[static::ARGUMENT_NAME]));

            return new JsonResponse($responseData);

        } catch (MissingOptionsException $exception) {
            throw new App404Exception($exception->getMessage());
        } catch (\Throwable $exception) {
            throw new App404Exception($exception->getMessage());
        }
    }

    private function resolveArguments(array $arguments): array
    {
        return (new OptionsResolver())
            ->setRequired([static::SERVICE_ARGUMENT_NAME, static::ACTION_ARGUMENT_NAME])
            ->setDefined([static::ARGUMENT_NAME])

            ->setAllowedTypes(static::SERVICE_ARGUMENT_NAME, 'string')
            ->setAllowedTypes(static::ACTION_ARGUMENT_NAME, 'string')
            ->setAllowedTypes(static::ARGUMENT_NAME, 'array')

            ->setDefault(static::ARGUMENT_NAME, [])

            ->resolve($arguments);
    }
}
