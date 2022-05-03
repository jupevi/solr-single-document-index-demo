#!/usr/bin/env bash
script_dir="$(cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd)"
docker run --rm --entrypoint /opt/solr-8.5.2/entrypoint.sh -v "$script_dir"/solr/configsets:/opt/solr-8.5.2/server/solr/configsets -v "$script_dir"/solr/entrypoint.sh:/opt/solr-8.5.2/entrypoint.sh -p 8983:8983 solr:8.5.2
