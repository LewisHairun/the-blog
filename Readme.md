* Pour preparer la base de donnée, execute la commande ci-dessous:
```
composer prepare-database
```

* Pour charger les données addresses, execute la commande ci-dessous:
```
php bin/console app:import:address
```

* Pour charger les données compagnies, execute la commande ci-dessous:
```
php bin/console app:import:company
```

* Pour charger les données utilisateurs, execute la commande ci-dessous:
```
php bin/console app:import:users
```

* Pour charger les données articles, execute la commande ci-dessous:
```
php bin/console app:import:posts
```

* Pour charger les données commentaires, execute la commande ci-dessous:
```
php bin/console app:import:comments
```

* Pour charger les données albums, execute la commande ci-dessous:
```
php bin/console app:import:albums
```

* Pour charger les données photos, execute la commande ci-dessous:
```
php bin/console app:import:photos
```

* Pour charger tous les données, execute la commande ci-dessous:
```
composer load-data
```
