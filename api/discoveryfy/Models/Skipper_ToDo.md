# Skipper: Changes To Be Done

### Already done in the models

Poll:
  * Change order
  * Add field description (string)
  * anon_can_add_track -> who_can_add_track (smallint)
  * Add field spotifyPlaylistPublic (bool)
  * Add field spotifyPlaylistCollaborative (bool)

User:
  * Add field Language (string)
  * Add field Theme (string)



ToDo:


Filters:
  * Organization:
    * name
    * public_membership
  * Membership:
    * roles
  * Poll:
    * name
    * end_date
    * anon_can_vote
    * anon_can_add_track
  * Track:
    * name?
    * artist?
  * Vote:
  * User:
    * username
    * email
  * Session:
    * name
    
Provider/Security:
  * Membership:
    * self
    * organization.public_membership
    * organization.member
  * Organization:
    * self.membership
    * public_visibility
  * Poll:
    * public_visibility
  * Session:
    * self
  * Track:
  * User:
    * self
    * enabled
    * public_visibility
    * public_email
  * Vote:
    * poll.public_votes
  
