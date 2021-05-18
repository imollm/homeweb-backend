# TFG - WEB DEVELOPMENT
## HOMEWEB

## LOCAL ENVIRONMENT

### Pre requisits

1. Install web environment
    - [XAMPP](https://www.apachefriends.org/es/download.html)
    - [MAMP](https://www.mamp.info/en/downloads/)
    - Manually mode
       - [MAC OS X](https://getgrav.org/blog/macos-bigsur-apache-multiple-php-versions)
       - [Linux](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-20-04-es)
       - [Windows 10](https://codebriefly.com/how-to-setup-apache-php-mysql-on-windows-10/)

    
2. When installation is finished, go to `http://localhost`, if **It Works!** displayed, all is correct.


3. Check if PHP is linked with Apache. Go to apache root folder and create a new index.php file, with this code `<?php echo phpinfo();`, save and go to `http://localhost`, we will to see something similar to the image. 
    <p>
        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PHP_7.1_-_Example_phpinfo%28%29_Screen.png" alt="phpinfo image"/>
   </p>


4. Next step, install php dependencies manager Composer, [Composer](https://getcomposer.org/doc/00-intro.md). Install better globally, then type `composer --version` and you make sure.

### Inicialitzar projecte

1. Download the repository.
    - If Git is installed, go to apache root folder and type `git clone https://github.com/imollm/homeweb-backend`
    - If Git isn't installed [Git](https://git-scm.com/downloads), else `https://github.com/imollm/homeweb-backend`, then unzip.
    

2. Type `composer install` and composer will install laravel dependencies, from composer.json.


3. Type `cp .env.exemple .env`, and then configure database params.

    <ul>
        <li style="list-style: none"><span style="color: orange">DB_CONNECTION</span><span style="color: white">=</span><span style="color: green">mysql</span></li>
        <li style="list-style: none"><span style="color: orange">DB_HOST</span><span style="color: white">=</span><span style="color: green">127.0.0.1</span></li>
       <li style="list-style: none"><span style="color: orange">DB_PORT</span><span style="color: white">=</span><span style="color: green">3306</span></li>
       <li style="list-style: none"><span style="color: orange">DB_DATABASE</span><span style="color: white">=</span><span style="color: green">homeweb</span></li>
       <li style="list-style: none"><span style="color: orange">DB_USERNAME</span><span style="color: white">=</span><span style="color: green">root</span></li>
       <li style="list-style: none"><span style="color: orange">DB_PASSWORD</span><span style="color: white">=</span><span style="color: green">password_here</span></li>
    </ul>



4. Create new database with the same name of DB_DATABASE of .env file
      ```sh
      mysql> create database homeweb
      Query OK, 1 row affected (0.00 sec)
      ```
   
5. Then run migrations and seeders.
    ```sh
      php artisan migrate --seed
    ```
6. Generate app keys.
   ```sh
      php artisan key:generate
   ```

7. Generate token client keys.
    ```sh
      php artisan passport:install
    ```
   If rerun migrations, auth tokens will be deleted, you may rerun to generate it.
   I create a command that do migration and generate auth tokens.
    ```sh
      php artisan db:restore
    ```
8. We need to configure into ```.env``` file the email client. Then publish credentials with next command.
   ```sh
      php artisan config:cache
   ```

### Testing with Postman
1. Install API REST Client [Postman](https://www.postman.com/downloads/)
   

2. Download project json file.
    ```sh
      wget -O ~/tfg-docker-backend/postman.json https://www.getpostman.com/collections/c02928439a50147cc744
    ```

3. In environment configuration if you work with:
   * Local environment ```http://localhost/homeweb-backend/public/api```
   * Docker environment ```http://localhost:8080/homeweb-backend/public/api```
    

4. Open with postman and test it.

### Test amb PHPUnit
In ```env``` file there are some registered users, and some fake data to test backend.
<ul>
<li style="list-style: none"><span style="color: orange">API_ADMIN_EMAIL</span><span style="color: white">=</span><span style="color: green">admin@homeweb.com</span></li>
<li style="list-style: none"><span style="color: orange">API_ADMIN_PASSWORD</span><span style="color: white">=</span><span style="color: green">12345678</span></li>
<li style="list-style: none"><span style="color: orange">API_CUSTOMER_EMAIL</span><span style="color: white">=</span><span style="color: green">customer@homeweb.com</span></li>
<li style="list-style: none"><span style="color: orange">API_CUSTOMER_PASSWORD</span><span style="color: white">=</span><span style="color: green">12345678</span></li>
<li style="list-style: none"><span style="color: orange">API_EMPLOYEE_EMAIL</span><span style="color: white">=</span><span style="color: green">employee@homeweb.com</span></li>
<li style="list-style: none"><span style="color: orange">API_EMPLOYEE_PASSWORD</span><span style="color: white">=</span><span style="color: green">12345678</span></li>
<li style="list-style: none"><span style="color: orange">API_OWNER_EMAIL</span><span style="color: white">=</span><span style="color: green">owner@homeweb.com</span></li>
<li style="list-style: none"><span style="color: orange">API_OWNER_PASSWORD</span><span style="color: white">=</span><span style="color: green">12345678</span></li>
<li style="list-style: none"><span style="color: orange">API_OWNER1_EMAIL</span><span style="color: white">=</span><span style="color: green">owner1@homeweb.com</span></li>
<li style="list-style: none"><span style="color: orange">API_OWNER1_PASSWORD</span><span style="color: white">=</span><span style="color: green">12345678</span></li>
</ul>


Type this command to publish this users into cache, and then run tests.
   ```sh
      php artisan config:cache
   ```

### Llicència

Aquest programari té llicència [MIT license](https://opensource.org/licenses/MIT).

## Contact

Ivan Moll Moll - imollm@uoc.edu

Project Link: [https://github.com/imollm/homeweb-backend](https://github.com/imollm/homeweb-backend)
