# Discoveryfy (API) - WIP

### API documentation

OpenAPI Specification located in `./public/docs` folder or in `/docs` when server is running.

### Installation

This API uses Phalcon (PHP), SQL & Redis as stack framework. Docker compose created for an easy setup. In the root folder run:
```
docker-compose up -d
```
More information in the root README.md.

#### Config

All config files are inside `./config` folder, but the values are located in the files `.env` and `.env.local`.

#### Private key generation

Generate new SSH Keys:
```shell
docker-compose exec api mkdir -p config/jwt
docker-compose exec api openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
docker-compose exec api openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```
Edit `.env` file with the keys password.

Or all in one with this cmd from https://api-platform.com/docs/core/jwt/:
```shell
docker-compose exec api sh -c '
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

### Roadmap
* Define Spotify endpoints for get information
* Scheduled agent for autofinish polls & grab information from SpotifyService
* Finish api test -> api/tests/api/ToDo.md
* Sysadmin: Define domain, deploy (Ansible?) and define backup strategy.
* CORS - Uncomment middleware in RouterProvider
* [Lost password](https://github.com/phalcon/vokuro/blob/4.0.x/src/Models/ResetPasswords.php) & Logout
* Test against schema: https://github.com/aWuttig/codeception-api-validator / https://github.com/mlambley/swagception
* JSON-LD Schema. First define the specs in OpenApi file, after that write the code
    * https://ca.wikipedia.org/wiki/JSON-LD
* Pull request to the main phalcon/json-api project:
    * https://github.com/PhilippBaschke/acf-pro-installer/pull/35/files

### Based on the following projects:
* https://github.com/phalcon/rest-api
* https://github.com/phalcon/vokuro
* https://github.com/phalcon/invo
* https://github.com/krazzer/kikcms

### Useful commands

Phalcon tasks:
```shell
docker-compose exec api ./runCli
```

Run tests:
```shell
docker-compose exec api ./runTests
```

Enter in container shell:
```shell
docker exec -ti db /bin/bash
```

Executes queries in sql (modify N with values in .env files):
```shell
docker-compose exec db mysql -D N -u N -p
docker-compose exec db psql --dbname N --username N --password
```

Login in the API (modify N with the values in .env files):
```shell
# Get CSRF Token
curl -X GET  -H "Content-Type: application/json" http://localhost/login -v
# Login as anon
curl -X POST -H "Content-Type: application/json" -H "X-CSRF-TOKEN: N" http://localhost/login -v
# Login as user
curl -X POST -H "Content-Type: application/json" -H "X-CSRF-TOKEN: N" --data '{"username":"N","password":"N"}' http://localhost/login -v
# Check auth
curl -X GET  -H "Content-Type: application/json" -H "Authorization: Bearer N" http://localhost/polls -v
```

Create poll:
```shell
# Response as a plain json object
curl -X POST -H "Content-Type: application/json" -H "Authorization: Bearer N" -d '{}' http://localhost/polls -v
# Response as JSON:API
curl -X POST -H "Content-Type: application/vnd.api+json" -H "Authorization: Bearer N" -d '{}' http://localhost/polls -v
```

Create track:
```shell
curl -X POST -H "Content-Type: application/json" -H "Authorization: Bearer N" -d "{\"spotify_uri\": \"spotify:track:1ECc1EhfkRx08o8uIwYOxW\",\"youtube_uri\": \"t67NhxJhrUU\",\"artist\": \"Lágrimas de Sangre\",\"name\": \"Rojos y separatistas\",\"poll\": \"6a3a946c-c0f5-4a2a-9a1c-ab230c051206\"}" http://localhost/tracks -v 
```
Track object:
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
curl -X POST -H "Content-Type: application/json" -H "Authorization: Bearer N" -d "{\"name\": \"lenin\",  \"poll\": \"6a3a946c-c0f5-4a2a-9a1c-ab230c051206\",  \"track\": \"29b44e2b-7f55-4ef5-b462-43bcaa8f02f9\"}" http://localhost/votes -v 
```
Vote object:
```
{
"name": "lenin",
"poll": "6a3a946c-c0f5-4a2a-9a1c-ab230c051206",
"track": "29b44e2b-7f55-4ef5-b462-43bcaa8f02f9"
}
```
