# TFG - DESENVOLUPAMENT WEB
## HOMEWEB

### Pre requisits

1. Tenir instal·lat un entorn web al nostre ordinador
    - [XAMPP](https://www.apachefriends.org/es/download.html)
    - [MAMP](https://www.mamp.info/en/downloads/)
    - Si ho vols fer manualment 
       - [MAC OS X](https://getgrav.org/blog/macos-bigsur-apache-multiple-php-versions)
       - [Linux](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-20-04-es)
       - [Windows 10](https://codebriefly.com/how-to-setup-apache-php-mysql-on-windows-10/)


2. Un cop instal·lat, comprovar que ens funciona l'Apache. Ens dirigim al navegador web i ingressem `http://localhost`, si ens dóna com a resultat un missatge **It Works!** significa que funciona.


3. Revisar que PHP està enllaçat a Apache. Ens dirigim a la carpeta root de l'Apache, cream un fitxer p.e. `index.php` i escrivim `<?php echo phpinfo();`, guardem i ens dirigim al navegador, 
   escrivim `http://localhost`, el resultat ha de donar similar a la imatge següent.
   
    <p>
        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PHP_7.1_-_Example_phpinfo%28%29_Screen.png" alt="phpinfo image"/>
   </p>

    Si obtenim el mateix resultat, significarà que tenim Apache i PHP funcionant.


4. El següent pas es instal·lar el gestor de dependencies de PHP, [Composer](https://getcomposer.org/doc/00-intro.md). Es interessant fer la instal·lació globalment, per comporvar que esta operatiu obrim una consola de comandes i escrivim `composer --version`, si ho hem fet bé, ens dirà la versió.

### Inicialitzar projecte

1. El següent pas és descarregar el repositori.
    - Si tenim Git instal·lat, ens situem a la carpeta root d'Apache i a la consola de comandes escrivim `git clone https://github.com/imollm/homeweb-backend`
    - Si no tenim [Git](https://git-scm.com/downloads) podem instal·lar-lo, sinó `https://github.com/imollm/homeweb-backend` i descarreguem amb zip.
    

2. Ara toca instal·lar les dependencies del projecte, per tant mitjançant per consola de comandes ens situem a la carpeta on tenim el projecte descomprimit i escrivim `composer install`, això farà que composer instal·li els paquets indicats en el fitxer composer.lock o sino al composer.json


3. Copiem el fitxer `.env.exemple` i canviem el nom, per tant a la consola `cp .env.exemple .env`. Ara cal configurar les credencials de la base de dades, per tant cal modificar els següents paràmetres.

    <ul>
        <li style="list-style: none"><span style="color: orange">DB_CONNECTION</span><span style="color: white">=</span><span style="color: green">mysql</span></li>
        <li style="list-style: none"><span style="color: orange">DB_HOST</span><span style="color: white">=</span><span style="color: green">127.0.0.1</span></li>
       <li style="list-style: none"><span style="color: orange">DB_PORT</span><span style="color: white">=</span><span style="color: green">3306</span></li>
       <li style="list-style: none"><span style="color: orange">DB_DATABASE</span><span style="color: white">=</span><span style="color: green">homeweb</span></li>
       <li style="list-style: none"><span style="color: orange">DB_USERNAME</span><span style="color: white">=</span><span style="color: green">root</span></li>
       <li style="list-style: none"><span style="color: orange">DB_PASSWORD</span><span style="color: white">=</span><span style="color: green">password_here</span></li>
    </ul>



4. Ara per testejar, primer de tot creem una base de dades.
      ```sh
      mysql> create database homeweb
      Query OK, 1 row affected (0.00 sec)
      ```
   
5. Ara executem les *migrations* i els *seeders* per crear un joc de dades de prova.
    ```sh
      php artisan migrate --seed
    ```

6. Generar claus d'encriptació de l'aplicació
   ```sh
      php artisan key:generate
    ```

7. Generar tokens keys.
    ```sh
      php artisan passport:install
    ```
   Tenir en compte que cada vegada que executem les migracions amb els seeders, les claus d'encriptació de Passport s'esborren, per tant tenim que tornar executar el pas 6.


   
### Test amb Postman
1. Instal·lem l'API REST Client [Postman](https://www.postman.com/downloads/)
2. Importem *l'API schema* que tenim al projecte `HomeWeb.postman_collection.json`
3. 


### Llicència

Aquest programari té llicència [MIT license](https://opensource.org/licenses/MIT).

#### README de exemple
https://github.com/othneildrew/Best-README-Template/blob/master/README.md
