# Discoveryfy

[![Gitpod Ready-to-Code](https://img.shields.io/badge/Gitpod-Ready--to--Code-blue?logo=gitpod)](https://gitpod.io/#https://github.com/weart/discoveryfy)
[![JWT Compatible](http://jwt.io/img/badge-compatible.svg)](https://jwt.io/)

Tiny WebApp for Share & Rate Songs in Spotify.

Create collaborative playlists in Spotify with your friends and colleagues and rate the songs.

More information in api/README.md & client/README.md.


## Technology stack:

Api / Backend:
  * OpenApi specs available at ./api/public/openapi.yaml
  * Phalcon: PHP framework for generate a [JSON:API](https://jsonapi.org/) compliant API, [OpenAPI Specification](/docs): [json definition](/openapi.json) & [yaml definition](/openapi.yaml).
  * [jwilsson/spotify-web-api-php](https://github.com/jwilsson/spotify-web-api-php): PHP wrapper for Spotify's Web API. 
  * JWT: Authentication engine
  * MySQL: Database engine for persist data
  * Redis: Database engine for cache
  * Other possibilites:
    * Django, Laravel, Flask
    * Instead of JSON:API, use [any other hypermedia spec](https://www.nginx.com/blog/building-your-api-for-longevity-best-practices/), [2](https://sookocheff.com/post/api/on-choosing-a-hypermedia-format/):
		* Hydra, HAL, [CPHL](https://github.com/mikestowe/CPHL)...

Client / Frontend:
  * Quasar? Ionic? Material UI? Flutter?
  * Gravatar
  * [JSON:API vuex adapter?](https://mrichar1.github.io/jsonapi-vuex/)

DevOps:
  * Docker, and GitPod
  * Kibana?
  * https://github.com/jesseduffield/lazydocker

## Requirements
* PHP 7.2 or later.
* Phalcon 4.0
* PHP [cURL extension](http://php.net/manual/en/book.curl.php) (Usually included with PHP).

## Installation

Install [docker](https://docs.docker.com/engine/install/) & [docker-compose](https://docs.docker.com/compose/install/).

Create your own env file.
```bash
cp .env .env.local
```
And modify `.env.local` with your values.

Build &  start all containers:
```bash
docker-compose up -d
docker-compose up --force-recreate -d
docker-compose up --force-recreate --build -d
```

Execute migrations and seeding. (Commands below)

<!--
### Configure ngrok

Oauth providers require a public URL for the callback, ngrok expose the local web server into a public URL.

Create the file ~/.ngrok2/ngrok.yml with the follow content:
```yaml
authtoken: CopySecretHere
remote_management: null
tunnels:
  api:
    proto: http
    addr: 8080
  client-quasar:
    proto: http
    addr: 80
```
And launch the daemon:
```bash
/opt/ngrok start --all
```
-->

## Useful commands

Check machine ip:
```bash
docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' api
docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' db
docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' cache
```

Check docker installed php version:
```bash
docker run --rm -it api php -v
```

Execute phalcon or composer commands:
```shell
docker-compose exec api phalcon
docker-compose exec api composer
```

Execute migration:
```shell
docker-compose exec api vendor/bin/phinx migrate -e development
```

Execute seeding:
```shell
docker-compose exec api vendor/bin/phinx seed:run
```

Enter command in database:
```shell
docker exec -ti db /bin/bash
mysql -u leninux -p discoveryfydb
```

## Backers

Discover awesome collectives to support in Open Collective:
* [Phalcon](https://opencollective.com/phalcon#backer)
* [Women Who Code](https://opencollective.com/wwcode)
