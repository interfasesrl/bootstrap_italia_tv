# Bootstrap Italia Interfase
Prova installazione e templating tema Bootstrap Italia

docs ufficiali con gli step:

https://github.com/italia/design-drupal-theme


## Installazione node_modules
"node_modules" del tema non versionati perchè troppo pesanti, 
includono tutte le dipendenze delle librerie di bootstrap e bootstrap_italia
per installare da shell git:

```
cd web/themes/custom/italiagov
npm install
```


## svg-inline-loader rule
Problema per webpack nel fare il bundle di immagini svg inline nel codice,
risolto con il modulo

```
npm install svg-inline-loader --save-dev
```

Poi serve aggiungere la seguente regola a module.loaders.rules in 
webpack.dev.js e webpack.prod.js

```
{
   test: /\.svg$/,
   loader: 'svg-inline-loader'
}
```

https://www.npmjs.com/package/svg-inline-loader


## Watch scss
Durante sviluppo insieme a Docker, lanciare una shell git con

```
npm run watch:dev
```

Che compila scss in tempo reale nel css letto da Drupal,
serve comunque fare "drush cr" per vedere i cambiamenti


# Errori in creazione del progetto


## ERROR: "These entities need to be deleted before importing."
Dopo aver creato il DB la prima volta, provando a fare un drush cim potrebbe uscire l'errore

```
"Drupal\Core\Config\ConfigImporterException: There were errors validating the config synchronization.
[ ... ]
Entities exist of type <em class="placeholder">Shortcut link</em> and <em class="placeholder"></em>
<em class="placeholder">Default</em>. These entities need to be deleted before importing."
```

Risolto andando in sul back in Configuration > User interface > Shortcuts (admin/config/user-interface/shortcut). Entrando in "List links" della lista di shortcut "Default" e cancellandoli tutti.

https://www.drupal.org/forum/support/post-installation/2015-12-20/problem-during-import-configuration


## ERROR: "Site UUID in Source Storage does not match"
Dropo un drush cim, potrebbe uscire l'errore

```
"Site UUID in source storage does not match the target storage."
```

Risolto prendendo l'UUID dal DB originale del progetto creato con

```
$ drush config-get "system.site" uuid
```

e settando lo stesso UUID sul nuovo progetto clonato

```
$ drush config:set system.site uuid <my_uuid> -y
```

https://cornel.co/article/solve-missmatched-site-uuid-drupal
