# General notes

Files matching `*.sql` and `*.sh` (and some others) in this directory will be executed (in order) to initialise the DB
in the container so long as it's empty. Also, statements like
`load data infile '/docker-entrypoint-initdb.d/example.csv' ...` work. Other files will be ignored.

More info can be found in the section “*Initializing the database contents*” of https://hub.docker.com/_/mariadb/.