<?php

use Phalcon\Security;
use Phalcon\Security\Random;
use Phinx\Seed\AbstractSeed;

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
        $data = [
            [
                'id'                => $this->getRandomService()->uuid(),
                'username'          => getenv('SEED_ROOT_USER'),
                'password'          => $this->getSecurityService()->hash(getenv('SEED_ROOT_PASS')),
                'email'             => getenv('SEED_ROOT_MAIL'),
                'enabled'           => true,
                'public_visibility' => true,
                'public_email'      => true,
                'language'          => 'ca',
                'theme'             => 'default',
                'rol'               => 'ROLE_ADMIN',
            ],[
//                'id'                => $this->getRandomService()->uuid(),
                'id'                => 'f756ffbe-6143-46f0-b914-f898b1f05f84',
                'username'          => 'testuser',
                'password'          => $this->getSecurityService()->hash('testpassword'),
                'email'             => 'test@user.net',
                'enabled'           => true,
                'public_visibility' => false,
                'public_email'      => false,
                'language'          => 'en',
                'theme'             => 'default',
                'rol'               => 'ROLE_USER',
            ],[
//                'id'                => $this->getRandomService()->uuid(),
                'id'                => '5860b321-e7ce-4927-8b81-0f0dd6058350',
                'username'          => 'testaltuser',
                'password'          => $this->getSecurityService()->hash('testaltpassword'),
                'email'             => 'testalt@user.net',
                'enabled'           => true,
                'public_visibility' => false,
                'public_email'      => false,
                'language'          => 'en',
                'theme'             => 'default',
                'rol'               => 'ROLE_USER',
            ]
        ];

        $this->table('users')->insert($data)->saveData();
    }

    /**
     * Taken from SecurityProvider
     */
    protected function getSecurityService(): Security
    {
        $security = new Security();
        // set Work factor (how many times we go through)
        $security->setWorkFactor(12); // can be a number from 1-12
        // set Default Hash
        $security->setDefaultHash(Security::CRYPT_BLOWFISH_Y); // choose default hash
        return $security;
    }

    protected function getRandomService(): Random
    {
        return (new Random());
    }
}
