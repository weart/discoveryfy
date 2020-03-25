<?php

use Phinx\Migration\AbstractMigration;

class CreateComments extends AbstractMigration
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
        $polls_table = $this->table('comments', [ 'id' => false, 'primary_key' => [ 'id' ]]);
        $polls_table
            ->addColumn('id', 'uuid', [ 'null' => false, 'comment' => 'Comment unique identifier' ])
            ->addColumn('user_id', 'uuid', [ 'null' => false, 'comment' => 'User unique identifier' ])
            //Method 1: Three keys
            ->addColumn('poll_id', 'uuid', [ 'null' => true, 'comment' => 'Poll unique identifier' ])
            ->addColumn('track_id', 'uuid', [ 'null' => true, 'comment' => 'Track unique identifier' ])
            ->addColumn('organization_id', 'uuid', [ 'null' => true, 'comment' => 'Organization unique identifier' ])
            //Method 2: Two columns relation_type & relation_uuid

            ->addTimestampsWithTimezone() //Add fields: created_at and updated_at
            ->addColumn('message', 'string', [ 'limit' => 255, 'null' => false, 'comment' => 'Content of the message' ])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'comment_user_id'
            ])
            ->addForeignKey('poll_id', 'polls', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'comment_poll_id'
            ])
            ->addForeignKey('track_id', 'tracks', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'comment_track_id'
            ])
            ->addForeignKey('organization_id', 'organizations', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'comment_organization_id'
            ])
            ->create();
    }
}
