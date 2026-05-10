# HamLog Webapp

Webapp PHP/MySQL per log radioamatoriale personale o multiutente, pensata per hosting condiviso Aruba.

Funzioni principali:

- multiutente con pannello superadmin;
- nessuna registrazione autonoma: gli utenti vengono creati dal superadmin;
- più log per ogni utente, utili per POTA, SOTA, contest, attivazioni o log personali separati;
- inserimento rapido QSO da desktop e smartphone;
- maschera QSO completa con call, banda, modo, frequenza, RST, nome, QTH, locator e note;
- bande e modi predefiniti;
- RST automatico in base al modo;
- export ADIF per singolo log;
- salvataggio QSO con coda locale: se cade la connessione, il QSO resta nel browser come "non sincronizzato" e viene reinviato quando torna online;
- interfaccia responsive basata su Bootstrap 5.

## Requisiti

- PHP 7.4 o superiore, consigliato PHP 8.x;
- MySQL/MariaDB;
- estensione PDO MySQL attiva;
- hosting Apache compatibile con `.htaccess`.

## Installazione su Aruba o hosting condiviso

1. Crea un database MySQL dal pannello hosting.
2. Importa `install.sql` nel database usando phpMyAdmin.
3. Copia `config.sample.php` in `config.php`.
4. Modifica `config.php` con i dati reali del database:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'nome_database');
define('DB_USER', 'username_database');
define('DB_PASS', 'password_database');
define('APP_NAME', 'HamLog');
```

5. Carica tutti i file nella cartella desiderata del sito, per esempio:

```text
/public_html/hamlog/
```

6. Apri nel browser:

```text
https://tuodominio.it/hamlog/install.php
```

7. Crea il primo utente superadmin.
8. Dopo la creazione del superadmin, elimina dal server:

```text
install.php
install.sql
```

9. Accedi da:

```text
https://tuodominio.it/hamlog/login.php
```

## File importanti

```text
login.php              login utenti
logout.php             logout
dashboard_logs.php     lista log dell'utente
log_new.php            creazione nuovo log
log_edit.php           modifica dati log
log_delete.php         eliminazione log
dashboard_qso.php      inserimento e lista QSO
save_qso.php           salvataggio QSO, anche JSON/fetch
edit_qso.php           modifica QSO
delete_qso.php         elimina QSO
export_adif.php        esportazione ADIF
admin_users.php        pannello utenti superadmin
admin_user_new.php     crea utente
admin_user_edit.php    modifica utente
```

## Hardening consigliato

Il file `.htaccess` incluso:

- disabilita il directory listing;
- blocca l'accesso diretto a `config.php`, `db.php`, `auth.php`, `functions.php`;
- blocca file `.sql`, `.zip`, `.log`, `.bak`, `.old`;
- blocca file nascosti.

Dopo l'installazione elimina comunque fisicamente `install.php` e `install.sql`: non limitarti a bloccarli.

## Uso con GitHub

Il file `.gitignore` esclude `config.php`, così eviti di pubblicare per errore le credenziali del database.

Nel repository lascia `config.sample.php`; sul server crea invece `config.php` copiandolo dal sample.

## Note ADIF

L'export ADIF usa data e ora in UTC. Ogni log ha il proprio nominativo stazione, fuso orario, locator e referenza opzionale.

## Versione

Versione attuale: **1.1**
