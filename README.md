# Pagerfanta

[![Build Status](https://secure.travis-ci.org/whiteoctober/Pagerfanta.png)](http://travis-ci.org/whiteoctober/Pagerfanta)

Pagination for PHP 5.3

## Usage

```php
<?php

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;

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

The `->setMaxPerPage()` and `->setCurrentPage()` methods implement a fluent interface:

```php
<?php

$pagerfanta
    ->setMaxPerPage($maxPerPage)
    ->setCurrentPage($currentPage)
;
```

The `->setMaxPerPage()` method throws an exception if the max per page is not valid:

  * `Pagerfanta\Exception\NotIntegerMaxPerPageException` (including integer in string)
  * `Pagerfanta\Exception\LessThan1MaxPerPageException`

Both extends from `Pagerfanta\Exception\NotValidMaxPerPageException`.

The `->setCurrentPage()` method throws an exception if the page is not valid:

  * `Pagerfanta\Exception\NotIntegerCurrentPageException` (including integer in string)
  * `Pagerfanta\Exception\LessThan1CurrentPageException`
  * `Pagerfanta\Exception\OutOfRangeCurrentPageException`

All of them extends from `Pagerfanta\Exception\NotValidCurrentPageException`.

## Adapters

The adapter's concept is very simple. An adapter just returns the number of results and an slice for a offset and length. This way you can adapt a pagerfanta to paginate any kind results simply creating an adapter.

An adapter must implement the `Pagerfanta\Adapter\AdapterInterface` interface, which has these two methods:

```php
<?php

/**
 * Returns the number of results.
 *
 * @return integer The number of results.
 *
 * @api
 */
function getNbResults();

/**
 * Returns an slice of the results.
 *
 * @param integer $offset The offset.
 * @param integer $length The length.
 *
 * @return array|\Iterator|\IteratorAggregate The slice.
 *
 * @api
 */
function getSlice($offset, $length);
```

Pagerfanta comes with eight adapters:

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

$query = \Model\Article::getRepository()->createQuery();
$adapter = new MandangoAdapter($query);
```

### DoctrineORMAdapter

To paginate [DoctrineORM](http://www.doctrine-project.org/projects/orm) query objects.

```php
<?php

use Pagerfanta\Adapter\DoctrineORMAdapter;

$queryBuilder = $entityManager->createQueryBuilder()
    ->select('u')
    ->from('Model\Article', 'u')
;
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

### DoctrineCollectionAdapter

To paginate a `Doctrine\Common\Collection\Collections` interface you can use the `DoctrineCollectionAdapter`. It proxies to the count() and slice() methods on the Collections interface for pagination. This makes sense if you are using Doctrine ORMs Extra Lazy association features:

```php
<?php

use Pagerfanta\Adapter\DoctrineCollectionAdapter;

$user = $em->find("Pagerfanta\Tests\Adapter\DoctrineORM\User", 1);

$adapter = new DoctrineCollectionAdapter($user->getGroups());
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

## Views

The views are to render pagerfantas, this way you can reuse your pagerfantas' html in several projects, share them and use another ones from another developers.

The views implement the `Pagerfanta\View\ViewInterface` interface, which has two methods:

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
 *
 * @api
 */
function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = array());

/**
 * Returns the canonical name.
 *
 * @return string The canonical name.
 *
 * @api
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

Pagerfanta comes with three views, the default one, one for [Twitter Bootstrap](https://github.com/twitter/bootstrap) and an special optionable view.

### DefaultView

This is the default view.

```php
<?php

use Pagerfanta\View\DefaultView;

$view = new DefaultView();
$html = $view->render($pagerfanta, $routeGenerator, array(
    'proximity' => 3,
));
```

Options (default):

  * proximity (3)
  * previous_message (Previous)
  * next_message (Next)
  * css_disabled_class (disabled)
  * css_dots_class (dots)
  * css_current_class (current)

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

This view generates a pagination for [Twitter Bootstrap](https://github.com/twitter/bootstrap).

```php
<?php

use Pagerfanta\View\TwitterBootstrapView;

$view = new TwitterBootstrapView();
$html = $view->render($pagerfanta, $routeGenerator, array(
    'proximity' => 3,
));
```

Options (default):

  * proximity (3)
  * prev_message (&larr; Previous)
  * prev_disabled_href ()
  * next_message (Next)
  * next_disabled_href ()
  * dots_message (&hellip;)
  * dots_href ()
  * css_disabled_class (pagination)
  * css_previous_class (prev)
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

$myView2 = new OptionableView($defaultView, array('previous_message' => 'Anterior', 'next_message' => 'Siguiente'));

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
