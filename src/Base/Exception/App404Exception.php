<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Exception;

use Symfony\Component\HttpFoundation\Response;

/*
 * 404 Not Found The requested resource could not be found but may
 * be available in the future. Subsequent requests by the client are permissible.
 *
 */
class App404Exception extends \Exception implements AppExceptionInterface
{
    public function getHttpCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
