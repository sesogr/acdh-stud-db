# Container setup
## Image Details on Docker Hub Registry

* [PHP on Apache](https://registry.hub.docker.com/_/php/)
* [MariaDB](https://registry.hub.docker.com/_/mariadb/)
* [Docker Grand Ambassador](https://registry.hub.docker.com/u/cpuguy83/docker-grand-ambassador/),
	see also: [blog article](http://www.tech-d.net/2014/08/28/docker-grand-ambassador/)

## _rksd-proxy-mariadb_

	docker run \
		--detach \
		--publish 13006:3306 \
		--volume /var/run/docker.sock:/var/run/docker.sock \
		--name rksd-proxy-mariadb \
		cpuguy83/docker-grand-ambassador \
			-name rksd-origin-mariadb

## _rksd-proxy-php-apache_

	docker run \
		--detach \
		--publish 13080:80 \
		--volume /var/run/docker.sock:/var/run/docker.sock \
		--name rksd-proxy-php-apache \
		cpuguy83/docker-grand-ambassador \
			-name rksd-origin-php-apache

## _rksd-origin-mariadb_

	docker run \
		--detach \
		--env MYSQL_ROOT_PASSWORD=hW3e4iNuFDqs \
		--env MYSQL_DATABASE=rksd \
		--env MYSQL_USER=rksd \
		--env MYSQL_PASSWORD=nJkyj2pOsfUi \
		--name rksd-origin-mariadb \
		mariadb

## _rksd-origin-php-apache_

	docker run \
		--detach \
		--link rksd-proxy-mariadb:mariadb \
		--volume "$HOME/projects/sednasoft/klugseder/rksd-2016/conf/php":/usr/local/etc/php/conf.d \
		--volume "$HOME/projects/sednasoft/klugseder/rksd-2016/web":/var/www/html \
		--name rksd-origin-php-apache \
		aschaffhirt/php-apache-xdebug

## Interactive command-line database client

	docker run --rm -it --link rksd-proxy-mariadb:db mariadb mysql -hdb -urksd -pnJkyj2pOsfUi -Drksd

## Redirectable command-line database client

	docker run --rm -i --link rksd-proxy-mariadb:db mariadb mysql -hdb -urksd -pnJkyj2pOsfUi -Drksd
