all:

deb:
	dpkg-buildpackage -A -us -uc

clean:
	rm -rf debian/flexplorer vendor/* composer.lock

dimage:
	docker build -t vitexsoftware/flexplorer .

dtest:
	docker-compose run --rm default install
        
drun: dimage
	docker run  -dit --name flexplorer -p 2323:80 vitexsoftware/flexplorer
	nightly http://localhost:2323
