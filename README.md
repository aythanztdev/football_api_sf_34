# Requirements

### Docker

Move to docker directory
```
cd infra/docker
```

#

Copy .env
```
cp dist.env .env
```

#

Build
```
docker-compose build --pull
docker-compose up -d
```

#

### System requirements
Add to /etc/hosts
```
127.0.0.1 lfp-api.loc
```

#

### Deploy project
Composer install
```
docker exec -it lfp-api-php /bin/bash
cd api
composer install
exit
```

#

### Creation and Population database
Create database
```
docker exec -it lfp-api-db mysql -e "CREATE DATABASE IF NOT EXISTS lfp"
docker exec -it lfp-api-db mysql -e "GRANT ALL ON lfp.* TO 'lfp'@'%' IDENTIFIED BY 'lfp'"
docker exec -it lfp-api-php php api/bin/console doctrine:schema:update --force
```

#

Populate database with random data
```
docker exec -it lfp-api-php /bin/bash
cd api
php bin/console doctrine:fixtures:load
exit
```

#

You can show some data visiting:
```
http://lfp-api.loc/api/clubs
http://lfp-api.loc/api/clubs/1
http://lfp-api.loc/api/players
http://lfp-api.loc/api/players/1
http://lfp-api.loc/api/coachs
http://lfp-api.loc/api/coachs/1
```

#
For testing the web services, you can use the LFP.postman_collection.json located in:
```
cd ../../api/data
```

Postman is a HTTP client for testing web services. You can download it from https://www.getpostman.com/

##### STEP 1
For importing the file, you should go to the top right and press "Import", after this, you should add
the LFP.postman_collection.json.

![alt text](https://github.com/aythanztdev/lfp_sf_34/blob/master/api/data/step1.png)

##### STEP 2
You have to press in the gear of top right and then, click on "Add".

![alt text](https://github.com/aythanztdev/lfp_sf_34/blob/master/api/data/step2.png)

##### STEP 3
Add the next values and save.

![alt text](https://github.com/aythanztdev/lfp_sf_34/blob/master/api/data/step3.png)

##### STEP 4
Go to the selector on top right and select LFP (the enviroment name that you set in the last step)

![alt text](https://github.com/aythanztdev/lfp_sf_34/blob/master/api/data/step4.png)

##### STEP 5
Select a service from the list of left side and press "Send" in the top right

![alt text](https://github.com/aythanztdev/lfp_sf_34/blob/master/api/data/step4.png)

#