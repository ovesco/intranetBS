#!/usr/bin/env bash
php app/console cache:clear
php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
php app/console fos:elastica:populate

#launchctl load ~/Library/LaunchAgents/homebrew.mxcl.elasticsearch.plist

#buil role hierarchy
php app/console security:roles:build roles.yml

#reset all the intranet_parameters defined in parameters.yml
php app/console parameter reset




