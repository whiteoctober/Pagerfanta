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
 * OptionableView.
 *
 * This view renders another view with a default options to reuse them in a project.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class OptionableView implements ViewInterface
{
    private $view;
    private $defaultOptions;

    /**
     * Constructor.
     *
     * @param ViewInterface $view    A view.
     * @param array         $defaultOptions An array of default options.
     */
    public function __construct(ViewInterface $view, array $defaultOptions)
    {
        $this->view = $view;
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = array())
    {
        return $this->view->render($pagerfanta, $routeGenerator, array_merge($this->defaultOptions, $options));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'optionable';
    }
}
