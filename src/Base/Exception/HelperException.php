<?php

namespace StreakSymfony\Base\Exception;

/** @codeCoverageIgnore  */
class HelperException
{
    public static function getFullInfo(\Throwable $exception, array $additionalData = []) : array
    {
        return array_merge($additionalData, [
            'msg' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }
}