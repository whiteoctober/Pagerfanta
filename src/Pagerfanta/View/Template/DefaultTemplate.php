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
        'previous_message'   => 'Previous',
        'next_message'       => 'Next',
        'css_disabled_class' => 'disabled',
        'css_dots_class'     => 'dots',
        'css_current_class'  => 'current',
        'dots_text'          => '...',
        'container_template' => '<nav>%pages%</nav>',
        'page_template'      => '<a href="%href%">%text%</a>',
        'span_template'      => '<span class="%class%">%text%</span>',
    );

    /**
     * @return mixed
     */
    public function container()
    {
        return $this->option('container_template');
    }

    /**
     * @param int $page
     *
     * @return mixed
     */
    public function page($page)
    {
        $text = $page;

        return $this->pageWithText($page, $text);
    }

    /**
     * @param int    $page
     * @param string $text
     *
     * @return mixed
     */
    public function pageWithText($page, $text)
    {
        $search = array('%href%', '%text%');

        $href = $this->generateRoute($page);
        $replace = array($href, $text);

        return str_replace($search, $replace, $this->option('page_template'));
    }

    /**
     * @return mixed
     */
    public function previousDisabled()
    {
        return $this->generateSpan($this->option('css_disabled_class'), $this->option('previous_message'));
    }

    /**
     * @param int $page
     *
     * @return mixed
     */
    public function previousEnabled($page)
    {
        return $this->pageWithText($page, $this->option('previous_message'));
    }

    /**
     * @return mixed
     */
    public function nextDisabled()
    {
        return $this->generateSpan($this->option('css_disabled_class'), $this->option('next_message'));
    }

    /**
     * @param int $page
     *
     * @return mixed
     */
    public function nextEnabled($page)
    {
        return $this->pageWithText($page, $this->option('next_message'));
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return $this->page(1);
    }

    /**
     * @param int $page
     *
     * @return mixed
     */
    public function last($page)
    {
        return $this->page($page);
    }

    /**
     * @param int $page
     *
     * @return mixed
     */
    public function current($page)
    {
        return $this->generateSpan($this->option('css_current_class'), $page);
    }

    /**
     * @return mixed
     */
    public function separator()
    {
        return $this->generateSpan($this->option('css_dots_class'), $this->option('dots_text'));
    }

    /**
     * @param $class
     * @param $page
     *
     * @return mixed
     */
    private function generateSpan($class, $page)
    {
        $search = array('%class%', '%text%');
        $replace = array($class, $page);

        return str_replace($search, $replace, $this->option('span_template'));
    }
}
