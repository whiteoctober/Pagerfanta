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
        'prev_message'        => '&larr; Previous',
        'next_message'        => 'Next &rarr;',
        'dots_message'        => '&hellip;',
        'active_suffix'       => '',
        'css_container_class' => 'pagination',
        'css_prev_class'      => 'prev',
        'css_next_class'      => 'next',
        'css_disabled_class'  => 'disabled',
        'css_dots_class'      => 'disabled',
        'css_active_class'    => 'active',
        'rel_previous'        => 'prev',
        'rel_next'            => 'next'
    );

    public function container()
    {
        return sprintf('<div><ul class="%s">%%pages%%</ul></div>',
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

    private function pageWithTextAndClass($page, $text, $class, $rel = null)
    {
        $href = $this->generateRoute($page);

        return $this->linkLi($class, $href, $text, $rel);
    }

    public function previousDisabled()
    {
        $class = $this->previousDisabledClass();
        $text = $this->option('prev_message');

        return $this->spanLi($class, $text);
    }

    private function previousDisabledClass()
    {
        return $this->option('css_prev_class').' '.$this->option('css_disabled_class');
    }

    public function previousEnabled($page)
    {
        $text = $this->option('prev_message');
        $class = $this->option('css_prev_class');
        $rel = $this->option('rel_previous');

        return $this->pageWithTextAndClass($page, $text, $class, $rel);
    }

    public function nextDisabled()
    {
        $class = $this->nextDisabledClass();
        $text = $this->option('next_message');

        return $this->spanLi($class, $text);
    }

    private function nextDisabledClass()
    {
        return $this->option('css_next_class').' '.$this->option('css_disabled_class');
    }

    public function nextEnabled($page)
    {
        $text = $this->option('next_message');
        $class = $this->option('css_next_class');
        $rel = $this->option('rel_next');

        return $this->pageWithTextAndClass($page, $text, $class, $rel);
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
        $text = trim($page.' '.$this->option('active_suffix'));
        $class = $this->option('css_active_class');

        return $this->spanLi($class, $text);
    }

    public function separator()
    {
        $class = $this->option('css_dots_class');
        $text = $this->option('dots_message');

        return $this->spanLi($class, $text);
    }

    protected function linkLi($class, $href, $text, $rel = null)
    {
        $liClass = $class ? sprintf(' class="%s"', $class) : '';
        $rel = $rel ? sprintf(' rel="%s"', $rel) : '';

        return sprintf('<li%s><a href="%s"%s>%s</a></li>', $liClass, $href, $rel, $text);
    }

    protected function spanLi($class, $text)
    {
        $liClass = $class ? sprintf(' class="%s"', $class) : '';

        return sprintf('<li%s><span>%s</span></li>', $liClass, $text);
    }
}
