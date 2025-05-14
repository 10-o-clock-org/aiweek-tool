# aiweek-tool

Das AI-Week Redaktionstool ist eine Web-Anwendung, die potentiellen Sessiongeber:innen dazu dienen
soll, ihre Sessions zur AI-Week bekannt zu geben. Daran angebunden ist ein kleiner
Freigabe-/Jury-/Scheduling-Prozess.

Es handelt sich um eine Symfony 5.4 Applikation, die erstmals zur WueWW 2020 zur Verfügung gestellt
wurde ... und seither für die AI-Week weiterentwickelt wurde.

## Mitentwickeln

Wenn du Vorschläge zur Applikation hast, dann nichts wie los.  Auf GitHub unter *Issues* kannst du
gerne eigene Vorschläge einbringen bzw. dich an Diskussionen beteiligen.

Falls du aktiv mit an der Anwendung hacken möchtest, dein Beitrag ist herzlich willkommen :-)

Um die Applikation lokal auszuprobieren, bietet sich der Symfony DEV Web-Server in Kombination
mit SQLite als Datenbank an.  Dazu musst du nichts weiter konfigurieren, sondern einfach nur
das Git Repository klonen, dann composer & yarn install, dann die Anwendung starten.

 Step 1: Abhängigkeiten installieren + Build der JavaScript/CSS Assets

 ```
$ composer install
$ yarn install
$ yarn build
```

Step 2: Datenbank erstellen

```
$ bin/console doctrine:database:create -n
$ bin/console doctrine:schema:create -n
```

Wenn du Dummy-Daten in der Datenbank haben möchtest, dann zusätzlich

```
$ bin/console doctrine:fixtures:load -n
```

Last but not least, die Anwendung starten

```
$ bin/console cache:warmup -n
$ symfony local:server:start --no-tls
```

Du kannst dich jetzt mit dem Browser einloggen:

- User: editor@example.org / Passwort: editor_password
- User: reporter1@example.org / Passwort: reporter1_password
- User: reporter2@example.org / Passwort: reporter2_password

## Datenbank Migration

Die Produktionsumgebung läuft *nicht* auf SQLite, sondern auf MySQL.  Um das lokal zu testen zunächst eine MySQL in
Docker hochfahren, zum Beispiel so:

```
$ docker run -d --name aiweek-mysql -e MYSQL_USER=aiweek -e MYSQL_PASSWORD=aiweek -e MYSQL_ROOT_PASSWORD=password -e MYSQL_DATABASE=aiweek -p 3306:3306 mysql:8
```

und dann folgende Zeile in die `.env.local` Datei einfügen (bzw. diese ggf. anlegen):

```
DATABASE_URL=mysql://aiweek:aiweek@127.0.0.1:3306/aiweek
```

Jetzt kannst du die Migrationen mit dem Befehl

```
$ bin/console doctrine:migrations:migrate latest
```

... erstmal ausgeführt werden. Wenn du danach die Entity-Klassen abänderst, kannst du so ein neues Migration Skript
erzeugen:

```
$ bin/console make:migration
```

Das Skript wird dann im Ordner `migrations` abgelegt.

## Admin User anlegen

```
$ bin/console app:create-editor adminuser@example.org
```
