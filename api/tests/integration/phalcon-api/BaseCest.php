<?php

namespace Discoveryfy\Tests\integration\Phalcon\Api;

use Discoveryfy\Models\Users;
use Phalcon\Security\Random;

class BaseCest
{
    protected function getDefaultModel(): string
    {
        return Users::class;
    }

    protected function getDefaultModelAttributes(array $whitelist = [], array $blacklist = []): array
    {
        $rtn = $this->getKnownUserAttributes();
        if (empty($whitelist) && empty($blacklist)) {
            return $rtn;
        }
        $rtn = array_filter($rtn, function ($val) use ($blacklist) {
            return !in_array($val, $blacklist, true);
        });
        return array_filter($rtn, function ($val) use ($whitelist) {
            return in_array($val, $whitelist, true);
        });
    }

    private function getKnownUserAttributes()
    {
        return [
            'id'                => 'f756ffbe-6143-46f0-b914-f898b1f05f84',
            'enabled'           => true,
            'created_at'        => '2020-03-18 17:43:09',
            'updated_at'        => null,
            'username'          => 'testuser',
            'password'          => 'testpassword',
            'email'             => 'test@user.net',
            'enabled'           => true,
            'public_visibility' => false,
            'public_email'      => false,
            'language'          => 'en',
            'theme'             => 'default',
            'rol'               => 'ROLE_USER',
        ];
    }

    private function getRandomUserAttributes()
    {
        $datetime = new \Datetime('now', new \DateTimeZone('UTC'));
        return [
            'id'                => (new Random())->uuid(),
            'enabled'           => true,
            'created_at'        => $datetime->format('Y-m-d H:i:s'),
            'updated_at'        => $datetime->format('Y-m-d H:i:s'),
            'username'          => 'test_'.(new Random())->hex(5),
            'password'          => 'test_'.(new Random())->hex(5),
            'email'             => 'test_'.(new Random())->hex(5).'@test.com',
            'public_visibility' => true,
            'public_email'      => true,
            'language'          => 'en',
            'theme'             => 'default',
            'rol'               => 'ROLE_USER',
        ];
    }
}
