# Laravel and ElasticSearch

## Setup

### ElasticSearch
Install ElasticSearch 6.7 or higher. The code should work with latest version of ElasticSearch. However, the code only tested with ElasticSearch 6.7

### Database migration
Please create the `world` database and populate data from the following url.

https://downloads.mysql.com/docs/world.sql.zip

Detail guide:

https://dev.mysql.com/doc/world-setup/en/world-setup-installation.html


### Index creation

```
php artisan elastic:create-index
```

The above command will create `world` index.

## Push all data to ElasticSearch index

```
php artisan elastic:push-to-cluster
```

The above command will push all data from database to `world` index.

## Unit testing
```
vendor/bin/phpunit
```
