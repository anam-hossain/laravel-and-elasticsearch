# Laravel and ElasticSearch

## Setup

### ElasticSearch
Install ElasticSearch 6.7 or higher. The code should work with latest version of ElasticSearch. However, the code only tested with ElasticSearch 6.7

Add the following to `.env` file:

```
ELASTIC_HOST=127.0.0.1
ELASTIC_PORT=9200
```

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

## Searching documents

Visit:

```
/search?country=Australia&city=Sydney
```

## Run queries in Kibana
#### List indices
GET /_cat/indices?v&h=index,docs.count

#### Get mappings
GET /world/_doc/_mapping

#### Search all
GET /world/_doc/_search

#### Bool Queries
##### Bool "AND"
SQL representation
```sql
Select * from world where Name = 'Australia'
```
ElasticSearch query:
```json
GET /world/_doc/_search
{
  "query": {
    "bool": {
      "must": [
        {
          "match": {
            "Name": "Australia"
          }
        }
    ]}
  }
}
```

##### Bool "OR"
SQL representation
```sql
Select * from world where Name = 'Australia' OR Name = 'New Zealand'
```

ElasticSearch Query

```json
GET /world/_doc/_search
{
  "query": {
    "bool": {
      "should": [
        {
          "match": {
            "Name": "Australia"
          }
        },
        {
          "match": {
            "Name": "New Zealand"
          }
        }
    ]}
  }
}
```

##### Bool "NOT"
SQL representation
```sql
Select * from world where Name != 'Australia'
```
ElasticSearch Query:

```json
GET /world/_doc/_search
{
  "query": {
    "bool": {
      "must_not": [
        {
          "match": {
            "Name": "Australia"
          }
        }
    ]}
  }
}
```

#### Get an item by ID

GET /world/_doc/ALB

#### Delete Index

DELETE /world

## Run query via cURL

```
curl -XGET -H 'Content-Type: application/json' 'localhost:9200/world/_doc/_search?pretty' -d '
{
  "query": {
    "bool": {
      "must": [
        {
          "match": {
            "Name": "Australia"
          }
        }
    ]}
  }
}'
```

## Unit testing
```
vendor/bin/phpunit
```
