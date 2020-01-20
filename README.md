# Training Factory Project!

# Hoe krijg ik dit aan de praat?

Clone de repository, en voer het volgende command uit op een UNIX-like systeem (MacOS, Linux)

``` bash
php bin/console doctrine:database:drop --force && php bin/console doctrine:database:create && rm -rf var/cache && rm -rf src/Migrations && php bin/console make:migration && php bin/console doctrine:migrations:migrate --no-interaction && php bin/console doctrine:fixtures:load --no-interaction && rm -rf src/Migrations
```


Dit project nog Work In Progress!
Bugs reporten kan je doen in de [issue](https://github.com/godwinacheampong/tf_project/issues) tracker!
