#!/usr/bin/env bash

# Exit on any non-zero return codes.
# https://vaneyckt.io/posts/safer_bash_scripts_with_set_euxo_pipefail/
set -Eeo pipefail

SOLR_VERSION=8.5.2
SOLR_PRECREATE_CORES=catalog
SOLR_CONFIGSET=catalog

echo "------------------------------------"
echo "Creating or updating Solr cores"
echo "------------------------------------"

for core in $SOLR_PRECREATE_CORES; do
  if [[ -d "/var/solr/data/$core" ]]; then
    echo "Core $core exists, updating configuration"
    cp -frvuT "/opt/solr-$SOLR_VERSION/server/solr/configsets/$SOLR_CONFIGSET/" "/var/solr/data/$core/"
  else
    echo "Core $core doesn't exist, creating"
    precreate-core "$core" "/opt/solr-$SOLR_VERSION/server/solr/configsets/$SOLR_CONFIGSET"
  fi
done

echo "------------------------------------"
echo "Starting Solr"
echo "------------------------------------"

solr-foreground
