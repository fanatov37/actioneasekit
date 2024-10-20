<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Exception;

use Symfony\Component\HttpFoundation\Response;

/*
 * 500 Internal Server Error
 * A generic error message, given when an unexpected condition
 * was encountered and no more specific message is suitable.[62]
 *
 */
class App500Exception extends \Exception implements AppExceptionInterface
{
    public function getHttpCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
