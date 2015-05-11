php app/console cache:clear
php app/console doctrine:database:drop --force
php app/console doctrine:database:create  --a faire manuellement
php app/console doctrine:schema:update --force

php app/console fos:elastica:populate

launchctl load ~/Library/LaunchAgents/homebrew.mxcl.elasticsearch.plist

php app/console security:roles:build roles.yml

php app/console security:set:admin Nicolas.Uffer


#command populate

php app/console app:populate create

php app/console app:populate fill 100

php app/console app:populate security

