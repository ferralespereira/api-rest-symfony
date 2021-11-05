# Api Rest Symfony
This is an Api Rest for a video web site enviroment. Development wiht Symfony php framework.

## Some Tips:
### To Run the proyect:
* Install "apache-pack" to allow the framework runs in Apache: `composer require symfony/apache-pack`
* Configure the apache file "/etc/apache2/sites-available" like this:
```   
# api-rest-symfony
<VirtualHost 127.0.0.4:80>
    # ...
    DocumentRoot /var/www/html/api-rest-symfony/public

    <Directory /var/www/html/api-rest-symfony/public>
        AllowOverride None

        # Copy project/public .htaccess file content here-----ini
        DirectoryIndex index.php
        <IfModule mod_negotiation.c>
            Options -MultiViews
        </IfModule>

        <IfModule mod_rewrite.c>
           RewriteEngine On

           RewriteCond %{REQUEST_URI}::$0 ^(/.+)/(.*)::\2$
           RewriteRule .* - [E=BASE:%1]

           RewriteCond %{HTTP:Authorization} .+
           RewriteRule ^ - [E=HTTP_AUTHORIZATION:%0]

           RewriteCond %{ENV:REDIRECT_STATUS} =""
           RewriteRule ^index\.php(?:/(.*)|$) %{ENV:BASE}/$1 [R=301,L]

           RewriteCond %{REQUEST_FILENAME} !-f
           RewriteRule ^ %{ENV:BASE}/index.php [L]

        </IfModule>

        <IfModule !mod_rewrite.c>
                <IfModule mod_alias.c>
                        RedirectMatch 307 ^/$ /index.php/
                 </IfModule>
        </IfModule>
        # Copy project/public .htaccess file content here-----end
        

    </Directory>
</VirtualHost>
```
* Restart apache: `sudo systemctl restart apache2`

### Other Tips:
* To install new Symfony api: `composer create-project symfony/skeleton api-name "4.4.*"`
* To make a controller: `php bin/console make:controller UserController`
* To see my Symfony version: `php bin/console --version`
* To remove any package: `composer remove package-name`
* To install symfony by composer.json file: `composer update "symfony/*"`
* To create Entities(this are like Data Base models): `php bin/console doctrine:mapping:import App\Entity annotation --path=src/Entity`