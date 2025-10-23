Flexplorer
==========

![Flexplorer Logo](flexplorer-logo.png?raw=true "Project Logo")

## 🔍 Developer Console for ABRA Flexi API

**Flexplorer** is an alternative web interface for the **ABRA Flexi** ERP system (formerly FlexiBee), designed primarily for **developers and integrators**. It allows you to explore, test, and manipulate data via REST API without writing code.

Built using the [PHP AbraFlexi](https://github.com/Spoje-NET/php-abraflexi) library

### ⚡ Main Features

#### 📊 Data Management
  * **Evidence browsing** - display all available evidences across all companies
  * **DataGrids** - dynamic tables with filtering, sorting, and pagination
  * **Record editor** - create, update, and delete data
  * **Evidence structure display** - metadata, fields, relations
  * **Format support** - JSON, XML, and CSV
  * **External IDs** - manage external record identifiers
  * **Labels** - modify label assignments to evidences
  * **Permissions** - view roles and access rights

#### 🔧 API Testing & Debugging
  * **Query Builder** - send direct requests to the server
  * **Response Viewer** - display server responses with syntax highlighting
  * **Parallel View** - JSON/XML result alongside AbraFlexi GUI
  * **Filtering** - advanced filters by ID, external ID, and other parameters

#### 🪝 WebHooks & ChangesAPI
  * **WebHook Manager** - configure and manage webhooks
  * **ChangesAPI Monitor** - view received change data
  * **WebHook Tester** - test webhook script responses
  * **Change Data Replay** - resend change data to webhooks

#### 🎨 Custom Buttons
  * **Button Designer** - create custom buttons in AbraFlexi GUI
  * **Integration Links** - connect AbraFlexi with FlexPlorer
  * **Action Configuration** - define button actions

#### 🏢 Company Management
  * **Create and delete companies**
  * **Company reset** - delete and recreate
  * **Cloning** - create company copies
  * **Backup & Restore** - save and load backups
  * **Accounting periods** - batch create and cancel

#### 📄 Documents and Printing
  * **PDF previews** - view print reports
  * **Print reports** - access PDFs for records and entire evidences
  * **Document preview** - preview edited documents
  

[![Source Code](http://img.shields.io/badge/source-VitexSoftware/Flexplorer-blue.svg?style=flat-square)](https://github.com/VitexSoftware/Flexplorer)
[![Latest Version](https://img.shields.io/github/release/VitexSoftware/Flexplorer.svg?style=flat-square)](https://github.com/VitexSoftware/Flexplorer/releases)
[![Software License](https://img.shields.io/badge/license-GNU-brightgreen.svg?style=flat-square)](https://github.com/VitexSoftware/Flexplorer/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/VitexSoftware/Flexplorer/master.svg?style=flat-square)](https://travis-ci.org/VitexSoftware/Flexplorer)
[![Coverage Status](https://img.shields.io/coveralls/VitexSoftware/Flexplorer/master.svg?style=flat-square)](https://coveralls.io/r/VitexSoftware/Flexplorer?branch=master)

Try it out: 

  * Stable version: http://abraflexi-dev.spoje.net/ (always working)
  * Development version: https://vitexsoftware.cz/flexplorer/ (latest features)
Installation
-----------

For Debian or Ubuntu, please use the [repository](http://vitexsoftware.cz/repos.php):

```shell
sudo apt install lsb-release wget
echo "deb http://repo.vitexsoftware.cz $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/vitexsoftware.list
sudo wget -O /etc/apt/trusted.gpg.d/vitexsoftware.gpg http://repo.vitexsoftware.cz/keyring.gpg
sudo apt update
sudo apt install flexplorer
```	                

During installation, dialogs will prompt for the default server, username, and password.
These values will be written to the application configuration file and offered as defaults.

![Debian Configure](screenshots/flexplorer-debian-configure.png?raw=true "Debian Configuration")

After package installation and web server reload, the application will be installed in the 
/usr/share/flexplorer directory and accessible at http://localhost/flexplorer/ 
(configured in /etc/apache2/conf-enabled/flexplorer.conf).
If the avahi daemon is installed, it will advertise the application as a service.
The application can then be launched from the program menu in the "programming" section.


Authentication
--------------

Unauthenticated users are redirected to login.php with a login dialog. Here you can enter credentials or click on links in the left-side tabs.
New tabs can be added either by selecting the switch below the login dialog or by adding a JSON file to the /etc/abraflexi/ directory.
The format of these files must be compatible with https://github.com/VitexSoftware/abraflexi-client-config, meaning:

```json
{
    "ABRAFLEXI_URL": "https:\/\/demo.abraflexi.eu:5434",
    "ABRAFLEXI_LOGIN": "winstrom",
    "ABRAFLEXI_PASSWORD": "winstrom",
    "ABRAFLEXI_COMPANY": "demo"
}
```

It is now also possible to log in directly to the application using the server URL and auth token. An example URL looks like this:

evidence.php?serveruri=https%3A%2F%2Fdemo.abraflexi.eu%3A5434&



Updates
-------

If you have the package installed from the repository, updates work as is customary in Debian:

![Debian Upgrade](screenshots/flexplorer-debian-upgrade.png?raw=true "Package Upgrade")


Vagrant
-------
A [Box](https://atlas.hashicorp.com/vitexsoftware/boxes/flexplorer) for Vagrant is also available. After running the command:

    vagrant init vitexsoftware/flexplorer; vagrant up --provider virtualbox

you will be able to view the current development version at [http://localhost:8080/src/]
and the version from the latest Debian package at [http://localhost:8080/flexplorer/]


Docker
------

A Docker image is also available. The following command makes FlexPlorer accessible at: [localhost:2323](http://0.0.0.0:2323/)

    docker run  -dit --name flexplorer -p 2323:80 vitexsoftware/flexplorer

```    
vitex@docker:~$ docker run  -dit --name flexplorer -p 2323:80 vitexsoftware/flexplorer
Unable to find image 'vitexsoftware/flexplorer:latest' locally
latest: Pulling from vitexsoftware/flexplorer
cc1a78bfd46b: Pull complete 
1cd0b77f3d1d: Pull complete 
9b851b09757c: Pull complete 
9b36fad49c61: Pull complete 
d0e15216409e: Pull complete 
da8507a1fa91: Pull complete 
1285ef6f4076: Pull complete 
07c17144f477: Pull complete 
058b8f440dad: Pull complete 
507722a10e0a: Pull complete 
f3440e09e483: Pull complete 
967168855bae: Pull complete 
da8a7cb827b5: Pull complete 
Digest: sha256:38ed8bd94aaf2e57877c8b207cd55bb486d09178dacbd0b4def87090cae6170b
Status: Downloaded newer image for vitexsoftware/flexplorer:latest
396261e16a3adb66faf8f63a3f518b3c10331cc9c0f575c73cd86df3899b8f87
```





Configuration
-------------

The configuration file config.php is located in the src/includes directory. The default configuration looks like this:

    define('LOG_NAME', 'Flexplorer'); //Log identifier
    define('LOG_TYPE', 'syslog'); //Possible values: memory, syslog, file

    /*
     * Default message sender
     */
    define('EMAIL_FROM', 'flexplorer@localhost');

    /*
     * AbraFlexi API URL
     */
    define('DEFAULT_ABRAFLEXI_URL', 'https://demo.flexibee.eu');
    /*
     * AbraFlexi API user
     */
    define('DEFAULT_ABRAFLEXI_LOGIN', 'winstrom');
    /*
     * AbraFlexi API password
     */

    define('DEFAULT_ABRAFLEXI_PASSWORD', 'winstrom');
    /*
     * Company in AbraFlexi
     */

    define('DEFAULT_ABRAFLEXI_COMPANY', 'demo');


Acknowledgments
---------------

The creation of this tool would not have been possible without the kind support of [Spoje.Net](http://www.spoje.net).

At Spoje.Net, you can order commercial support for integrating
the [PHP AbraFlexi](https://github.com/Spoje-NET/FlexiPeeHP) library into your projects.

![Spoje.Net](spoje-net_logo.gif?raw=true "Spoje.Net")

[Statistiky Projektu na Wakatime](https://wakatime.com/@5abba9ca-813e-43ac-9b5f-b1cfdf3dc1c7/projects/wvloiziluw)

Application Screenshots
======================

Login uses the application user's name and password with REST API permissions. The tabs on the left side are loaded from configuration files in the /etc/abraflexi/ directory.

![Mobile Login](screenshots/flexplorer-login.png?raw=true "Login Screenshot")

FlexPlorer displays request responses:

![Server Response](screenshots/flexplorer-response_serveru.png?raw=true "Response Screenshot")

You can select any AbraFlexi evidence and list its contents:

![Evidence Listing](screenshots/flexplorer-vypis_evidence.png?raw=true "Evidence Listing Screenshot")

Data can be edited and saved if the logged-in user has the appropriate permissions:

![Evidence Editor](screenshots/flexplorer-editor-evidence.png "Evidence Editor Screenshot")

You can easily perform queries on an open evidence:

![JSON Editor](screenshots/flexplorer-json-editor.png "JSON Editor Screenshot")

Deleting a record from an evidence must be confirmed:

![Confirmation Before Deletion](screenshots/flexplorer-potvrzeni-pred-smazanim.png "Overview and Confirmation Before Record Deletion")

When testing WebHooks, this tool helps by composing a ChangesAPI record mock and sending it to the selected WebHook:

![WebHook Request](screenshots/flexplorer-webhook-request.png "WebHook Request Screenshot")

You can also use change data received on the FlexPlorer webhook and choose which webhook of the tested application to send it to:

![Change Data Reuse](screenshots/flexplorer-reuse-change-data.png "Received Change Data Screenshot")

The correct response is empty. Although it works, AbraFlexi would not like this script. Here we see what it returns:

![WebHook Response](screenshots/flexplorer-webhook-response.png "WebHook Response Screenshot")

A search field is available for searching evidences:

![WebHook Response](screenshots/flexplorer-hinter_evidence.png "Evidence Autocomplete")

After pressing enter, more detailed results are displayed:

![WebHook Response](screenshots/flexplorer-nalezene_evidence.png "Found Evidences")

It is also possible to search in column names of individual evidences and their descriptions:

![Columns Search](screenshots/flexplorer-nalezene_sloupce.png "Found Columns")

Request body can be loaded from a file:

![File Upload](screenshots/flexplorer-xml-file-upload.png "File Upload")

Request response can be in supported formats (XML here):

![XML Response](screenshots/flexplorer-xml-response.png "XML Response")

Company overview:

![Company overview](screenshots/flexplorer-company-page.png "Company Page")

