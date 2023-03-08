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
