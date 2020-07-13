# IPSymconGardena
[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-5.0%20%3E-green.svg)](https://www.symcon.de/forum/threads/38222-IP-Symcon-5-0-verf%C3%BCgbar)

Modul für IP-Symcon ab Version 5. Ermöglicht die Kommunikation mit Gardena Geräten.

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)  
2. [Voraussetzungen](#2-voraussetzungen)  
3. [Installation](#3-installation)  
4. [Funktionsreferenz](#4-funktionsreferenz)  
5. [Anhang](#5-anhang)  

## 1. Funktionsumfang

Steuerung von Gardena Geräten über die Gardena API.

## 2. Voraussetzungen

 - IPS 5.2
 - Gardena Benutzername und Gardena Smart Gateway
 - IP-Symcon Connect

## 3. Installation

### a. Laden des Moduls

Die Webconsole von IP-Symcon mit _http://{IP-Symcon IP}:3777/console/_ öffnen. 


Anschließend oben rechts auf das Symbol für den Modulstore (IP-Symcon > 5.2) klicken

![Store](img/store_icon.png?raw=true "open store")

Im Suchfeld nun

```
Gardena
```  

eingeben

![Store](img/module_store_search.png?raw=true "module search")

und schließend das Modul auswählen und auf _Installieren_

![Store](img/install.png?raw=true "install")

drücken.

### b. Gardena-Cloud
Es wird ein Account bei Gardena benötigt, den man für das Gardena Smart Gateway nutzt.

Um Zugriff auf das Gardena Smart Gateway über die Gardena API zu erhalten muss zunächst IP-Symcon als System authentifiziert werden.
Hierzu wird ein aktives IP-Symcon Connect benötigt und den normalen Gardena Benutzernamen und Passwort.
Zunächst wird beim installieren des Modul gefragt ob eine Konfigurator Instanz angelegt werden soll, dies beantwortet man mit _ja_, man kann aber auch die Konfigurator Instanz von Hand selber anlegen

### c. Authentifizierung bei Gardena
Anschließend erscheint ein Fenster Schnittstelle konfigurieren, hier drückt man auf den Knopf _Registrieren_ und hält seinen Gardena (Husqvarna) Benutzernamen und Passwort bereit.

![Schnittstelle](img/register.png?raw=true "Schnittstelle")

Es öffnet sich die Anmeldeseite von Gardena. Hier gibt man in die Maske den  Benutzernamen und das Gardena Passwort an und fährt mit einem Klick auf _Anmelden_ fort.

![Anmeldung](img/gardena_oauth_1.png?raw=true "Anmeldung")

Jetzt wird man von Gardena gefragt ob IP-Symcon als System die persönlichen Geräte auslesen darf, die Gardena Geräte steuern sowie den Status der Geräte auslesen darf.
HIer muss man nun mit _Ja_ bestätigen um IP-Symcon zu erlauben das Gardena Smart Gateway zu steuern und damit auch die Gardena Geräte steuern zu können.

![Genehmigung](img/gardena_oauth_2.png?raw=true "Genehmigung")

Es erscheint dann eine Bestätigung durch IP-Symcon das die Authentifizierung erfolgreich war,
 
![Success](img/oauth_2.png?raw=true "Success")
 
anschließend kann das Browser Fenster geschlossen werden und man kehrt zu IP-Symcon zurück.
Zurück beim Fenster Schnittstelle konfigurieren geht man nun auf _Weiter_

Nun öffnen wir die Konfigurator Instanz im Objekt Baum zu finden unter _Konfigurator Instanzen_. 

### d. Einrichtung des Konfigurator-Moduls

Jetzt wechseln wir im Objektbaum in die Instanz _**Gardena**_ (Typ Gardena Configurator) zu finden unter _Konfigurator Instanzen_.

![config](img/gardena_konfigurator.png?raw=true "config")

Hier werden alle Geräte, die bei Gardena unter dem Account registiert sind und von der Gardena API unterstützt werden aufgeführt.

Ein einzelnes Gerät kann man durch markieren auf das Gerät und ein Druck auf den Button _Erstellen_ erzeugen. Der Konfigurator legt dann eine Geräte Instanz an.

### e. Einrichtung der Geräteinstanz
Eine manuelle Einrichtung eines Gerätemoduls ist nicht erforderlich, das erfolgt über den Konfigurator. In dem Geräte-Modul ist gegebenenfalls nur das Abfrage-Intervall anzupassen, die anderen Felder, insbesondere die Seriennummer (diese ist die Identifikation des Gerätes) und die Geräte-Typ-ID (diese steuert, welche Variablen angelegt werden) sind vom Konfigurator vorgegeben.


## 4. Funktionsreferenz


  

## 5. Konfiguration:



## 6. Anhang

###  GUIDs und Datenaustausch:

#### Gardena Cloud:

GUID: `{9775D7CA-5667-8554-0172-2EBB2F553A54}` 


#### Gardena Device:

GUID: `{3B073BE1-6556-037C-42FB-6311BC452C68}` 