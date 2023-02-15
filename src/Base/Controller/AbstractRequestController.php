<?php

namespace App\Base\Controller;

use App\Base\Exception\App404Exception;
use App\Base\Service\IActionService;
use App\Base\Service\IRoleIAction;
use App\Base\Service\ValidationInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
                $this->getLogger()->info($this::class, $content);
            }

            $service = $content[$this::SERVICE_ARGUMENT_NAME];
            $actionService = $this->actionServices[$service] ?? throw new App404Exception("Action {$service} not exist");
            $actionService->setRequest($request);

            if ($content[$this::ARGUMENT_NAME] && $actionService instanceof ValidationInterface) {
                $validation = $actionService->getValidationClass();

                if (!is_object($validation)) {
                    $validation = new $validation();
                }

                $validationResult = call_user_func_array([$validation, $content[$this::ACTION_ARGUMENT_NAME] . ValidationInterface::POSTFIXUS], array_values($content[$this::ARGUMENT_NAME]));

                if (is_array($validationResult)) {
                    $content[$this::ARGUMENT_NAME] = [];
                    $content[$this::ARGUMENT_NAME][] = $validationResult;
                }
            }

            $actionMethodName = $content[$this::ACTION_ARGUMENT_NAME];

            if ($actionService instanceof IRoleIAction && !$actionService->checkRoleAccessToAction($actionMethodName)) {
                throw new App404Exception('Not access to action');
            }

            $responseData = call_user_func_array([$actionService, $actionMethodName], array_values($content[$this::ARGUMENT_NAME]));

            return new JsonResponse($responseData);

        } catch (MissingOptionsException $exception){
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function resolveArguments(array $arguments): array
    {
        $resolver = (new OptionsResolver())
            ->setRequired([$this::SERVICE_ARGUMENT_NAME, $this::ACTION_ARGUMENT_NAME])
            ->setDefined([$this::ARGUMENT_NAME])

            ->setAllowedTypes($this::SERVICE_ARGUMENT_NAME, 'string')
            ->setAllowedTypes($this::ACTION_ARGUMENT_NAME, 'string')
            ->setAllowedTypes($this::ARGUMENT_NAME, 'array')

            ->setDefault($this::ARGUMENT_NAME, []);

        $arguments = $resolver->resolve($arguments);

        return $arguments;
    }
}
