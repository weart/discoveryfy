<?php

use Phinx\Seed\AbstractSeed;
use Phalcon\Security\Random;

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

        $data = [
            [
                'id'                => $random->uuid(),
                'username'          => getenv('SEED_ROOT_USER'),
                'password'          => getenv('SEED_ROOT_PASS'),
                'jwt_private_key'   => getenv('SEED_ROOT_KEY'),
                'email'             => getenv('SEED_ROOT_MAIL'),
                'enabled'           => true,
                'public_visibility' => true,
                'public_email'      => true,
                'language'          => 'ca',
                'theme'             => 'default',
                'rol'               => 'ROLE_ADMIN',
            ],[
                'id'                => $random->uuid(),
                'username'          => 'testuser',
                'password'          => 'testpassword',
                'jwt_private_key'   => '110011',
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
}
