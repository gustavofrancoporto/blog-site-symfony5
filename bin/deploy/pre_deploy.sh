# Remove symlink
sudo rm -R /var/www/blogsite_old && \
sudo cp -R /var/www/blogsite_current /var/www/blogsite_old/ && \
sudo rm /var/www/blogsite && \
sudo rm -R /var/www/blogsite_current && \
# Create symlink to older version
sudo ln -s /var/www/blogsite_old /var/www/blogsite