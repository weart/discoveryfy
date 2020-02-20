<?php

use Phinx\Migration\AbstractMigration;
//use Phinx\Util\Literal;
//use Phinx\Db\Adapter\MysqlAdapter;

class CreatePollsTracksVotes extends AbstractMigration
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
//      CREATE TABLE polls (id UUID NOT NULL, organization_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL,
//          spotify_playlist_images JSON DEFAULT NULL, spotify_playlist_public BOOLEAN DEFAULT NULL, spotify_playlist_collaborative BOOLEAN DEFAULT NULL, spotify_playlist_uri VARCHAR(255) DEFAULT NULL,
//          spotify_winner_playlist_uri VARCHAR(255) DEFAULT NULL, spotify_historic_playlist_uri VARCHAR(255) DEFAULT NULL,
//          start_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, restart_date VARCHAR(255) DEFAULT NULL,
//          public_visibility BOOLEAN DEFAULT 'false' NOT NULL, public_votes BOOLEAN DEFAULT 'false' NOT NULL,
//          anon_can_vote BOOLEAN DEFAULT 'true' NOT NULL, who_can_add_track SMALLINT DEFAULT NULL, anon_votes_max_rating SMALLINT DEFAULT 1 NOT NULL,
//          user_votes_max_rating SMALLINT DEFAULT 10 NOT NULL, multiple_user_tracks BOOLEAN DEFAULT 'true' NOT NULL, multiple_anon_tracks BOOLEAN DEFAULT 'true' NOT NULL, PRIMARY KEY(id));
//      CREATE INDEX IDX_1D3CC6EE32C8A3DE ON polls (organization_id);
//      COMMENT ON COLUMN polls.id IS '(DC2Type:uuid)';
//      COMMENT ON COLUMN polls.organization_id IS '(DC2Type:uuid)';
//      COMMENT ON COLUMN polls.start_date IS '(DC2Type:datetimetz_immutable)';
//      COMMENT ON COLUMN polls.end_date IS '(DC2Type:datetimetz_immutable)';
//      ALTER TABLE polls ADD CONSTRAINT FK_1D3CC6EE32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        $polls_table = $this->table('polls', [ 'id' => false, 'primary_key' => [ 'id' ]]);
        $polls_table
            ->addColumn('id', 'uuid', [ 'null' => false, 'comment' => 'Poll unique identifier' ])
            ->addColumn('organization_id', 'uuid', [ 'null' => false, 'comment' => 'Organization unique identifier' ])
            ->addTimestampsWithTimezone() //Add fields: created_at and updated_at
            ->addColumn('name', 'string', [ 'limit' => 255, 'null' => false, 'comment' => 'Name of the poll' ])
            ->addColumn('description', 'string', [ 'limit' => 255, 'null' => true, 'comment' => 'Description of the poll, optional' ])
            ->addColumn('spotify_playlist_images', 'json', [ 'default' => null, 'null' => true, 'comment' => 'Json with all the images provided by Spotify' ])
            ->addColumn('spotify_playlist_public', 'boolean', [ 'default' => false, 'null' => true, 'signed' => false, 'comment' => 'Define the visibility of the playlist in Spotify' ])
            ->addColumn('spotify_playlist_collaborative', 'boolean', [ 'default' => false, 'null' => true, 'signed' => false, 'comment' => 'If true, the playlist will become collaborative in Spotify' ])
            ->addColumn('spotify_playlist_uri', 'string', [ 'default' => null, 'null' => true, 'comment' => 'Spotify playlist identifier, prepend "spotify:playlist:" for a valid spotify uri' ])
            ->addColumn('spotify_playlist_winner_uri', 'string', [ 'default' => null, 'null' => true, 'comment' => 'Spotify playlist identifier where the winner of the poll will be saved on finish, prepend "spotify:playlist:" for a valid spotify uri' ])
            ->addColumn('spotify_playlist_historic_uri', 'string', [ 'default' => null, 'null' => true, 'comment' => 'Spotify playlist identifier where all the songs of the poll will be saved on finish, prepend "spotify:playlist:" for a valid spotify uri' ])
            ->addColumn('start_date', 'datetime', [ 'null' => true, 'comment' => 'The start datetime of the poll' ])
            ->addColumn('end_date', 'datetime', [ 'null' => true, 'comment' => 'The end datetime of the poll, only used if restart_date is empty.' ])
            ->addColumn('restart_date', 'string', [ 'null' => true, 'comment' => 'String with a crontab style restart command.' ])
            ->addColumn('public_visibility', 'boolean', [ 'default' => false, 'null' => false, 'signed' => false, 'comment' => 'Is this poll visible to anyone or only to the members of the group?' ])
            ->addColumn('public_votes', 'boolean', [ 'default' => false, 'null' => false, 'signed' => false, 'comment' => 'Are the votes public meanwhile the poll is active?' ])
            ->addColumn('anon_can_vote', 'boolean', [ 'default' => false, 'null' => false, 'signed' => false, 'comment' => 'Can anyone vote into this poll or only the members of the group?' ])
            ->addColumn('who_can_add_track', 'enum', [ 'values' => 'OWNERS,ADMINS,MEMBERS,USERS,ANYONE', 'null' => false, 'comment' => 'Who can add tracks into this poll?' ])
            ->addColumn('anon_votes_max_rating', 'smallinteger', [ 'default' => 0, 'null' => false, 'signed' => false, 'comment' => 'All the ratings given by an anonymous user to this poll can\'t excede this number' ])
            ->addColumn('user_votes_max_rating', 'smallinteger', [ 'default' => 0, 'null' => false, 'signed' => false, 'comment' => 'All the ratings given by a member to this poll can\'t excede this number' ])
            ->addColumn('multiple_user_tracks', 'boolean', [ 'default' => false, 'null' => false, 'signed' => false, 'comment' => 'Can one user add more than one track to this poll?' ])
            ->addColumn('multiple_anon_tracks', 'boolean', [ 'default' => false, 'null' => false, 'signed' => false, 'comment' => 'Can an anonymous user add more than one track to this poll?' ])
            ->addForeignKey('organization_id', 'organizations', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'poll_organization_id'
            ])
            ->create();

//        CREATE TABLE tracks (id UUID NOT NULL, poll_id UUID NOT NULL, session_id UUID NOT NULL, user_id UUID DEFAULT NULL,
//          artist VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, proposal_date TIMESTAMP(0) WITH TIME ZONE NOT NULL,
//          spotify_uri VARCHAR(255) DEFAULT NULL, spotify_images JSON DEFAULT NULL, youtube_uri VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id));
//        CREATE INDEX IDX_246D2A2E3C947C0F ON tracks (poll_id);
//        CREATE INDEX IDX_246D2A2E613FECDF ON tracks (session_id);
//        CREATE INDEX IDX_246D2A2EA76ED395 ON tracks (user_id);
//        COMMENT ON COLUMN tracks.id IS '(DC2Type:uuid)';
//        COMMENT ON COLUMN tracks.poll_id IS '(DC2Type:uuid)';
//        COMMENT ON COLUMN tracks.session_id IS '(DC2Type:uuid)';
//        COMMENT ON COLUMN tracks.user_id IS '(DC2Type:uuid)';
//        COMMENT ON COLUMN tracks.proposal_date IS '(DC2Type:datetimetz_immutable)';
//        ALTER TABLE tracks ADD CONSTRAINT FK_246D2A2E3C947C0F FOREIGN KEY (poll_id) REFERENCES polls (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
//        ALTER TABLE tracks ADD CONSTRAINT FK_246D2A2E613FECDF FOREIGN KEY (session_id) REFERENCES sessions (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
//        ALTER TABLE tracks ADD CONSTRAINT FK_246D2A2EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        $tracks_table = $this->table('tracks', [ 'id' => false, 'primary_key' => [ 'id' ]]);
        $tracks_table
            ->addColumn('id', 'uuid', [ 'null' => false, 'comment' => 'Track unique identifier' ])
            ->addColumn('poll_id', 'uuid', [ 'null' => false, 'comment' => 'Poll unique identifier' ])
            ->addColumn('session_id', 'uuid', [ 'null' => false, 'comment' => 'Session unique identifier' ])
            ->addColumn('user_id', 'uuid', [ 'null' => true, 'comment' => 'User unique identifier' ])
            ->addTimestampsWithTimezone() //Add fields: created_at and updated_at
            ->addColumn('artist', 'string', [ 'limit' => 255, 'null' => true, 'comment' => 'Name of the artist' ])
            ->addColumn('name', 'string', [ 'limit' => 255, 'null' => false, 'comment' => 'Name of the track' ])
            ->addColumn('spotify_uri', 'string', [ 'limit' => 255, 'null' => true, 'comment' => 'Spotify track identifier, prepend "spotify:track:" for a valid spotify uri' ])
            ->addColumn('spotify_images', 'string', [ 'limit' => 255, 'null' => true, 'comment' => 'Json with all the images provided by Spotify' ])
            ->addColumn('youtube_uri', 'string', [ 'limit' => 255, 'null' => true, 'comment' => 'Youtube track identifier' ])
            ->addForeignKey('poll_id', 'polls', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'track_poll_id'
            ])
            ->addForeignKey('session_id', 'sessions', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'track_session_id'
            ])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'track_user_id'
            ])
            ->create();

//        CREATE TABLE votes (id UUID NOT NULL, poll_id UUID NOT NULL, track_id UUID NOT NULL, session_id UUID NOT NULL, user_id UUID DEFAULT NULL,
//          created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, rating SMALLINT NOT NULL, PRIMARY KEY(id));
//        CREATE INDEX IDX_518B7ACF3C947C0F ON votes (poll_id);
//        CREATE INDEX IDX_518B7ACF5ED23C43 ON votes (track_id);
//        CREATE INDEX IDX_518B7ACF613FECDF ON votes (session_id);
//        CREATE INDEX IDX_518B7ACFA76ED395 ON votes (user_id);
//        COMMENT ON COLUMN votes.id IS '(DC2Type:uuid)';
//        COMMENT ON COLUMN votes.poll_id IS '(DC2Type:uuid)';
//        COMMENT ON COLUMN votes.track_id IS '(DC2Type:uuid)';
//        COMMENT ON COLUMN votes.session_id IS '(DC2Type:uuid)';
//        COMMENT ON COLUMN votes.user_id IS '(DC2Type:uuid)';
//        COMMENT ON COLUMN votes.created_at IS '(DC2Type:datetimetz_immutable)';
//         ALTER TABLE votes ADD CONSTRAINT FK_518B7ACF3C947C0F FOREIGN KEY (poll_id) REFERENCES polls (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
//         ALTER TABLE votes ADD CONSTRAINT FK_518B7ACF5ED23C43 FOREIGN KEY (track_id) REFERENCES tracks (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
//         ALTER TABLE votes ADD CONSTRAINT FK_518B7ACF613FECDF FOREIGN KEY (session_id) REFERENCES sessions (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
//         ALTER TABLE votes ADD CONSTRAINT FK_518B7ACFA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        $votes_table = $this->table('votes', [ 'id' => false, 'primary_key' => [ 'id' ]]);
        $votes_table
            ->addColumn('id', 'uuid', [ 'null' => false, 'comment' => 'Vote unique identifier' ])
            ->addColumn('poll_id', 'uuid', [ 'null' => false, 'comment' => 'Poll unique identifier' ])
            ->addColumn('track_id', 'uuid', [ 'null' => true, 'comment' => 'Track unique identifier' ])
            ->addColumn('session_id', 'uuid', [ 'null' => false, 'comment' => 'Session unique identifier' ])
            ->addColumn('user_id', 'uuid', [ 'null' => true, 'comment' => 'User unique identifier' ])
            ->addTimestampsWithTimezone() //Add fields: created_at and updated_at
            ->addColumn('rate', 'smallinteger', [ 'default' => 1, 'null' => false, 'signed' => false, 'comment' => 'The points given to one track' ])
            ->addForeignKey('poll_id', 'polls', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'vote_poll_id'
            ])
            ->addForeignKey('track_id', 'tracks', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'vote_track_id'
            ])
            ->addForeignKey('session_id', 'sessions', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'vote_session_id'
            ])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE', 'update' => 'RESTRICT', 'constraint' => 'vote_user_id'
            ])
            ->create();
    }
}
