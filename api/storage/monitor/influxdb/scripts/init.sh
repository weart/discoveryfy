#!/usr/bin/env bash

set -o errexit
set -o nounset

source .env
source .env.local

echo "==> Initial Database"
docker exec -it influxdb                 \
  influx                                 \
    -username ${INFLUXDB_ADMIN_USER}     \
    -password ${INFLUXDB_ADMIN_PASSWORD} \
    -execute 'CREATE DATABASE 'discoveryfy';'
