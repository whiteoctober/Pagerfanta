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
 * TwitterBootstrap4Template
 */
class TwitterBootstrap4Template extends TwitterBootstrap3Template
{
    protected function linkLi($class, $href, $text)
    {
        $liClass = implode(' ', array_filter(array('page-item', $class)));

        return sprintf('<li class="%s"><a class="page-link" href="%s">%s</a></li>', $liClass, $href, $text);
    }

    protected function spanLi($class, $text)
    {
        $liClass = implode(' ', array_filter(array('page-item', $class)));
        
        return sprintf('<li class="%s"><span class="page-link">%s</span></li>', $liClass, $text);
    }
}
