<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Exception;

use Symfony\Component\HttpFoundation\Response;

/*
 * 401 Unauthorized (RFC 7235)
 * Similar to 403 Forbidden, but specifically for use when authentication is
 * required and has failed or has not yet been provided. The response must include
 * a WWW-Authenticate header field containing a challenge applicable to the requested resource.
 * See Basic access authentication and Digest access authentication.[34]
 * 401 semantically means "unauthenticated",[35] i.e. the user does not have the necessary credentials.
 * Note: Some sites incorrectly issue HTTP 401 when an IP address is banned
 * from the website (usually the website domain) and that specific address is refused permission to access a website.
 *
 */
class App401Exception extends \Exception implements AppExceptionInterface
{
    public function getHttpCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
