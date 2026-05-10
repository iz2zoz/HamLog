HAMLOG - Webapp PHP/MySQL per log radioamatoriale

Installazione su hosting Aruba condiviso:

1) Crea un database MySQL dal pannello Aruba.
2) Importa install.sql nel database tramite phpMyAdmin.
3) Modifica config.php con host, nome database, username e password MySQL.
4) Carica tutti i file via FTP nella cartella desiderata.
5) Apri https://tuodominio.it/percorso/install.php
6) Crea il primo utente superadmin.
7) Dopo la creazione del superadmin, elimina install.php dal server.
8) Accedi da login.php.

Note:
- Ogni utente vede solo i propri log.
- Il superadmin crea/modifica/disattiva utenti.
- Ogni log ha nominativo usato, fuso orario, locator e referenza opzionale.
- Export ADIF disponibile da lista log e dashboard QSO.
- ADIF esporta data e ora in UTC.
