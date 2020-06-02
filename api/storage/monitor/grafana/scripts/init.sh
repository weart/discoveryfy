#!/usr/bin/env bash

set -o errexit
set -o nounset

source .env
source .env.local

echo "==> Prepare Configurations"
sed -e 's/%%INFLUXDB_ADMIN_USER%%/'${INFLUXDB_ADMIN_USER}'/g'         \
    -e 's/%%INFLUXDB_ADMIN_PASSWORD%%/'${INFLUXDB_ADMIN_PASSWORD}'/g' \
    -e 's/%%INFLUXDB_DATABASE%%/'${INFLUXDB_DATABASE}'/g'   \
    grafana/etc/provisioning/datasources/datasource.yaml.template     \
  > grafana/etc/provisioning/datasources/datasource.yaml
