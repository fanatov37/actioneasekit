<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * 400 Bad Request
 * The server cannot or will not process the request due to an apparent
 * client error (e.g., malformed request syntax, size too large, invalid request message framing,
 * or deceptive request routing).
 *
 */
class App400Exception extends \Exception implements AppExceptionInterface
{
    public function getHttpCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
