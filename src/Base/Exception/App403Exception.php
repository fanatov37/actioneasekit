<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Exception;

use Symfony\Component\HttpFoundation\Response;

/*
 * 403 Forbidden The request was valid, but the server is refusing action.
 * The user might not have the necessary permissions for a resource, or may need an account of some sort.
 *
 */
class App403Exception extends \Exception implements AppExceptionInterface
{
    public function getHttpCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
