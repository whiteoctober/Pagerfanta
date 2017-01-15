# Pagerfanta

[![Build Status](https://travis-ci.org/whiteoctober/Pagerfanta.png?branch=master)](https://travis-ci.org/whiteoctober/Pagerfanta) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/whiteoctober/Pagerfanta/badges/quality-score.png?s=1ee480491644c07812b5206cf07d33a5035d0118)](https://scrutinizer-ci.com/g/whiteoctober/Pagerfanta/) [![Code Coverage](https://scrutinizer-ci.com/g/whiteoctober/Pagerfanta/badges/coverage.png?s=284be0616a9ba0439ee1123bcaf5fb3f6bfb0e50)](https://scrutinizer-ci.com/g/whiteoctober/Pagerfanta/) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/9e710230-b088-4904-baef-5f5e2d62e681/mini.png)](https://insight.sensiolabs.com/projects/9e710230-b088-4904-baef-5f5e2d62e681) [![Latest Stable Version](https://poser.pugx.org/pagerfanta/pagerfanta/v/stable.png)](https://packagist.org/packages/pagerfanta/pagerfanta) [![Total Downloads](https://poser.pugx.org/pagerfanta/pagerfanta/downloads.png)](https://packagist.org/packages/pagerfanta/pagerfanta)

Pagination for PHP 5.3

## Usage

```php
<?php

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

$adapter = new ArrayAdapter($array);
$pagerfanta = new Pagerfanta($adapter);

$pagerfanta->setMaxPerPage($maxPerPage); // 10 by default
$maxPerPage = $pagerfanta->getMaxPerPage();

$pagerfanta->setCurrentPage($currentPage); // 1 by default
$currentPage = $pagerfanta->getCurrentPage();

$nbResults = $pagerfanta->getNbResults();
$currentPageResults = $pagerfanta->getCurrentPageResults();

$pagerfanta->getNbPages();

$pagerfanta->haveToPaginate(); // whether the number of results if higher than the max per page

$pagerfanta->hasPreviousPage();
$pagerfanta->getPreviousPage();
$pagerfanta->hasNextPage();
$pagerfanta->getNextPage();
```

The `->setMaxPerPage()` and `->setCurrentPage()` methods implement
a fluent interface:

```php
<?php

$pagerfanta
    ->setMaxPerPage($maxPerPage)
    ->setCurrentPage($currentPage);
```

The `->setMaxPerPage()` method throws an exception if the max per page
is not valid:

  * `Pagerfanta\Exception\NotIntegerMaxPerPageException`
  * `Pagerfanta\Exception\LessThan1MaxPerPageException`

Both extend from `Pagerfanta\Exception\NotValidMaxPerPageException`.

The `->setCurrentPage()` method throws an exception if the page is not valid:

  * `Pagerfanta\Exception\NotIntegerCurrentPageException`
  * `Pagerfanta\Exception\LessThan1CurrentPageException`
  * `Pagerfanta\Exception\OutOfRangeCurrentPageException`

All of them extend from `Pagerfanta\Exception\NotValidCurrentPageException`.

`->setCurrentPage()` throws an out ot range exception depending on the
max per page, so if you are going to modify the max per page, you should do it
before setting the current page.

## Adapters

The adapter's concept is very simple. An adapter just returns the number
of results and an slice for a offset and length. This way you can adapt
a pagerfanta to paginate any kind results simply by creating an adapter.

An adapter must implement the `Pagerfanta\Adapter\AdapterInterface`
interface, which has these two methods:

```php
<?php

/**
 * Returns the number of results.
 *
 * @return integer The number of results.
 */
function getNbResults();

/**
 * Returns an slice of the results.
 *
 * @param integer $offset The offset.
 * @param integer $length The length.
 *
 * @return array|\Iterator|\IteratorAggregate The slice.
 */
function getSlice($offset, $length);
```

Pagerfanta comes with these adapters:

### ArrayAdapter

To paginate an array.

```php
<?php

use Pagerfanta\Adapter\ArrayAdapter;

$adapter = new ArrayAdapter($array);
```

### MongoAdapter

To paginate [Mongo](http://php.net/manual/en/book.mongo.php) Cursors.

```php
<?php

use Pagerfanta\Adapter\MongoAdapter;

$cursor = $collection->find();
$adapter = new MongoAdapter($cursor);
```

### MandangoAdapter

To paginate [Mandango](http://mandango.org) Queries.

```php
<?php

use Pagerfanta\Adapter\MandangoAdapter;

$query = $mandango->getRepository('Model\Article')->createQuery();
$adapter = new MandangoAdapter($query);
```

### DoctrineDbalAdapter

To paginate [DoctrineDbal](http://www.doctrine-project.org/projects/dbal.html)
query builders.

```php
<?php

use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Doctrine\DBAL\Query\QueryBuilder;

$queryBuilder = new QueryBuilder($conn);
$queryBuilder->select('p.*')->from('posts', 'p');

$countQueryBuilderModifier = function ($queryBuilder) {
    $queryBuilder->select('COUNT(DISTINCT p.id) AS total_results')
          ->setMaxResults(1);
};

$adapter = new DoctrineDbalAdapter($queryBuilder, $countQueryBuilderModifier);
```

### DoctrineDbalSingleTableAdapter

To simplify the pagination of single table
[DoctrineDbal](http://www.doctrine-project.org/projects/dbal.html)
query builders.

This adapter only paginates single table query builders, without joins.

```php
<?php

use Pagerfanta\Adapter\DoctrineDbalSingleTableAdapter;
use Doctrine\DBAL\Query\QueryBuilder;

$queryBuilder = new QueryBuilder($conn);
$queryBuilder->select('p.*')->from('posts', 'p');

$countField = 'p.id';

$adapter = new DoctrineDbalSingleTableAdapter($queryBuilder, $countField);
```

### DoctrineORMAdapter

To paginate [DoctrineORM](http://www.doctrine-project.org/projects/orm) query objects.

```php
<?php

use Pagerfanta\Adapter\DoctrineORMAdapter;

$queryBuilder = $entityManager->createQueryBuilder()
    ->select('u')
    ->from('Model\Article', 'u');
$adapter = new DoctrineORMAdapter($queryBuilder);
```

### DoctrineODMMongoDBAdapter

To paginate [DoctrineODMMongoDB](http://www.doctrine-project.org/docs/mongodb_odm/1.0/en/) query builders.

```php
<?php

use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;

$queryBuilder = $documentManager->createQueryBuilder('Model\Article');
$adapter = new DoctrineODMMongoDBAdapter($queryBuilder);
```

### DoctrineODMPhpcrAdapter

To paginate [Doctrine PHPCR-ODM](http://docs.doctrine-project.org/projects/doctrine-phpcr-odm/en/latest/) query builders.

```php
<?php

use Pagerfanta\Adapter\DoctrineODMPhpcrAdapter;

$queryBuilder = $documentManager->createQueryBuilder();
$queryBuilder->from('Model\Article');
$adapter = new DoctrineODMPhpcrAdapter($queryBuilder);
```

### DoctrineCollectionAdapter

To paginate a `Doctrine\Common\Collection\Collections` interface
you can use the `DoctrineCollectionAdapter`. It proxies to the
count() and slice() methods on the Collections interface for
pagination. This makes sense if you are using Doctrine ORMs Extra
Lazy association features:

```php
<?php

use Pagerfanta\Adapter\DoctrineCollectionAdapter;

$user = $em->find("Pagerfanta\Tests\Adapter\DoctrineORM\User", 1);

$adapter = new DoctrineCollectionAdapter($user->getGroups());
```

### DoctrineSelectableAdapter

To paginate a `Doctrine\Common\Collection\Selectable` interface
you can use the `DoctrineSelectableAdapter`. It uses the matching()
method on the Selectable interface for pagination. This is
especially usefull when using the Doctrine Criteria object to
filter a PersistentCollection:

```php
<?php

use Pagerfanta\Adapter\DoctrineSelectableAdapter;
use Doctrine\Common\Collections\Criteria;

$user = $em->find("Pagerfanta\Tests\Adapter\DoctrineORM\User", 1);
$comments = $user->getComments();
$criteria = Criteria::create()->andWhere(Criteria::expr()->in('id', array(1,2,3));

$adapter = new DoctrineSelectableAdapter($comments, $criteria);
```

Note that you should never use this adapter with a
PersistentCollection which is not set to use the EXTRA_LAZY fetch mode.

*Be carefull when using the `count()` method, currently Doctrine2
needs to fetch all the records to count the number of elements.*

### ElasticaAdapter

To paginate an Elastica Query query:

```php
<?php

use Elastica\Index;
use Elastica\Query;
use Elastica\Query\Term;
use Pagerfanta\Adapter\ElasticaAdapter;

// Searchable can be any valid searchable Elastica object. For example a Type or Index
$searchable = new Index($elasticaClient, 'index_name');
// A Query can be any valid Elastica query (json, array, Query object)
$query = new Query::create(new Term(array(
    'name' => 'Fred'
));

$adapter = new ElasticaAdapter($searchable, $query);
```

### PropelAdapter

To paginate a propel query:

```php
<?php

use Pagerfanta\Adapter\PropelAdapter;

$adapter = new PropelAdapter($query);
```

### SolariumAdapter

To paginate a [solarium](https://github.com/basdenooijer/solarium) query:

```php
<?php

use Pagerfanta\Adapter\SolariumAdapter;

$query = $solarium->createSelect();
$query->setQuery('search term');

$adapter = new SolariumAdapter($solarium, $query);
```

### FixedAdapter

Best used when you need to do a custom paging solution and
don't want to implement a full adapter for a one-off use case.

It returns always the same data no matter what page you query:

```php
<?php

use Pagerfanta\Adapter\FixedAdapter;

$nbResults = 5;
$results = array(/* ... */);

$adapter = new FixedAdapter($nbResults, $results);
```

## Views

Views are to render pagerfantas, this way you can reuse your
pagerfantas' html in several projects, share them and use another
ones from another developers.

The views implement the `Pagerfanta\View\ViewInterface` interface,
which has two methods:

```php
<?php

/**
 * Renders a pagerfanta.
 *
 * The route generator is any callable to generate the routes receiving the page number
 * as first and unique argument.
 *
 * @param PagerfantaInterface $pagerfanta     A pagerfanta.
 * @param mixed               $routeGenerator A callable to generate the routes.
 * @param array               $options        An array of options (optional).
 */
function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = array());

/**
 * Returns the canonical name.
 *
 * @return string The canonical name.
 */
function getName();
```

RouteGenerator example:

```php
<?php

$routeGenerator = function($page) {
    return '/path?page='.$page;
}
```

Pagerfanta comes with four views, the default one, one for
[Twitter Bootstrap](https://github.com/twitter/bootstrap), one for
[Semantic UI](https://github.com/Semantic-Org/Semantic-UI) and
an special optionable view.

### DefaultView

This is the default view.

```php
<?php

use Pagerfanta\View\DefaultView;

$view = new DefaultView();
$options = array('proximity' => 3);
$html = $view->render($pagerfanta, $routeGenerator, $options);
```

Options (default):

  * proximity (3)
  * prev_message (Previous)
  * next_message (Next)
  * css_disabled_class (disabled)
  * css_dots_class (dots)
  * css_current_class (current)
  * dots_text (...)
  * container_template (<nav>%pages%</nav>)
  * page_template (<a href="%href%">%text%</a>)
  * span_template (<span class="%class%">%text%</span>)

![Pagerfanta DefaultView](http://img813.imageshack.us/img813/601/pagerfanta.png)

CSS:

```css
.pagerfanta {
}

.pagerfanta a,
.pagerfanta span {
    display: inline-block;
    border: 1px solid blue;
    color: blue;
    margin-right: .2em;
    padding: .25em .35em;
}

.pagerfanta a {
    text-decoration: none;
}

.pagerfanta a:hover {
    background: #ccf;
}

.pagerfanta .dots {
    border-width: 0;
}

.pagerfanta .current {
    background: #ccf;
    font-weight: bold;
}

.pagerfanta .disabled {
    border-color: #ccf;
    color: #ccf;
}

COLORS:

.pagerfanta a,
.pagerfanta span {
    border-color: blue;
    color: blue;
}

.pagerfanta a:hover {
    background: #ccf;
}

.pagerfanta .current {
    background: #ccf;
}

.pagerfanta .disabled {
    border-color: #ccf;
    color: #cf;
}
```

### TwitterBootstrapView

This view generates a pagination for
[Twitter Bootstrap](https://github.com/twitter/bootstrap).

```php
<?php

use Pagerfanta\View\TwitterBootstrapView;

$view = new TwitterBootstrapView();
$options = array('proximity' => 3);
$html = $view->render($pagerfanta, $routeGenerator, $options);
```

Options (default):

  * proximity (3)
  * prev_message (&larr; Previous)
  * prev_disabled_href ()
  * next_message (Next &rarr;)
  * next_disabled_href ()
  * dots_message (&hellip;)
  * dots_href ()
  * css_container_class (pagination)
  * css_prev_class (prev)
  * css_next_class (next)
  * css_disabled_class (disabled)
  * css_dots_class (disabled)
  * css_active_class (active)

### SemanticUiView

This view generates a pagination for
[Semantic UI](https://github.com/Semantic-Org/Semantic-UI).

```php
<?php

use Pagerfanta\View\SemanticUiView;

$view = new SemanticUiView();
$options = array('proximity' => 3);
$html = $view->render($pagerfanta, $routeGenerator, $options);
```

Options (default):

  * proximity (3)
  * prev_message (&larr; Previous)
  * prev_disabled_href ()
  * next_message (Next &rarr;)
  * next_disabled_href ()
  * dots_message (&hellip;)
  * dots_href ()
  * css_container_class (pagination)
  * css_item_class (item)
  * css_prev_class (prev)
  * css_next_class (next)
  * css_disabled_class (disabled)
  * css_dots_class (disabled)
  * css_active_class (active)

### OptionableView

This view is to reuse options in different views.

```php
<?php

use Pagerfanta\DefaultView;
use Pagerfanta\OptionableView;

$defaultView = new DefaultView();

// view and default options
$myView1 = new OptionableView($defaultView, array('proximity' => 3));

$myView2 = new OptionableView($defaultView, array('prev_message' => 'Anterior', 'next_message' => 'Siguiente'));

// using in a normal way
$pagerfantaHtml = $myView2->render($pagerfanta, $routeGenerator);

// overwriting default options
$pagerfantaHtml = $myView2->render($pagerfanta, $routeGenerator, array('next_message' => 'Siguiente!!'));
```

## Todo

## Author

Pablo Díez - <pablodip@gmail.com>

## License

Pagerfanta is licensed under the MIT License. See the LICENSE file for full details.

## Sponsors

[WhiteOctober](http://www.whiteoctober.co.uk/)

## Acknowledgements

Pagerfanta is inspired by [Zend Paginator](https://github.com/zendframework/zf2).
