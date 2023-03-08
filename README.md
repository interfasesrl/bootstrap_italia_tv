# bootstrap_italia_tv
Prova installazione e templating tema Bootstrap Italia

## installazione node_modules
"node_modules" del tema non versionati perchè troppo pesanti, 
includono tutte le dipendenze delle librerie di bootstrap e bootstrap_italia
per installare da shell git:

cd web/custom/italiagov
npm install

## watch scss
Durante sviluppo insieme a Docker, lanciare una shell git con

npm run watch:dev

Che compila scss in tempo reale nel css letto da Drupal
