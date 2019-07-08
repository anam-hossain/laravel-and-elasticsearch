# Laravel and ElasticSearch

## Setup

### Database migration
Please create the `world` database and populate data from the following url.

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

The above command will push all data to `world` index.
