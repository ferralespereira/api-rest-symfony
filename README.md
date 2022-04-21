# [Api Rest Symfony](https://video.javierfolder.com/)

<p align="center">
  <img src="https://github.com/ferralespereira/videos-angular/blob/master/src/assets/img/video.svg" width="350" title="Foro Angular">
</p>

<p align="center">
<strong>This is an Api Rest developed in Symfony to store favorite Youtube video links.</strong>
</p>
<p align="center">
  It is the Backend of the Frontend at https://github.com/ferralespereira/videos-angular
</p>
<br>


## Some Tips:
### To Run the proyect in Production:
* Go to: `https://symfony.com/doc/current/deployment.html#symfony-deployment-basics`
* Configure the apache file "/etc/apache2/sites-available" like this:
```apache   
# api-rest-symfony
<VirtualHost 127.0.0.4:80>
    ServerName yourdomain.com
    ServerAdmin webmaster@thedomain.com

    DocumentRoot /var/www/html

    <Directory /var/www/html>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
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
* To create Entity methods: `php bin/console make:entity --regenerate`