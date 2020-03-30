<?php

use Phinx\Migration\AbstractMigration;
//use Phinx\Util\Literal;
//use Phinx\Db\Adapter\MysqlAdapter;

class CreateUsersGroupsMembershipsSessions extends AbstractMigration
{

    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        //Queries from doctrine
//      CREATE TABLE users
//          (id UUID NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL,
//          created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL,
//          enabled BOOLEAN DEFAULT 'true' NOT NULL, public_visibility BOOLEAN DEFAULT 'false' NOT NULL, public_email BOOLEAN DEFAULT 'false' NOT NULL,
//          language VARCHAR(255) DEFAULT 'en' NOT NULL, theme VARCHAR(255) DEFAULT 'default' NOT NULL, roles TEXT NOT NULL, PRIMARY KEY(id));
//      CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username);
//      CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email);
//      COMMENT ON COLUMN users.id IS '(DC2Type:uuid)';
//      COMMENT ON COLUMN users.created_at IS '(DC2Type:datetimetz_immutable)';
//      COMMENT ON COLUMN users.roles IS '(DC2Type:simple_array)';
        $users_table = $this->table('users', [ 'id' => false, 'primary_key' => [ 'id' ]]);
        $users_table
            ->addColumn('id', 'uuid', [ 'null' => false, 'comment' => 'User unique identifier' ])
            ->addTimestampsWithTimezone() //Add fields: created_at and updated_at
            ->addColumn('deleted_at', 'timestamp', [ 'null' => true, 'default' => null, 'timezone' => true]) //SoftDelete
            ->addColumn('username', 'string', [ 'limit' => 255, 'null' => false, 'comment' => 'Nickname of the user, must be unique' ])
            ->addColumn('password', 'string', [ 'limit' => 255, 'null' => false, 'comment' => 'Only encrypted passwords!' ])
            ->addColumn('email', 'string', [ 'limit' => 255, 'null' => false ])
            ->addColumn('enabled', 'boolean', [ 'default' => true, 'null' => false, 'signed' => false, 'comment' => 'Define if the user is active' ])
            ->addColumn('public_visibility', 'boolean', [ 'default' => false, 'null' => false, 'signed' => false, 'comment' => 'Is this user visible to anyone?' ])
            ->addColumn('public_email', 'boolean', [ 'default' => false, 'null' => false, 'signed' => false, 'comment' => 'Is the email of this user visible to anyone?' ])
            ->addColumn('language', 'enum', [ 'values' => 'en,es,ca', 'null' => false, 'comment' => 'Language of the app' ])
            ->addColumn('theme', 'enum', [ 'values' => 'default', 'null' => true, 'comment' => 'Theme of the app' ])
            ->addColumn('rol', 'enum', [ 'values' => 'ROLE_ADMIN,ROLE_USER', 'default' => 'ROLE_USER', 'null' => false, 'comment' => 'Role of the user in the app' ])
            ->addIndex([ 'username' ], [ 'unique' => true, 'name' => 'idx_user_username' ])
            ->addIndex([ 'email' ], [ 'unique' => true, 'name' => 'idx_user_email' ])
            ->create();

//      CREATE TABLE organizations (id UUID NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, public_visibility BOOLEAN DEFAULT 'true' NOT NULL, public_membership BOOLEAN DEFAULT 'false' NOT NULL, can_create_polls SMALLINT DEFAULT 1 NOT NULL, PRIMARY KEY(id));
//      COMMENT ON COLUMN organizations.id IS '(DC2Type:uuid)';
//      COMMENT ON COLUMN organizations.created_at IS '(DC2Type:datetimetz_immutable)';
        $orgs_table = $this->table('organizations', [ 'id' => false, 'primary_key' => [ 'id' ]]);
        $orgs_table
            ->addColumn('id', 'uuid', [ 'null' => false, 'comment' => 'Organization unique identifier' ])
            ->addTimestampsWithTimezone() //Add fields: created_at and updated_at
            ->addColumn('deleted_at', 'timestamp', [ 'null' => true, 'default' => null, 'timezone' => true]) //SoftDelete
            ->addColumn('name', 'string', [ 'limit' => 255, 'null' => false, 'comment' => 'Name of the organization' ])
            ->addColumn('description', 'string', [ 'limit' => 255, 'null' => true, 'comment' => 'Description of the organization' ])
            ->addColumn('public_visibility', 'boolean', [ 'default' => false, 'null' => false, 'signed' => false, 'comment' => 'Is this group public and can be seen be anyone?' ])
            ->addColumn('public_membership', 'boolean', [ 'default' => false, 'null' => false, 'signed' => false, 'comment' => 'Can anyone be part of this group? Or an invitation is required?' ])
            ->addColumn('who_can_create_polls', 'enum', [ 'values' => 'MEMBERS,ADMINS,OWNERS', 'default' => 'ADMINS', 'null' => false, 'comment' => 'Define the rol needed for create one new poll' ])
            ->create();

//      CREATE TABLE memberships (id UUID NOT NULL, member_id UUID NOT NULL, organization_id UUID NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, rol SMALLINT NOT NULL, PRIMARY KEY(id));
//      CREATE INDEX IDX_865A47767597D3FE ON memberships (member_id);
//      CREATE INDEX IDX_865A477632C8A3DE ON memberships (organization_id);
//      COMMENT ON COLUMN memberships.id IS '(DC2Type:uuid)';
//      COMMENT ON COLUMN memberships.member_id IS '(DC2Type:uuid)';
//      COMMENT ON COLUMN memberships.organization_id IS '(DC2Type:uuid)';
//      COMMENT ON COLUMN memberships.created_at IS '(DC2Type:datetimetz_immutable)';
        $members_table = $this->table('memberships', [ 'id' => false, 'primary_key' => [ 'id' ]]);
        $members_table
            ->addColumn('id', 'uuid', [ 'null' => false, 'comment' => 'Membership unique identifier' ])
            ->addColumn('user_id', 'uuid', [ 'null' => false, 'comment' => 'User unique identifier' ])
            ->addColumn('organization_id', 'uuid', [ 'null' => false, 'comment' => 'Organization unique identifier' ])
            ->addColumn('rol', 'enum', [ 'values' => 'ROLE_OWNER,ROLE_ADMIN,ROLE_MEMBER,ROLE_INVITED', 'default' => 'ROLE_INVITED', 'null' => false ])
        // Foreign keys
//      ALTER TABLE memberships ADD CONSTRAINT FK_865A47767597D3FE FOREIGN KEY (member_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
//      ALTER TABLE memberships ADD CONSTRAINT FK_865A477632C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
            ->addIndex([ 'user_id', 'organization_id' ], [ 'unique' => true, 'name' => 'idx_membership_user_id_organization_id' ])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'membership_user_id'
            ])
            ->addForeignKey('organization_id', 'organizations', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'membership_organization_id'
            ])
            ->addTimestampsWithTimezone() //Add fields: created_at and updated_at
            ->create();

//      CREATE TABLE sessions (id UUID NOT NULL, user_id UUID DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, sess_id VARCHAR(128) DEFAULT NULL, sess_data BYTEA DEFAULT NULL, sess_time INT DEFAULT NULL, sess_lifetime INT DEFAULT NULL, PRIMARY KEY(id));
//      CREATE INDEX IDX_9A609D13A76ED395 ON sessions (user_id);
//      COMMENT ON COLUMN sessions.id IS '(DC2Type:uuid)';
//      COMMENT ON COLUMN sessions.user_id IS '(DC2Type:uuid)';
//      COMMENT ON COLUMN sessions.created_at IS '(DC2Type:datetimetz_immutable)';
        $sessions_table = $this->table('sessions', [ 'id' => false, 'primary_key' => [ 'id' ]]);
        $sessions_table
            ->addColumn('id', 'uuid', [ 'null' => false, 'comment' => 'Session unique identifier' ])
            ->addColumn('user_id', 'uuid', [ 'null' => true, 'comment' => 'User unique identifier' ])
            ->addTimestampsWithTimezone() //Add fields: created_at and updated_at
            ->addColumn('name', 'string', [ 'limit' => 255, 'null' => true, 'comment' => 'Nickname of the user' ])
            //¿php session?
        // Foreign keys
//      ALTER TABLE sessions ADD CONSTRAINT FK_9A609D13A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'session_user_id'
            ])
            ->create();

        $security_events = $this->table('security_events', [ 'id' => false, 'primary_key' => [ 'id' ]]);
        $security_events
            ->addColumn('id', 'uuid', [ 'null' => false, 'comment' => 'Security event unique identifier' ])
            ->addColumn('user_id', 'uuid', [ 'null' => true, 'comment' => 'User unique identifier' ])
            ->addColumn('created_at', 'timestamp', [ 'default' => 'CURRENT_TIMESTAMP', 'update' => '', 'timezone' => true ])
            ->addColumn('type', 'enum', [ 'values' => 'login_success,login_failure,email_confirmation,password_change,password_reset', 'null' => false, 'comment' => 'Type of event' ])
            ->addColumn('ip_address', 'string', [ 'limit' => 255, 'null' => true, 'comment' => 'Request IP address' ])
            ->addColumn('user_agent', 'string', [ 'limit' => 255, 'null' => true, 'comment' => 'Request user agent' ])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'no_action', 'update' => 'RESTRICT', 'constraint' => 'security_event_user_id'
            ])
            ->create();
    }
}
