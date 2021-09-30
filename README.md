# ATLAS

yarn install
php bin/console doctrine:fixtures:load
symfony server:start
php bin/console doctrine:migrations:migrate
php bin/console doctrine:database:create
php bin/console doctrine:database:drop --force
yarn encore dev
symfony server:stop
