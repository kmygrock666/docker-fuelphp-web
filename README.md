配置
crontab
*/1 * * * * docker exec -i app_php-fpm5.5_1 bash -c "cd /var/www/fuel_server && php oil refine timer"

資料庫遷移migrate
php oil refine migrate:current

websocket啟動
docker exec -i app_php-fpm5.5_1 bash -c "cd /var/www/fuel_server && php oil refine react"