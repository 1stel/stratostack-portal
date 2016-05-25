## StratoSTACK Billing Portal

### Pre-requisites

**Packages:**  
Apache2  
MySQL 5.5+  
PHP-5.5.9+  
Redis-server

**Other:**  
StratoSTACK Bill Records Generation Server  
PowerDNS Server  
TaxCloud Account  
Authorize.Net Account

### Installation

StratoSTACK uses Composer for dependency management.  See its [Download Guide](https://getcomposer.org/download/) for installation instructions.

#### Ubuntu

**Ubuntu 16.04 Package Dependencies**

	# sudo apt install apache2 php mysql-server php-mysql php-mcrypt php-mbstring libapache2-mod-php php-bcmath php-xml

**Ubuntu 14.04 Package Dependencies**

	# sudo apt-get install apache2 php5 mysql-server-5.5 php5-mysql redis-server php5-mcrypt

Move to the html directory:

	# cd /var/www/html

**Install StratoSTACK Billing Portal**

	# composer create-project --prefer-dist 1stel/stratostack-portal portal

**Add a MySQL-compatible database named cloud_billing for portal's usage**

	# mysqladmin -u<your user> -p create cloud_billing

**Edit configuration files**

Edit .env, adding database access configuration.

	DB_HOST=localhost
	DB_DATABASE=cloud_billing
	DB_USERNAME=homestead
	DB_PASSWORD=secret

Edit config/cloud.php.  Add management server, API credentials and the Bill Records Generation API key.

Edit config/taxcloud.php, config/authorizenet.php and config/powerdns.php, entering the values for your TaxCloud, Authorize.Net accounts and PowerDNS server credentials.

**Populate the database**

In /var/www/html/portal, run these commands

	# php artisan migrate: install
	# php artisan migrate --seed

**Update Apache Configuration**

Edit /etc/apache2/sites-enabled/000-default.conf

Change DocumentRoot to /var/www/html/portal/public

Add the following under DocumentRoot:

	<Directory /var/www/html>
		Options FollowSymLinks
		AllowOverride All
	</Directory>

Enable mod_rewrite:

	# a2enmod rewrite

Restart Apache:

	# service apache2 restart

Set permissions on the Portal

	# chown www-data.www-data /var/www/html/portal -R

**Add Laravel's event scheduler to cron**

Add the following to your crontab:

	* * * * * root php /var/www/html/portal/artisan schedule:run >> /dev/null 2>&1

**Run the queue processor upon startup**

Add the following to /etc/rc.local:

	php /var/www/html/portal/artisan queue:listen --sleep=5 --tries=3 &

### Customization

See customize.md for more information on customizing the StratoSTACK installation for your needs.