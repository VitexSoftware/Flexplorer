Flexplorer
==========

![Flexplorer Logo](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/src/images/flexplorer-logo.png "Project Logo")

Vývojářský nástroj pro FlexiBee API. Napsaný s využitím knihovny [FlexiPeeHP](https://github.com/Spoje-NET/FlexiPeeHP)
Umožňuje:

  * Pracovat s formáty JSON,XML a CSV
  * zobrazovat obsah všech dostupných evidencí ve všech firmách
  * zobrazovat strukturu evidence
  * odesílat přímé požadavky na server a zobrazovat výsledky
  * Nastavovat ChangesAPI a přidávat WebHooks
  * Zobrazovat data přijatá WebHookem
  * Test odpovědi WebHook skriptu zpracovávajícího změny z FlexiBee
  * Hromadně zakládat a rušit účetní období
  * Rozlišit evidnece které jsou z důvodu licence nedostupné
  * Zobrazovat vedle json výsledku požadavku i stránku z FlexiBee
  * Upravovat Externí ID záznamů
  * Měnit stav přiřazení štítků k evidenci
  * Doplnit FlexiBee GUI o tlačítka odkazující do FlexPloreru
  * Zakládat a mazat firmy
  * Resetovat firmu (smazat a znovu založit )
  * Uložit a znovu načíst výchozí zálohu firmy
  * Filtrovat záznamy dle ID nebo Externího ID

[![Source Code](http://img.shields.io/badge/source-VitexSoftware/Flexplorer-blue.svg?style=flat-square)](https://github.com/VitexSoftware/Flexplorer)
[![Latest Version](https://img.shields.io/github/release/VitexSoftware/Flexplorer.svg?style=flat-square)](https://github.com/VitexSoftware/Flexplorer/releases)
[![Software License](https://img.shields.io/badge/license-GNU-brightgreen.svg?style=flat-square)](https://github.com/VitexSoftware/Flexplorer/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/VitexSoftware/Flexplorer/master.svg?style=flat-square)](https://travis-ci.org/VitexSoftware/Flexplorer)
[![Coverage Status](https://img.shields.io/coveralls/VitexSoftware/Flexplorer/master.svg?style=flat-square)](https://coveralls.io/r/VitexSoftware/Flexplorer?branch=master)

Vyzkoušejte: 

  * Stabilní verze: http://flexibee-dev.spoje.net/ (vždy funguje)
  * Vývojová verze: https://vitexsoftware.cz/flexplorer/ ( poslední novinky )

Pro přihlášení se používá jména a hesla uživatele aplikace s oprávněním používat REST API:

![Mobilní přihlášení](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-mobile_login.png "Screenshot přihlášení")

FlexPlorer zobrazuje odpověď požadavku:

![Odpověď serveru](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-response_serveru.png "Screenshot odpovědi")

Je možné si zvolit libovolnou evidenci FlexiBee a její obsah si vypsat:

![Výpis evidence](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-vypis_evidence.png "Screenshot výpisu evidence")

Data můžeme editovat a uložit, pokud k tomu má přihlášený uživatel práva:

![Editor Evidence](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-editor-evidence.png "Screenshot Editoru Evidence")

Nad otevřenou evidencí je možné snadno provádět dotazy:

![JSON Editor](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-json-editor.png "Screenshot JSON Editoru")

Smazání záznamu z evidence je třeba potvrdit:

![Potvrzení před smazáním](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-potvrzeni-pred-smazanim.png "Přehledu a potvrzení před smazáním záznamu")

Při testování WebHooku pomůže tento nástroj, který sestaví maketu záznamu ChangesAPI a odešle jí na zvolený WebHook:

![WebHook Request](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-webhook-request.png "Screenshot Požadavku na webhook")

Správná je prázdná odpověď. Ačkoliv funguje, tak by se tento skript FlexiBee nelíbil. Zde vidíme co vrací:

![WebHook Response](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-webhook-response.png "Screenshot Odpovědi webhooku")

Pro hledání v evidencích je k dispozici vyhledávací políčko:

![WebHook Response](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-hinter_evidence.png "Našeptávač evidencí")

Po stisku entru se zobrazí podrobnější výsledky:

![WebHook Response](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-nalezene_evidence.png "Nalezené evidence")

Vyhledávat je také možné v názvech sloupců jednotlivých evidencí a jejich popiscích:

![Columns Search](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-nalezene_sloupce.png "Nalezené sloupce")

Tělo požadavku je možné načítat ze souboru:

![File Upload](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-xml-file-upload.png "Upload Souboru")

Odpověď požadavku může být v podporovaných formátech (zde XML):

![XML Response](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-xml-response.png "XML Response")


Instalace
---------

Pro Debian či Ubuntu prosím použijte [repozitář](http://vitexsoftware.cz/repos.php):

    wget -O - http://v.s.cz/info@vitexsoftware.cz.gpg.key|sudo apt-key add -
    echo deb http://v.s.cz/ stable main > /etc/apt/sources.list.d/vitexsoftware.list
    aptitude update
    aptitude install flexplorer

Poté budou zobrazeny dialogy pro zadání výchozího serveru, jména a hesla.
Takto zadané údaje budou zapsány do konfiguráku aplikace a nabízeny jako 
předvyplněné.

![Debian Configure](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-debian-configure.png "Konfigurace v Debianu")

Po instalaci balíčku a reloadu webserveru bude aplikace nainstalována do složky 
/usr/share/flexplorer a dostupná jako http://localhost/flexplorer/ 
( konfigurováno v  /etc/apache2/conf-enabled/flexplorer.conf ) 
Pokud je nainstalován démon avahi, bude tento propagovat aplikaci jako službu.
Aplikaci je pak možné spustit z nabídky programů v sekci "programování"

Aktualizace
-----------

Pokud máte balíček nainstalovný ze zdroje, aktualizace probíhají tak jak je v debianu zvykem: 

![Debian Upgrade](https://raw.githubusercontent.com/VitexSoftware/Flexplorer/master/screenshots/flexplorer-debian-upgrade.png "Aktualizace balíčku")


Vagrant
-------
K dispozici je také [Box](https://atlas.hashicorp.com/vitexsoftware/boxes/flexplorer) pro Vagrant. Po doběhnutí příkazu

    vagrant init vitexsoftware/flexplorer; vagrant up --provider virtualbox

bude možné aktuální vývojovou zobrazit na adrese [http://localhost:8080/src/]
a verzi z nejnovějšího debianího balíčku na adrese [http://localhost:8080/flexplorer/]




Konfigurace
-----------

Konfigurační soubor config.php se náchází ve složce src/includes. Výchozí konfigurace vypadá takto:

    define('LOG_NAME', 'Flexplorer'); //Identifikace logu
    define('LOG_TYPE', 'syslog'); //Možné hodnoty: memory, syslog, file

    /*
     * Výchozí odesilatel zpráv
     */
    define('EMAIL_FROM', 'flexplorer@localhost');

    /*
     * URL Flexibee API
     */
    define('DEFAULT_FLEXIBEE_URL', 'https://demo.flexibee.eu');
    /*
     * Uživatel FlexiBee API
     */
    define('DEFAULT_FLEXIBEE_LOGIN', 'winstrom');
    /*
     * Heslo FlexiBee API
     */

    define('DEFAULT_FLEXIBEE_PASSWORD', 'winstrom');
    /*
     * Společnost v FlexiBee
     */

    define('DEFAULT_FLEXIBEE_COMPANY', 'demo');



Závislosti
----------
 
 * [FlexiBee](https://www.flexibee.eu/)
 * [EaseFramework](https://github.com/VitexSoftware/EaseFramework)
 * [FlexiPeeHP](https://github.com/Spoje-NET/FlexiPeeHP)
 * [Twitter Bootstrap](http://getbootstrap.com/)
 * [Bootstrap Switch](http://www.bootstrap-switch.org/)

Poděkování
----------

Vznik tohoto nástroje by nebyl možný bez laskavé podpory společnosti [Spoje.Net](http://www.spoje.net), 

U společnosti Spoje.Net, je možné si objednat komerční podporu pro integraci
knihovny [FlexiPeeHP](https://github.com/Spoje-NET/FlexiPeeHP) do vašich projektů. 

![Spoje.Net](https://github.com/Spoje-NET/FlexiPeeHP/raw/master/spoje-net_logo.gif "Spoje.Net")

