<?php


use Dotenv\Dotenv;
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
        (Dotenv::create(__DIR__, '.env'))->load();

        $data = [
            [
                'status'    => 1,
                'username'  => getenv('SEED_ROOT_USER'),
                'password'  => getenv('SEED_ROOT_PASS'),
                'issuer'    => getenv('APP_URL'),
                'tokenId'   => 'asdf',
                'tokenPassword' => 'fdsa',
            ],[
                'status'    => 1,
                'username'  => 'testuser',
                'password'  => 'testpassword',
                'issuer'    => 'https://niden.net',
                'tokenId'   => '12345',
                'tokenPassword' => '110011',
            ]
        ];

        $posts = $this->table('co_users');
        $posts->insert($data)
            ->saveData();
    }
}
