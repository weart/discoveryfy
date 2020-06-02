<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Constants;

use Lcobucci\JWT\Token;

class CacheKeys
{
    const LOGIN_CSRF        = 'login.csrf.';
    const REGISTER_CSRF     = 'register.csrf.';
    const JWT               = 'jwt.';
    const MODEL             = 'model.';
    const QUERY             = 'query.';
    const SPO_ACC_TKN       = 'SPO_ACC_TKN';
    const QUEUE             = 'QUEUE';
    const JOB_QUEUE         = 'JOB';

    public static function getLoginCSRFCacheKey(string $csrf_token): string
    {
        return self::createKey(self::LOGIN_CSRF, $csrf_token);
    }

    public static function getRegisterCSRFCacheKey(string $csrf_token): string
    {
        return self::createKey(self::REGISTER_CSRF, $csrf_token);
    }

    public static function getJWTCacheKey(Token $token): string
    {
        return self::createKey(self::JWT, $token->__toString());
    }

    public static function getModelCacheKey(string $model, string $uuid): string
    {
        return self::createKey(self::MODEL, sprintf('%s.%s', $model, $uuid));
    }

    public static function getQueryCacheKey(string $sql, string $params): string
    {
        return self::createKey(self::QUERY, sprintf('%s.%s', $sql, $params));
    }

    public static function getSpotifyAccessToken(): string
    {
        return self::SPO_ACC_TKN;
    }

    public static function getQueue(string $name): string
    {
        return self::QUEUE . '_' . $name;
    }

    public static function getJobQueue(): string
    {
        return self::getQueue(self::JOB_QUEUE);
    }

    /**
     * @param string $prefix
     * @param string $payload obfuscated with a sha1 for more security
     * @return string
     */
    private static function createKey(string $prefix, string $payload): string
    {
        return $prefix . '_' . sha1($payload);
    }
}
