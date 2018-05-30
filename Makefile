all:

deb:
	dpkg-buildpackage -A -us -uc


dimage:
	docker build -t vitexsoftware/flexplorer .

drun:
	docker-compose run --rm default install
