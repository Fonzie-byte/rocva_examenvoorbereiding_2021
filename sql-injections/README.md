## Opzet

Voer het bestand setup.sql uit, dit maakt de volgende 2 databases:

* ex-sql-injection-001
* ex-sql-injection-002

De _001 wordt gebruikt door login_fout.php, de _002 wordt gebruikt door login_goed.php.

Kijk in die twee login php bestanden bij ``new PDO`` of de gebruikersnaam en het wactwoord overeenkomen met wat je zelf
hebt.

## Gebruiken

1. Open index.php
1. Verander ``action="login_X.php"`` naar ``action="login_fout.php"``
1. Ga in je browser naar localhost/sql_injections en log in met

* user';DROP TABLE users;
* user

Als het goed is zie je nu "There are 0 registered users!" in je browser, en ben je nu in database 001 de hele tabel
users kwijt!

1. Open index.php weer
1. Verander ``action="login_fout.php"`` nu naar ``action="login_goed.php"``
1. Ga in je browser weer naar localhost/sql_injections en log weer in met
   * user';DROP TABLE users;
   * user

Als het goed is logt hij nu niet in.

Probeer het nu nog een keer met

* user
* user

Als het goed is logt hij nu in, en zie je nu "There are 3 registered users!" in je browser. Database 002 heeft nog
steeds een tabel users met 3 users erin.

## Moraan van het verhaal

Nooit input direct in een SQL-string zetten, *zeker straks in het werkleven!*
Anders kan zomaaar iedereen je data verwijderen of zelfs kopiëren/stelen! (``user';SELECT \* FROM TABLE users``)
Gebruik dus altijd parameters (``:kolomnaam``) en vul die dan in via een associative array in PDOStatement->execute()! (
dus ``$statement->execute(['kolomnaam' => waarde])``)
