<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Controller;

use ActionEaseKit\Base\Exception\App404Exception;
use ActionEaseKit\Base\Exception\AppExceptionInterface;
use ActionEaseKit\Base\Exception\HelperException;
use ActionEaseKit\Base\Service\AbstractActionService;
use ActionEaseKit\Base\Service\ActionServiceInterface;
use ActionEaseKit\Base\Service\ValidationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;

class AbstractRequestController extends AbstractController
{
    protected const SERVICE_ARGUMENT_NAME = 'service';
    protected const ACTION_ARGUMENT_NAME = 'action';
    protected const ARGUMENT_NAME = 'arguments';

    /** @var array|AbstractActionService[]  */
    private array $actionServices = [];

    public function __construct(ActionServiceInterface ...$actionServices)
    {
        foreach ($actionServices as $actionService) {
            $this->actionServices[$actionService->getClassName()] = $actionService;
        }
    }

    #[Route(name: 'index', methods: ['POST'])]
    public function indexAction(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $content = $this->resolveArguments($data);

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

            $actionService->checkAccess($actionMethodName);
            $responseData = call_user_func_array([$actionService, $actionMethodName], array_values($content[static::ARGUMENT_NAME]));

            return new JsonResponse($responseData);

        } catch (AppExceptionInterface $exception) {
            return new JsonResponse(HelperException::getFullInfo($exception), $exception->getHttpCode());

        } catch (\Throwable $exception) {
            return new JsonResponse(HelperException::getFullInfo($exception), Response::HTTP_NOT_FOUND);
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
