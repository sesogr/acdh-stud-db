# Container setup
## Image Details on Docker Hub Registry

* [PHP on Apache](https://registry.hub.docker.com/_/php/)
* [MariaDB](https://registry.hub.docker.com/_/mariadb/)
* [Docker Compose](https://docs.docker.com/compose/compose-file/compose-file-v3/)
* obsolete [Docker Grand Ambassador](https://registry.hub.docker.com/u/cpuguy83/docker-grand-ambassador/), see also: [blog article](http://www.tech-d.net/2014/08/28/docker-grand-ambassador/)


## Interactive command-line database client

	docker run --rm -it --network=rksd-2016_default mariadb mariadb -hmariadb -urksd -pnJkyj2pOsfUi -Drksd

## Redirectable command-line database client

	cat example.sql | docker run --rm -i --network=rksd-2016_default mariadb mariadb -hmariadb -urksd -pnJkyj2pOsfUi -Drksd
