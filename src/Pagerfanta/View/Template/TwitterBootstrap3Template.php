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

use Pagerfanta\View\Template\TwitterBootstrapTemplate;

/**
 * TwitterBootstrap3Template
 */
class TwitterBootstrap3Template extends TwitterBootstrapTemplate
{

    public function container()
    {
        return sprintf('<ul class="%s">%%pages%%</ul>', $this->option('css_container_class')
        );
    }

}

