deb:
	dpkg-buildpackage -A -us -uc


docker:
	docker build -t vitexsoftware/flexplorer .

install:
	docker-compose run --rm default install
