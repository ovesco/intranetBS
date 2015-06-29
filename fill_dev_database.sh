#!/usr/bin/env bash
php app/console cache:clear

#command populate
php app/console doctrine:schema:update --force
php app/console security:roles:build roles.yml
php app/console app:populate create
php app/console app:populate fill 200
php app/console app:populate security
php app/console fos:elastica:populate
