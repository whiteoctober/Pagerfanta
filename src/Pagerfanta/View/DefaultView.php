<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pagerfanta\View;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\Template\TemplateInterface;
use Pagerfanta\View\Template\DefaultTemplate;

/**
 * @author Pablo Díez <pablodip@gmail.com>
 */
class DefaultView implements ViewInterface
{
    private $template;

    private $pagerfanta;
    private $proximity;

    private $currentPage;
    private $nbPages;

    private $startPage;
    private $endPage;

    public function __construct(TemplateInterface $template = null)
    {
        $this->template = $template ?: $this->createDefaultTemplate();
    }

    protected function createDefaultTemplate()
    {
        return new DefaultTemplate();
    }

    /**
     * {@inheritdoc}
     */
    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = array())
    {
        $this->initializePagerfanta($pagerfanta);
        $this->initializeOptions($options);

        $this->configureTemplate($routeGenerator, $options);

        return $this->generate();
    }

    private function initializePagerfanta($pagerfanta)
    {
        $this->pagerfanta = $pagerfanta;

        $this->currentPage = $pagerfanta->getCurrentPage();
        $this->nbPages = $pagerfanta->getNbPages();
    }

    private function initializeOptions($options)
    {
        $this->proximity = isset($options['proximity']) ?
                           (int) $options['proximity'] :
                           $this->getDefaultProximity();
    }

    protected function getDefaultProximity()
    {
        return 2;
    }

    private function configureTemplate($routeGenerator, $options)
    {
        $this->template->setRouteGenerator($routeGenerator);
        $this->template->setOptions($options);
    }

    private function generate()
    {
        $pages = $this->generatePages();

        return $this->generateContainer($pages);
    }

    private function generateContainer($pages)
    {
        return str_replace('%pages%', $pages, $this->template->container());
    }

    private function generatePages()
    {
        $this->calculateStartAndEndPage();

        return $this->previous().
               $this->first().
               $this->secondIfStartIs3().
               $this->dotsIfStartIsOver3().
               $this->pages().
               $this->dotsIfEndIsUnder3ToLast().
               $this->secondToLastIfEndIs3ToLast().
               $this->last().
               $this->next();
    }

    private function calculateStartAndEndPage()
    {
        $startPage = $this->currentPage - $this->proximity;
        $endPage = $this->currentPage + $this->proximity;

        if ($this->startPageUnderflow($startPage)) {
            $endPage = $this->calculateEndPageForStartPageUnderflow($startPage, $endPage);
            $startPage = 1;
        }
        if ($this->endPageOverflow($endPage)) {
            $startPage = $this->calculateStartPageForEndPageOverflow($startPage, $endPage);
            $endPage = $this->nbPages;
        }

        $this->startPage = $startPage;
        $this->endPage = $endPage;
    }

    private function startPageUnderflow($startPage)
    {
        return $startPage < 1;
    }

    private function endPageOverflow($endPage)
    {
        return $endPage > $this->nbPages;
    }

    private function calculateEndPageForStartPageUnderflow($startPage, $endPage)
    {
        return min($endPage + (1 - $startPage), $this->nbPages);
    }

    private function calculateStartPageForEndPageOverflow($startPage, $endPage)
    {
        return max($startPage - ($endPage - $this->nbPages), 1);
    }

    private function previous()
    {
        if ($this->pagerfanta->hasPreviousPage()) {
            return $this->template->previousEnabled($this->pagerfanta->getPreviousPage());
        }

        return $this->template->previousDisabled();
    }

    private function first()
    {
        if ($this->startPage > 1) {
            return $this->template->first();
        }
    }

    private function secondIfStartIs3()
    {
        if ($this->startPage == 3) {
            return $this->template->page(2);
        }
    }

    private function dotsIfStartIsOver3()
    {
        if ($this->startPage > 3) {
            return $this->template->separator();
        }
    }

    private function pages()
    {
        $pages = '';

        foreach (range($this->startPage, $this->endPage) as $page) {
            $pages .= $this->page($page);
        }

        return $pages;
    }

    private function page($page)
    {
        if ($page == $this->currentPage) {
            return $this->template->current($page);
        }

        return $this->template->page($page);
    }

    private function dotsIfEndIsUnder3ToLast()
    {
        if ($this->endPage < $this->toLast(3)) {
            return $this->template->separator();
        }
    }

    private function secondToLastIfEndIs3ToLast()
    {
        if ($this->endPage == $this->toLast(3)) {
            return $this->template->page($this->toLast(2));
        }
    }

    private function toLast($n)
    {
        return $this->pagerfanta->getNbPages() - ($n - 1);
    }

    private function last()
    {
        if ($this->pagerfanta->getNbPages() > $this->endPage) {
            return $this->template->last($this->pagerfanta->getNbPages());
        }
    }

    private function next()
    {
        if ($this->pagerfanta->hasNextPage()) {
            return $this->template->nextEnabled($this->pagerfanta->getNextPage());
        }

        return $this->template->nextDisabled();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'default';
    }
}

/*

CSS:

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

*/
