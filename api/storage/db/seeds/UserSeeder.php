<?php

use Phinx\Seed\AbstractSeed;
use Phalcon\Security\Random;
use Phalcon\Security;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $random = new Random();
        $security = $this->getSecurityService();

        $data = [
            [
                'id'                => $random->uuid(),
                'username'          => getenv('SEED_ROOT_USER'),
                'password'          => $security->hash(getenv('SEED_ROOT_PASS')),
                'email'             => getenv('SEED_ROOT_MAIL'),
                'enabled'           => true,
                'public_visibility' => true,
                'public_email'      => true,
                'language'          => 'ca',
                'theme'             => 'default',
                'rol'               => 'ROLE_ADMIN',
            ],[
//                'id'                => $random->uuid(),
                'id'                => 'f756ffbe-6143-46f0-b914-f898b1f05f84',
                'username'          => 'testuser',
                'password'          => $security->hash('testpassword'),
                'email'             => 'test@user.net',
                'enabled'           => true,
                'public_visibility' => false,
                'public_email'      => false,
                'language'          => 'en',
                'theme'             => 'default',
                'rol'               => 'ROLE_USER',
            ]
        ];

        $posts = $this->table('users');
        $posts->insert($data)
            ->saveData();
    }

    /**
     * Taken from SecurityProvider
     */
    private function getSecurityService(): Security
    {
        $security = new Security();
        // set Work factor (how many times we go through)
        $security->setWorkFactor(12); // can be a number from 1-12
        // set Default Hash
        $security->setDefaultHash(Security::CRYPT_BLOWFISH_Y); // choose default hash
        return $security;
    }
}
