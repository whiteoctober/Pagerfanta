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

use Pagerfanta\View\Template\TwitterBootstrap3Template;

/**
 * TwitterBootstrap3View.
 *
 * View that can be used with the pagination module
 * from the Twitter Bootstrap3 CSS Toolkit
 * http://getbootstrap.com/
 *
 */
class TwitterBootstrap3View extends TwitterBootstrapView
{
    protected function createDefaultTemplate()
    {
        return new TwitterBootstrap3Template();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'twitter_bootstrap3';
    }
}
