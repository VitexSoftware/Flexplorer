all:

deb:
	dpkg-buildpackage -A -us -uc

clean:
	rm -rf debian/flexplorer vendor/* composer.lock

dimage:
	docker build -t vitexsoftware/flexplorer .

drun:
	docker-compose run --rm default install
