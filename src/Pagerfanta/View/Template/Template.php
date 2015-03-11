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
abstract class Template implements TemplateInterface
{
    protected static $defaultOptions = array();

    private $routeGenerator;
    private $options;

    public function __construct()
    {
        $this->initializeOptions();
    }

    public function setRouteGenerator($routeGenerator)
    {
        $this->routeGenerator = $routeGenerator;
    }

    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    private function initializeOptions()
    {
        $this->options = static::$defaultOptions;
    }

    protected function generateRoute($page)
    {
        return call_user_func($this->getRouteGenerator(), $page);
    }

    private function getRouteGenerator()
    {
        if (!$this->routeGenerator) {
            throw new \RuntimeException('There is no route generator.');
        }

        return $this->routeGenerator;
    }

    protected function option($name)
    {
        if (!isset($this->options[$name])) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
        }

        return $this->options[$name];
    }
}
