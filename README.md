# Introduction

Demo repository for Solarium issue https://github.com/solariumphp/solarium/issues/992

# Instructions

It's assumed you have Docker available and `php` points to a PHP 7.4 executable.

- Execute `./run-solr-foreground.sh` to run a Solr 8.5.2 instance in a Docker container
  + Unfortunately, signals aren't properly passed to Solr, so Ctrl+C will not interrupt the container.
    You must find the container's ID using `docker ps` and kill the container using `docker kill <id>`.
- Execute `php composer.phar install` to install dependencies.
- Execute `php index.php` to index a document with a single nested child document into Solr and query
  it to show that it's been properly indexed.
