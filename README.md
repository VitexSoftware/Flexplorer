Flexplorer
==========

![Flexplorer Logo](https://raw.githubusercontent.com/Spoje-NET/Flexplorer/master/src/images/flexplorer-logo.png "Project Logo")

Vývojářský nástroj pro FlexiBee API/JSON. Napsaný s využitím knihovny [FlexiPeeHP](https://github.com/Spoje-NET/FlexiPeeHP)
Umožňuje:

  * zobrazovat obsah všech dostpných evidencí ve všech firmách
  * zobrazovat strukturu evidence
  * odesílat přímé požadavky na server a zobrazovat výsledky
  * Nastavovat ChangesAPI a přidávat WebHooks  

[![Source Code](http://img.shields.io/badge/source/Spoje-NET/Flexplorer-blue.svg?style=flat-square)](https://github.com/Spoje-NET/Flexplorer)
[![Latest Version](https://img.shields.io/github/release/Spoje-NET/Flexplorer.svg?style=flat-square)](https://github.com/Spoje-NET/Flexplorer/releases)
[![Software License](https://img.shields.io/badge/license-GNU-brightgreen.svg?style=flat-square)](https://github.com/Spoje-NET/Flexplorer/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/Spoje-NET/Flexplorer/master.svg?style=flat-square)](https://travis-ci.org/Spoje-NET/Flexplorer)
[![Coverage Status](https://img.shields.io/coveralls/Spoje-NET/Flexplorer/master.svg?style=flat-square)](https://coveralls.io/r/Spoje-NET/Flexplorer?branch=master)

Demo: http://flexibee-dev.spoje.net/

![Mobilní přihlášení](https://raw.githubusercontent.com/Spoje-NET/Flexplorer/master/flexplorer-mobile_login.png "Screenshot přihlášení")
![Odpověď serveru](https://raw.githubusercontent.com/Spoje-NET/Flexplorer/master/flexplorer-response_serveru.png "Screenshot odpovědi")
![Výpis evidence](https://raw.githubusercontent.com/Spoje-NET/Flexplorer/master/flexplorer-vypis_evidence.png "Screenshot výpisu evidence")
  


Instalace
---------

Pro Debian prosím použijte [repozitář](http://vitexsoftware.cz/repos.php):

    wget -O - http://v.s.cz/info@vitexsoftware.cz.gpg.key|sudo apt-key add -
    echo deb http://v.s.cz/ stable main > /etc/apt/sources.list.d/vitexsoftware.list
    aptitude update
    aptitude install flexplorer

po instalaci balíčku a reloadu webserveru bude aplikace nainstalována do složky 
/usr/share/flexplorer a dostupná jako http://localhost/flexplorer/ 
( konfigurováno v  /etc/apache2/conf-enabled/flexplorer.conf ) 
Pokud je nainstalován démon avahi, bude tento propagovat aplikaci jako službu.

Závislosti
----------
 
 * [FlexiBee](https://www.flexibee.eu/)
 * [EaseFramework](https://github.com/VitexSoftware/EaseFramework)
 * [FlexiPeeHP](https://github.com/Spoje-NET/FlexiPeeHP)
 * [Twitter Bootstrap](http://getbootstrap.com/)
 * [Bootstrap Switch](http://www.bootstrap-switch.org/)

#Poděkování
Vznik tohoto nástroje by nebyl možný bez laskavé podpory společnosti [Spoje.Net](http://www.spoje.net), 

U společnosti Spoje.Net, je možné si objednat komerční podporu pro integraci
knihovny [FlexiPeeHP](https://github.com/Spoje-NET/FlexiPeeHP) do vašich projektů. 

![Spoje.Net](https://github.com/Spoje-NET/FlexiPeeHP/raw/master/spoje-net_logo.gif "Spoje.Net")



