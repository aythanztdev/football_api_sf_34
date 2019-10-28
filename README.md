# Requirements

### Docker

Move to docker directory
```
cd infra/docker
```

Copy .env
```
cp dist.env .env
```

Build
```
docker-compose build
docker-compose up -d
```

### System requirements
Add to /etc/hosts
```
127.0.0.1 lfp-api.loc
```

### Deploy project
Composer install
```
docker exec -it lfp-api-php /bin/bash
cd api
composer install
exit
```

Create database
```
docker exec -it lfp-api-db mysql -e "CREATE DATABASE IF NOT EXISTS lfp"
docker exec -it lfp-api-db mysql -e "GRANT ALL ON lfp.* TO 'lfp'@'%' IDENTIFIED BY 'lfp'"
docker exec -it lfp-api-php php api/bin/console doctrine:schema:update --force
```