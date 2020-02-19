# Discoveryfy (API)

WIP - Phalcon API

Specs in: ./public/openapi.yaml (@ToDo: JsonLD schemas)


Useful commands
-------
In order to execute symfony commands:
```shell
docker-compose exec php bin/console
```

Create poll:
```shell
curl -X POST "https://localhost:8443/polls" -H  "accept: application/ld+json" -H  "Content-Type: application/json" -d "{}"`
```

Create track:
```shell
curl -X POST "http://localhost:8080/tracks" -H  "accept: application/ld+json" -H  "Content-Type: application/json" -d "{\"spotify_uri\": \"spotify:track:1ECc1EhfkRx08o8uIwYOxW\",\"youtube_uri\": \"t67NhxJhrUU\",\"artist\": \"Lágrimas de Sangre\",\"name\": \"Rojos y separatistas\",\"poll\": \"6a3a946c-c0f5-4a2a-9a1c-ab230c051206\"}"
```
```
{
"spotify_uri": "spotify:track:1ECc1EhfkRx08o8uIwYOxW",
"youtube_uri": "t67NhxJhrUU",
"artist": "Lágrimas de Sangre",
"name": "Rojos y separatistas",
"poll": "6a3a946c-c0f5-4a2a-9a1c-ab230c051206"
}
```

Create vote:
```shell
curl -X POST "https://localhost:8443/votes" -H  "accept: application/ld+json" -H  "Content-Type: application/json" -d "{  \"name\": \"lenin\",  \"poll\": \"6a3a946c-c0f5-4a2a-9a1c-ab230c051206\",  \"track\": \"29b44e2b-7f55-4ef5-b462-43bcaa8f02f9\"}"`
```
```
{
"name": "lenin",
"poll": "6a3a946c-c0f5-4a2a-9a1c-ab230c051206",
"track": "29b44e2b-7f55-4ef5-b462-43bcaa8f02f9"
}
```

Executes queries in postgres:
```shell
docker-compose exec db psql --dbname api --username api-platform --password
```

Add fixtures:
```shell
docker-compose exec php bin/console doctrine:fixtures:load -n
```

Generate new SSH Keys:
```shell
docker-compose exec php  mkdir -p config/jwt
docker-compose exec php  openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
docker-compose exec php  openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```
Edit `.env` file with the keys password.

Or all in one with this cmd from https://api-platform.com/docs/core/jwt/:
```shell
docker-compose exec php sh -c '
    set -e
    apk add openssl
    mkdir -p config/jwt
    jwt_passhrase=$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')
    echo "$jwt_passhrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    echo "$jwt_passhrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
    setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
    setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
'
```
In case first openssl command forces you to input password use following to get the private key decrypted (https://emirkarsiyakali.com/implementing-jwt-authentication-to-your-api-platform-application-885f014d3358)
```shell
$ openssl rsa -in config/jwt/private.pem -out config/jwt/private2.pem
$ mv config/jwt/private.pem config/jwt/private.pem-back
$ mv config/jwt/private2.pem config/jwt/private.pem
```
More info: https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#generate-the-ssh-keys

Roadmap
-------
* Organizations
* Autofinish polls with a scheduled agent
* Bug with customized endpoint:
https://github.com/symfony/symfony/issues/32395
https://github.com/api-platform/api-platform/issues/588
* Filters
https://api-platform.com/docs/core/filters
* Security
https://symfony.com/doc/current/security/expressions.html
https://symfony.com/doc/current/security/voters.html
https://www.thinkbean.com/drupal-development-blog/restrict-properties-api-platform-serialization-groups
https://api-platform.com/docs/core/serialization/
* Session
https://symfony.com/doc/current/components/http_foundation/sessions.html
https://stackoverflow.com/questions/15400065/how-to-add-a-custom-field-to-the-session-table/15634139
https://symfony.com/doc/current/doctrine/pdo_session_storage.html
https://stackoverflow.com/questions/59073943/session-based-authentication-in-api-platform
https://symfony.com/doc/current/security/json_login_setup.html
* Subresources operations
https://github.com/api-platform/core/issues/2435
https://github.com/api-platform/api-platform/issues/571
https://api-platform.com/docs/core/controllers/#creating-custom-operations-and-controllers
