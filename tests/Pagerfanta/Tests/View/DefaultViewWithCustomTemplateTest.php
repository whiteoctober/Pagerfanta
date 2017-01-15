<?php

namespace Pagerfanta\Tests\View;

use Pagerfanta\View\DefaultView;
use Pagerfanta\View\Template\TwitterBootstrapTemplate;

class DefaultViewWithCustomTemplateTest extends ViewTestCase
{
    protected function createView()
    {
        $template = new TwitterBootstrapTemplate();

        return new DefaultView($template);
    }

    public function testRenderNormal()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(10);

        $options = array();

        $this->assertRenderedView(<<<EOF
<div class="pagination">
    <ul>
        <li class="prev"><a href="|9|" rel="prev">&larr; Previous</a></li>
        <li><a href="|1|">1</a></li>
        <li class="disabled"><span>&hellip;</span></li>
        <li><a href="|8|">8</a></li>
        <li><a href="|9|">9</a></li>
        <li class="active"><span>10</span></li>
        <li><a href="|11|">11</a></li>
        <li><a href="|12|">12</a></li>
        <li class="disabled"><span>&hellip;</span></li>
        <li><a href="|100|">100</a></li>
        <li class="next"><a href="|11|" rel="next">Next &rarr;</a></li>
    </ul>
</div>
EOF
        , $this->renderView($options));
    }

    protected function filterExpectedView($expected)
    {
        return $this->removeWhitespacesBetweenTags($expected);
    }
}
