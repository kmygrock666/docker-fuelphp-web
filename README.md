crontab
*/1 * * * * docker exec -i app_php-fpm5.5_1 bash -c "cd /var/www/fuel_server && php oil refine timer"


migrate

php oil refine migrate:current

auth:

oil refine migrate:current --packages=auth


docker exec -i app_php-fpm5.5_1 bash -c "cd /var/www/fuel_server && php oil refine timer 10"

docker exec -i app_php-fpm5.5_1 bash -c "cd /var/www/fuel_server && php oil refine migrate:current"

