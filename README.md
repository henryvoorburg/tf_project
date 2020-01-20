# Training Factory Project!

# Hoe krijg ik dit aan de praat?

Clone de repository, en voer het volgende command uit op een UNIX-like systeem (MacOS, Linux)

``` bash
php bin/console doctrine:database:drop --force && php bin/console doctrine:database:create && rm -rf var/cache && rm -rf src/Migrations && php bin/console make:migration && php bin/console doctrine:migrations:migrate --no-interaction && php bin/console doctrine:fixtures:load --no-interaction && rm -rf src/Migrations
```

Zet in je database deze parameters ook neer & vergeet niet de database connection info te updaten in het .env bestand.
```
SET GLOBAL innodb_default_row_format=dynamic;
SET GLOBAL innodb_file_format=barracuda;
SET GLOBAL innodb_file_per_table=true;
SET GLOBAL innodb_large_prefix=true;
```


Het project nog Work In Progress!
Bugs reporten kan je doen in de [issue](https://github.com/godwinacheampong/tf_project/issues) tracker!
