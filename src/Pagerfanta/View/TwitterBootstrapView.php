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

/**
 * TwitterBootstrapView.
 *
 * View that can be used with the pagination module 
 * from the Twitter Bootstrap CSS Toolkit
 * http://twitter.github.com/bootstrap/
 *
 * @author Pablo Díez <pablodip@gmail.com>
 * @author Jan Sorgalla <jsorgalla@gmail.com>
 *
 * @api
 */
class TwitterBootstrapView implements ViewInterface
{
    /**
     * {@inheritdoc}
     */
    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = array())
    {
        $options = array_merge(array(
            'proximity'           => 3,
            'prev_message'        => '&larr; Previous',
            'prev_disabled_href'  => '',
            'next_message'        => 'Next &rarr;',
            'next_disabled_href'  => '',
            'dots_message'        => '&hellip;',
            'dots_href'           => '',
            'css_container_class' => 'pagination',
            'css_prev_class'      => 'prev',
            'css_next_class'      => 'next',
            'css_disabled_class'  => 'disabled',
            'css_dots_class'      => 'disabled',
            'css_active_class'    => 'active',
        ), $options);

        $currentPage = $pagerfanta->getCurrentPage();

        $startPage = $currentPage - $options['proximity'];
        $endPage = $currentPage + $options['proximity'];

        if ($startPage < 1) {
            $endPage = min($endPage + (1 - $startPage), $pagerfanta->getNbPages());
            $startPage = 1;
        }
        if ($endPage > $pagerfanta->getNbPages()) {
            $startPage = max($startPage - ($endPage - $pagerfanta->getNbPages()), 1);
            $endPage = $pagerfanta->getNbPages();
        }

        $pages = array();

        // previous
        $class = $options['css_prev_class'];
        $url   = $options['prev_disabled_href'];
        if (!$pagerfanta->hasPreviousPage()) {
            $class .= ' '.$options['css_disabled_class'];
        } else {
            $url = $routeGenerator($pagerfanta->getPreviousPage());
        }

        $pages[] = sprintf('<li class="%s"><a href="%s">%s</a></li>', $class, $url, $options['prev_message']);


        // first
        if ($startPage > 1) {
            $pages[] = sprintf('<li><a href="%s">%s</a></li>', $routeGenerator(1), 1);
            if (3 == $startPage) {
                $pages[] = sprintf('<li><a href="%s">%s</a></li>', $routeGenerator(2), 2);
            } elseif (2 != $startPage) {
                $pages[] = sprintf('<li class="%s"><a href="%s">%s</a></li>', $options['css_dots_class'], $options['dots_href'], $options['dots_message']);
            }
        }

        // pages
        for ($page = $startPage; $page <= $endPage; $page++) {
            $class = '';
            if ($page == $currentPage) {
                $class = sprintf(' class="%s"', $options['css_active_class']);
            }

            $pages[] = sprintf('<li%s><a href="%s">%s</a></li>', $class, $routeGenerator($page), $page);
        }

        // last
        if ($pagerfanta->getNbPages() > $endPage) {
            if ($pagerfanta->getNbPages() > ($endPage + 1)) {
                if ($pagerfanta->getNbPages() > ($endPage + 2)) {
                    $pages[] = sprintf('<li class="%s"><a href="%s">%s</a></li>', $options['css_dots_class'], $options['dots_href'], $options['dots_message']);
                } else {
                    $pages[] = sprintf('<li><a href="%s">%s</a></li>', $routeGenerator($endPage + 1), $endPage + 1);
                }
            }

            $pages[] = sprintf('<li><a href="%s">%s</a></li>', $routeGenerator($pagerfanta->getNbPages()), $pagerfanta->getNbPages());
        }

        // next
        $class = $options['css_next_class'];
        $url   = $options['next_disabled_href'];
        if (!$pagerfanta->hasNextPage()) {
            $class .= ' '.$options['css_disabled_class'];
        } else {
            $url = $routeGenerator($pagerfanta->getNextPage());
        }

        $pages[] = sprintf('<li class="%s"><a href="%s">%s</a></li>', $class, $url, $options['next_message']);

        return sprintf('<div class="%s"><ul>%s</ul></div>', $options['css_container_class'], implode('', $pages));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'twitter_bootstrap';
    }
}
