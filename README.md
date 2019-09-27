crontab
*/1 * * * * docker exec -i app_php-fpm5.5_1 bash -c "cd /var/www/fuel_server && php oil refine timer"