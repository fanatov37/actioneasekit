<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Controller;

use ActionEaseKit\Base\Attribute\ValidationAttribute;
use ActionEaseKit\Base\Exception\App404Exception;
use ActionEaseKit\Base\Exception\AppExceptionInterface;
use ActionEaseKit\Base\Exception\HelperException;
use ActionEaseKit\Base\Service\AbstractActionService;
use ActionEaseKit\Base\Service\ActionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;

class AbstractRequestController extends AbstractController
{
    protected const SERVICE_KEY = 'service';
    protected const ACTION_KEY = 'action';
    protected const ARGUMENT_KEY = 'arguments';

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

            $service = $content[static::SERVICE_KEY];
            $actionMethodName = $content[static::ACTION_KEY];
            $arguments = $content[static::ARGUMENT_KEY];

            $actionService = $this->actionServices[$service] ?? throw new App404Exception("Action $service not exist");
            $actionService->setRequest($request);

            $arguments = $actionService->checkValidation($actionMethodName, $arguments);

            $actionService->checkAccess($actionMethodName);
            $responseData = call_user_func_array([$actionService, $actionMethodName], array_values($arguments));

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
            ->setRequired([static::SERVICE_KEY, static::ACTION_KEY])
            ->setDefined([static::ARGUMENT_KEY])

            ->setAllowedTypes(static::SERVICE_KEY, 'string')
            ->setAllowedTypes(static::ACTION_KEY, 'string')
            ->setAllowedTypes(static::ARGUMENT_KEY, 'array')

            ->setDefault(static::ARGUMENT_KEY, [])

            ->resolve($arguments);
    }
}
