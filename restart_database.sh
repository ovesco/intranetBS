php app/console cache:clear
php app/console doctrine:database:drop --force
#php app/console doctrine:database:create  --a faire manuellement
#php app/console doctrine:schema:update --force

php app/console fos:elastica:populate

launchctl load ~/Library/LaunchAgents/homebrew.mxcl.elasticsearch.plist
