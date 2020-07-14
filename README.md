# Discoveryfy

Tiny WebApp for Share & Rate Songs in Spotify.

Create collaborative playlists in Spotify with your friends and colleagues and rate the songs.

More information in the base README and in api/README.md.

Docs used
-------
* https://junghanns.it/posts/cqrs-and-eventsourcing-with-api-platform-ii/
* https://www.nielsvandermolen.com/symfony-4-api-platform-application/

* https://medium.com/@rebolon/how-to-bind-your-favorite-js-framework-with-symfony-4-8c9ba86e2b8d
* https://www.myalerts.org/demo/quasar#/
* https://github.com/Rebolon/php-sf-flex-webpack-encore-vuejs/blob/bff3370b3f98e87a008c1a5cf4a545ee8146433e/assets/js/form-quasar-vuejs/app.js
* https://github.com/Rebolon/php-sf-flex-webpack-encore-vuejs/blob/bff3370b3f98e87a008c1a5cf4a545ee8146433e/assets/js/form-quasar-vuejs/components/Book.vue

Useful documentation:
-------
* https://api-platform.com/docs/core/events/
* https://symfony.com/doc/current/reference/events.html#kernel-events
* https://quasar.dev/vue-components/

Pending docs:
-------
* https://vue-apollo.netlify.com/
* https://developer.okta.com/blog/2018/06/14/php-crud-app-symfony-vue
* https://github.com/hasura/graphql-engine/tree/master/community/sample-apps/quasar-framework-vue-graphql
* https://jasonwatmore.com/post/2018/07/06/vue-vuex-jwt-authentication-tutorial-example


Useful commands
-------
Build &  start all containers:
```bash
docker-compose up --force-recreate -d
```

Check machine ip:
```bash
docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' spotifydailylist_client-quasar_1
```

Upgrade everything:
```bash
./update-deps.sh
```

Configure ngrok
-------
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
    addr: 82
```
And launch the daemon:
```bash
/opt/ngrok start --all
```


Created using Api Platform
-------
<h1 align="center"><a href="https://api-platform.com"><img src="https://api-platform.com/logo-250x250.png" alt="API Platform"></a></h1>

Created by [Kévin Dunglas](https://dunglas.fr). Commercial support available at [Les-Tilleuls.coop](https://les-tilleuls.coop).
