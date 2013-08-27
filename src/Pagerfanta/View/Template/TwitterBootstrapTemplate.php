<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pagerfanta\View\Template;

/**
 * @author Pablo Díez <pablodip@gmail.com>
 */
class TwitterBootstrapTemplate extends Template
{
    static protected $defaultOptions = array(
        'previous_message'       => '&larr; Previous',
        'previous_disabled_href' => '#',
        'next_message'           => 'Next &rarr;',
        'next_disabled_href'     => '#',
        'dots_message'           => '&hellip;',
        'dots_href'              => '#',
        'css_container_class'    => 'pagination',
        'css_previous_class'     => 'prev',
        'css_next_class'         => 'next',
        'css_disabled_class'     => 'disabled',
        'css_dots_class'         => 'disabled',
        'css_active_class'       => 'active'
    );

    public function container()
    {
        return sprintf('<div class="%s"><ul>%%pages%%</ul></div>',
            $this->option('css_container_class')
        );
    }

    public function page($page)
    {
        $text = $page;

        return $this->pageWithText($page, $text);
    }

    public function pageWithText($page, $text)
    {
        $class = null;

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    private function pageWithTextAndClass($page, $text, $class)
    {
        $href = $this->generateRoute($page);

        return $this->li($class, $href, $text);
    }

    public function previousDisabled()
    {
        $class = $this->previousDisabledClass();
        $href = $this->option('previous_disabled_href');
        $text = $this->option('previous_message');

        return $this->li($class, $href, $text);
    }

    private function previousDisabledClass()
    {
        return $this->option('css_previous_class').' '.$this->option('css_disabled_class');
    }

    public function previousEnabled($page)
    {
        $text = $this->option('previous_message');
        $class = $this->option('css_previous_class');

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    public function nextDisabled()
    {
        $class = $this->nextDisabledClass();
        $href = $this->option('next_disabled_href');
        $text = $this->option('next_message');

        return $this->li($class, $href, $text);
    }

    private function nextDisabledClass()
    {
        return $this->option('css_next_class').' '.$this->option('css_disabled_class');
    }

    public function nextEnabled($page)
    {
        $text = $this->option('next_message');
        $class = $this->option('css_next_class');

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    public function first()
    {
        return $this->page(1);
    }

    public function last($page)
    {
        return $this->page($page);
    }

    public function current($page)
    {
        $text = $page;
        $class = $this->option('css_active_class');

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    public function separator()
    {
        $class = $this->option('css_dots_class');
        $href = $this->option('dots_href');
        $text = $this->option('dots_message');

        return $this->li($class, $href, $text);
    }

    private function li($class, $href, $text)
    {
        $liClass = $class ? sprintf(' class="%s"', $class) : '';

        return sprintf('<li%s><a href="%s">%s</a></li>', $liClass, $href, $text);
    }
}