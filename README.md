PostgreSearchBundle
===================================

<a target="_blank" href="http://www.postgresql.org/docs/9.1/static/textsearch.html">Full-text search PostgreSQL</a> in Doctrine 2.

Added type 'tsvector' to be used in the mapping.

Added functions 'to_tsquery' and 'ts_rank' to be used in the DQL.

### Step 1: Download the library using composer

```composer require ddmaster/postgre-search-bundle```

### Step 2: Configure Symfony

Add in your config.yml:

```yml
# Doctrine Configuration
doctrine:
    dbal:
        types:
            tsvector: Ddmaster\PostgreSearchBundle\Dbal\TsvectorType
        mapping_types:
            tsvector: tsvector
    orm:
        entity_managers:
            default:
                dql:
                    string_functions:
                        tsquery: Ddmaster\PostgreSearchBundle\DQL\TsqueryFunction
                        tsrank: Ddmaster\PostgreSearchBundle\DQL\TsrankFunction
                        plainto_tsquery: Ddmaster\PostgreSearchBundle\DQL\PlainToTsqueryFunction
```

### Step 4: Mapping example

```php
/**
 * @var string
 *
 * @ORM\Column(name="search_fts", type="tsvector", nullable=true)
 */
private $searchFts;
```

### Step 5: Use in DQL

```php
$searchQuery = 'family | history';
$em = $this->getDoctrine()->getManager();
$query = $em->createQuery(
    'SELECT b.id, sum(TSRANKCD(b.searchFts, :searchQuery)) as rank 
        FROM DemoSearchBundle:Books b
        WHERE TSQUERY( b.searchFts, :searchQuery ) = true
        GROUP BY b.id
        ORDER BY rank DESC')
    ->setParameter('searchQuery', $searchQuery)
;
$result = $query->getArrayResult();
```

Result example:

```yml
Array
(
    [0] => Array
        (
            [id] => 2
            [rank] => 0.0607927
        )
    [1] => Array
        (
            [id] => 3
            [rank] => 0.0303964
        )
)
```
