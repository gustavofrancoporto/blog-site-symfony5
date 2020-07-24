sudo rm -R /var/www/blogsite && |
sudo ln -s /var/www/blogsite_current /var/www/blogsite && |
cd /var/www/blogsite && |
sudo APP_ENV=$APP_ENV DATABASE_URL=$DATABASE_URL php bin/console doctrine:migrations:migrate && |
sudo chown -R www-data:www-data /var/www/blogsite_current && |
sudo chown -R www-data:www-data /var/www/blogsite && |
sudo service apache2 restart