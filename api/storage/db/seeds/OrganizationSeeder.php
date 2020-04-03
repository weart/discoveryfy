<?php

//use Phalcon\Security;
use Phalcon\Security\Random;
use Phinx\Seed\AbstractSeed;
//use RuntimeException;

class OrganizationSeeder extends AbstractSeed
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
        $datetime = new \Datetime('now', new \DateTimeZone('UTC'));
        $org_id = $this->getRandomService()->uuid();

        $orgs = [
            [
                'id'                    => $org_id,
                'created_at'            => $datetime->format('Y-m-d H:i:s'),
                'updated_at'            => $datetime->format('Y-m-d H:i:s'),
                'name'                  => 'Discoveryfy',
                'description'           => 'Discoveryfy Public Group! Come and share!',
                'public_visibility'     => true,
                'public_membership'     => true,
                'who_can_create_polls'  => 'OWNERS',
            ],[
                'id'                    => '52bff0b0-4d4f-4df7-b55a-d55dbac5d033', //Fixed id for tests
                'created_at'            => $datetime->format('Y-m-d H:i:s'),
                'updated_at'            => $datetime->format('Y-m-d H:i:s'),
                'name'                  => 'Discoveryfy Testing Group',
                'description'           => 'Nothing to see here, this is just for testing',
                'public_visibility'     => false,
                'public_membership'     => false,
                'who_can_create_polls'  => 'OWNERS',
            ]
        ];

        $root_id = ''; //The id of the user created in UserSeeder, with the name given with getenv('SEED_ROOT_USER')
        if (empty($root_id)) {
            throw new RuntimeException('For security reasons this id must be setted manually');
        }
        $members = [
            [
                'user_id'           => $root_id,
                'organization_id'   => $org_id,
                'id'                => $this->getRandomService()->uuid(),
                'created_at'        => $datetime->format('Y-m-d H:i:s'),
                'updated_at'        => $datetime->format('Y-m-d H:i:s'),
                'rol'               => 'ROLE_OWNER'
            ],
            [
                'user_id'           => 'f756ffbe-6143-46f0-b914-f898b1f05f84', //test
                'organization_id'   => '52bff0b0-4d4f-4df7-b55a-d55dbac5d033',
                'id'                => $this->getRandomService()->uuid(),
                'created_at'        => $datetime->format('Y-m-d H:i:s'),
                'updated_at'        => $datetime->format('Y-m-d H:i:s'),
                'rol'               => 'ROLE_OWNER'
            ]
        ];

        $this->table('organizations')->insert($orgs)->saveData();
        $this->table('memberships')->insert($members)->saveData();
    }

    /**
     * Taken from SecurityProvider
    protected function getSecurityService(): Security
    {
        $security = new Security();
        // set Work factor (how many times we go through)
        $security->setWorkFactor(12); // can be a number from 1-12
        // set Default Hash
        $security->setDefaultHash(Security::CRYPT_BLOWFISH_Y); // choose default hash
        return $security;
    }
     */

    protected function getRandomService(): Random
    {
        return (new Random());
    }
}
