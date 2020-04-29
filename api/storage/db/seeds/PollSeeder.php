<?php

//use Phalcon\Security;
use Phalcon\Security\Random;
use Phinx\Seed\AbstractSeed;
//use RuntimeException;

class PollSeeder extends AbstractSeed
{
    private $user_test_id = 'f756ffbe-6143-46f0-b914-f898b1f05f84';
    private $org_test_id = '52bff0b0-4d4f-4df7-b55a-d55dbac5d033';

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
        $poll_properties = [
            'spotify_playlist_public' => false,
            'spotify_playlist_collaborative' => false,
            'public_visibility' => true,
        ];
        $poll_properties_public = [
            'spotify_playlist_collaborative' => true,
            'public_votes' => true,
            'anon_can_vote' => true,
            'who_can_add_track' => 'ANYONE',
            'anon_votes_max_rating' => 5,
            'user_votes_max_rating' => 10,
            'multiple_anon_tracks' => true,
            'multiple_user_tracks' => true,
        ];
        $poll_properties_members = [
            'public_votes' => false,
            'anon_can_vote' => false,
            'who_can_add_track' => 'MEMBERS',
            'anon_votes_max_rating' => 0,
            'user_votes_max_rating' => 10,
            'multiple_anon_tracks' => false,
            'multiple_user_tracks' => true,
        ];
        $poll_properties_no_editable = [
            'spotify_playlist_collaborative' => false,
            'who_can_add_track' => 'OWNERS'
        ];
        $poll_properties_weekly = [
            'restart_date' => '0 0 * * 1' //"At 00:00 on Monday"
        ];
        $poll_properties_monthly = [
            'restart_date' => '0 0 1 * *' //"At 00:00 on day-of-month 1"
        ];
//        $poll_properties_finished = [
//            'end_date' => setEndDate is not a function (is markAsEnded)
//        ];

        $polls_schema = [
            'poll_public_weekly' => array_merge([
                    'name' => 'Weekly Public Playlist',
                    'description' => 'Public playlist, restarted weekly',
                ],
                $poll_properties, $poll_properties_public, $poll_properties_weekly
            ),
            'poll_members_weekly' =>array_merge([
                    'name' => 'Weekly Members Playlist',
                    'description' => 'Registered users playlist, restarted weekly',
                ],
                $poll_properties, $poll_properties_members, $poll_properties_weekly
            ),
            'poll_members_monthly' => array_merge([
                    'name' => 'Monthly Members Playlist',
                    'description' => 'Registered users playlist, restarted monthly',
                ],
                $poll_properties, $poll_properties_members, $poll_properties_monthly
            ),
            'poll_public_monthly_no_editable' => array_merge([
                    'name' => 'Best Of "Weekly Public Playlist" Monthly',
                    'description' => 'Playlist with the best song of each "Weekly Public Playlist" of this month',
                ],
                $poll_properties, $poll_properties_public, $poll_properties_no_editable, $poll_properties_monthly
            ),
            'poll_members_monthly_no_editable' => array_merge([
                    'name' => 'Best Of "Weekly Members Playlist" Monthly',
                    'description' => 'Playlist with best song of each "Weekly Members Playlist" of this month',
                ],
                $poll_properties, $poll_properties_members, $poll_properties_no_editable, $poll_properties_monthly
            ),
        ];
        $tracks_schema = [
            [
                'artist' => 'Lágrimas de Sangre',
                'name' => 'Rojos y separatistas',
                'youtube_uri' => 't67NhxJhrUU',
                'spotify_uri' => '1ECc1EhfkRx08o8uIwYOxW',
            ],[
                'artist' => 'Toti Soler',
                'name' => 'Em Dius Que El Nostre Amor',
                'youtube_uri' => 'rd55dcyjCSY',
                'spotify_uri' => '5o31tm7aa5PdsThhw36it9',
            ],[
                'artist' => 'Queen',
                'name' => 'I Was Born To Love You - 2011 Remaster',
                'spotify_uri' => '7DtdhIJlSSOaAFNk4JdXCb',
            ],[
                'artist' => 'Queen',
                'name' => 'Hammer To Fall - 2011 Remaster',
                'spotify_uri' => '61lj5cHhOifNzSMXuWg54Z',
            ],[
                'artist' => 'Giacomo Puccini',
                'name' => 'Turandot / Act 3: "Nessun dorma!',
                'spotify_uri' => '74WjYdm3Lvbwnds4thYPUU',
            ],[
                'artist' => 'R. Kelly',
                'name' => 'I Believe I Can Fly',
                'spotify_uri' => '2RzJwBCXsS1VnjDm2jKKAa',
            ],[
                'artist' => 'Zoufris Maracas',
                'name' => 'Cocagne',
                'spotify_uri' => '7FzdSwzenyUeiO6Dld2Y3v',
            ],[
                'artist' => 'Rozalén',
                'name' => 'La Puerta Violeta',
                'spotify_uri' => '60kCg3tKhxb61QbBOFVzXh',
            ],[
                'artist' => 'The Gramophone Allstars',
                'name' => 'I Wish I Knew How It Would Feel To Be Free',
                'spotify_uri' => '00POrfLzrW7hEtyAi1IeeM',
            ],[
                'artist' => 'Tokyo Ska Paradise Orchestra',
                'name' => '水琴窟-SUIKINKUTSU-',
                'spotify_uri' => '6fOGYN4YLmaA5Yr6GjcDYV',
            ],[
                'artist' => 'Michel Camilo',
                'name' => 'Tropical Jam - Live',
                'spotify_uri' => '4VJa1MNSiS5M1SkpdCNgxN',
            ],[
                'artist' => 'Portugal. The Man',
                'name' => 'Feel It Still',
                'spotify_uri' => '6QgjcU0zLnzq5OrUoSZ3OK',
            ],[
                'artist' => 'Inadaptats',
                'name' => 'Orgull De Classe',
                'spotify_uri' => '7dNYq25bervddvMWNe7Fqf',
            ],[
                'artist' => 'Woodkid',
                'name' => 'Run Boy Run',
                'spotify_uri' => '0boS4e6uXwp3zAvz1mLxZS',
            ],[
                'artist' => 'Chucho Valdés',
                'name' => 'Caridad Amaro',
                'spotify_uri' => '0qgfZBiE1XEGZuSESZTrIW',
            ],[
                'artist' => 'The Skatalites',
                'name' => 'Requiem for Rico',
                'spotify_uri' => '7aSRHl639kQIa81lTDG7BD',
            ]
        ];
        $num_tracks = count($tracks_schema);
        $num_polls_can_add_track = 0;

        // Add ids & Count editable polls
        foreach ($polls_schema as $poll_schema_key => &$poll_schema) {
            $poll_schema['id'] = $this->getRandomService()->uuid();
            $poll_schema['organization_id'] = $this->org_test_id;
            if ($poll_schema['who_can_add_track'] !== 'OWNERS') {
                $num_polls_can_add_track++;
            }
        }
        unset($poll_schema);

//        $this->table('sessions')->truncate();
        $sessions = $this->createAnonUsers();
//        $this->createSession('Test Voter', $this->user_test_id);
        $tracks = [];
        $votes = [];

        foreach ($polls_schema as $poll_schema_key => $poll_schema) {
            // Add tracks
            $poll_sessions = $sessions;
            $poll_tracks = [];
            if ($poll_schema['who_can_add_track'] !== 'OWNERS') {
                $add_more_tracks = true;
                $user_can_add_more_tracks = true;
                while ($add_more_tracks && count($tracks_schema) > 0) {
                    $track_info = $this->array_pop_rand($tracks_schema);

                    if ($poll_schema['who_can_add_track'] === 'ANYONE') { //Everyone can add tracks
                        if ($user_can_add_more_tracks && rand(0, 1) === 0) { //50% member user, only one if multiple
                            $session_id = $this->user_test_id;
                            $user_id = $this->user_test_id;
                            $user_can_add_more_tracks = $poll_schema['multiple_user_tracks'];
                        } else { //50% anon user
                            $user_id = null;
                            if ($poll_schema['multiple_user_tracks']) {
                                $session_id = $poll_sessions[rand(0, (count($poll_sessions)-1))];
                            } else {
                                $session_id = $this->array_pop_rand($poll_sessions);
                            }
                        }
                    } else { //Only members can add tracks
                        $session_id = $this->user_test_id;
                        $user_id = $this->user_test_id;
                        $add_more_tracks = $poll_schema['multiple_user_tracks'];
                    }

                    //Add track to poll
                    $datetime = new \Datetime('now', new \DateTimeZone('UTC'));
                    $track = [
                        'id'                => $this->getRandomService()->uuid(),
                        'poll_id'           => $poll_schema['id'],
                        'session_id'        => $session_id,
                        'user_id'           => $user_id,
                        'created_at'        => $datetime->format('Y-m-d H:i:s'),
                        'updated_at'        => $datetime->format('Y-m-d H:i:s'),
                        'artist'            => $track_info['artist'],
                        'name'              => $track_info['name'],
                        'spotify_uri'       => $track_info['spotify_uri'],
                        'spotify_images'    => null,
                        'youtube_uri'       => $track_info['youtube_uri'] ?? null,
                    ];
                    $tracks[] = $track;
                    $poll_tracks[] = $track;

                    //Distribute the tracks along the polls
                    $max_num_tracks = (int) ($num_tracks / $num_polls_can_add_track);
                    if (count($poll_tracks) > $max_num_tracks) {
                        $add_more_tracks = false;
                    }
                }
            }
            unset($track, $poll_sessions);

            // Add votes
            // If it's possible, user vote
            $max_remaining_rating = $poll_schema['user_votes_max_rating'];
            $tmp_poll_tracks = $poll_tracks;
            while ($max_remaining_rating > 0 && count($tmp_poll_tracks) > 0) {
//                $track = array_pop($tmp_poll_tracks);
                $track = $this->array_pop_rand($tmp_poll_tracks);
                $rating = rand(0, $max_remaining_rating);
                $max_remaining_rating -= $rating;
                $datetime = new \Datetime('now', new \DateTimeZone('UTC'));
                $vote = [
                    'id'            => $this->getRandomService()->uuid(),
                    'poll_id'       => $poll_schema['id'],
                    'track_id'      => $track['id'],
                    'session_id'    => $session_id,
                    'user_id'       => $user_id,
                    'created_at'    => $datetime->format('Y-m-d H:i:s'),
                    'updated_at'    => $datetime->format('Y-m-d H:i:s'),
                    'rate'          => $rating
                ];
                $votes[] = $vote;
            }
            unset($track, $vote, $tmp_poll_tracks);

            //If it's possible, anon votes
            $tmp_poll_tracks = $poll_tracks;
            if ($poll_schema['anon_can_vote']) {
                $poll_sessions = $sessions;
                foreach ($poll_sessions as $poll_session) {
                    $max_remaining_rating = $poll_schema['user_votes_max_rating'];
                    while ($max_remaining_rating > 0 && count($tmp_poll_tracks) > 0) {
//                        $track = array_pop($tmp_poll_tracks);
                        $track = $this->array_pop_rand($tmp_poll_tracks);
                        $rating = rand(0, $max_remaining_rating);
                        $max_remaining_rating -= $rating;
                        $datetime = new \Datetime('now', new \DateTimeZone('UTC'));
                        $vote = [
                            'id'            => $this->getRandomService()->uuid(),
                            'poll_id'       => $poll_schema['id'],
                            'track_id'      => $track['id'],
                            'session_id'    => $poll_session,
                            'user_id'       => null,
                            'created_at'    => $datetime->format('Y-m-d H:i:s'),
                            'updated_at'    => $datetime->format('Y-m-d H:i:s'),
                            'rate'          => $rating
                        ];
                        $votes[] = $vote;
                    }
                }
                unset($track, $vote, $tmp_poll_tracks, $poll_sessions);
            }

        }

        $this->table('polls')->insert($polls_schema)->saveData();
        $this->table('tracks')->insert($tracks)->saveData();
        $this->table('votes')->insert($votes)->saveData();
    }

    private function array_pop_rand(&$array)
    {
        $array = array_values($array);
        $key = rand(0, (count($array)-1));
        $val = $array[$key];
        unset($array[$key]);
//        $array = array_values($array);
        return $val;
    }

    private function createAnonUsers($num_users = 10): array
    {
        $sessions = [];
        foreach (range(0, $num_users) as $num_user) {
            $sessions[] = $this->createSession('Test Anon Voter '.$num_user);
        }
        return $sessions;
    }

    private function createSession(string $name, ?string $uuid = null): string
    {
        $datetime = new \Datetime('now', new \DateTimeZone('UTC'));
        $uuid = $uuid ?? $this->getRandomService()->uuid();
        $this->table('sessions')->insert([
            'id'                    => $uuid,
            'created_at'            => $datetime->format('Y-m-d H:i:s'),
            'updated_at'            => $datetime->format('Y-m-d H:i:s'),
            'name'                  => $name,
        ])->saveData();
        return $uuid;
    }

    protected function getRandomService(): Random
    {
        return (new Random());
    }

//    private function getNumTracksByPoll($tracks, $poll_id): int
//    {
//        $counter = array_count_values($tracks);
//        return $counter[$poll_id] ?? 0;
//    }
}
