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
class DefaultTemplate extends Template
{
    static protected $defaultOptions = array(
        'previous_message'        => 'Previous',
        'next_message'            => 'Next',
        'css_disabled_class'      => 'disabled',
        'css_dots_class'          => 'dots',
        'css_current_class'       => 'current',
        'dots_text'               => '...',
        'container_template'      => '<nav>%pages%</nav>',
        'page_with_text_template' => '<a href="%s">%s</a>',
        'span_template'           => '<span class="%s">%s</span>'
    );

    public function container()
    {
        return $this->option('container_template');
    }

    public function page($page)
    {
        $text = $page;

        return $this->pageWithText($page, $text);
    }

    public function pageWithText($page, $text)
    {
        return sprintf($this->option('page_with_text_template'), $this->generateRoute($page), $text);
    }

    public function previousDisabled()
    {
        return $this->generateSpan($this->option('css_disabled_class'), $this->option('previous_message'));
    }

    public function previousEnabled($page)
    {
        return $this->pageWithText($page, $this->option('previous_message'));
    }

    public function nextDisabled()
    {
        return $this->generateSpan($this->option('css_disabled_class'), $this->option('next_message'));
    }

    public function nextEnabled($page)
    {
        return $this->pageWithText($page, $this->option('next_message'));
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
        return $this->generateSpan($this->option('css_current_class'), $page);
    }

    public function separator()
    {
        return $this->generateSpan($this->option('css_dots_class'), $this->option('dots_text'));
    }

    private function generateSpan($class, $page)
    {
        return sprintf($this->option('span_template'), $class, $page);
    }
}