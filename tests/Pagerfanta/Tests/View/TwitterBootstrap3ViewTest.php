<?php

namespace Pagerfanta\Tests\View;

use Pagerfanta\View\TwitterBootstrap3View;
use Pagerfanta\Tests\View\TwitterBootstrapViewTest;

class TwitterBootstrap3ViewTest extends TwitterBootstrapViewTest
{

    protected function createView()
    {
        return new TwitterBootstrap3View();
    }

    public function testRenderNormal()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(10);

        $options = array();

        $this->assertRenderedView(<<<EOF
<ul class="pagination">
    <li class="prev"><a href="|9|">&larr; Previous</a></li>
    <li><a href="|1|">1</a></li>
    <li class="disabled"><a href="#">&hellip;</a></li>
    <li><a href="|7|">7</a></li>
    <li><a href="|8|">8</a></li>
    <li><a href="|9|">9</a></li>
    <li class="active"><a href="|10|">10</a></li>
    <li><a href="|11|">11</a></li>
    <li><a href="|12|">12</a></li>
    <li><a href="|13|">13</a></li>
    <li class="disabled"><a href="#">&hellip;</a></li>
    <li><a href="|100|">100</a></li>
    <li class="next"><a href="|11|">Next &rarr;</a></li>
</ul>
EOF
            , $this->renderView($options));
    }

    public function testRenderFirstPage()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(1);

        $options = array();

        $this->assertRenderedView(<<<EOF
<ul class="pagination">
    <li class="prev disabled"><a href="#">&larr; Previous</a></li>
    <li class="active"><a href="|1|">1</a></li>
    <li><a href="|2|">2</a></li>
    <li><a href="|3|">3</a></li>
    <li><a href="|4|">4</a></li>
    <li><a href="|5|">5</a></li>
    <li><a href="|6|">6</a></li>
    <li><a href="|7|">7</a></li>
    <li class="disabled"><a href="#">&hellip;</a></li>
    <li><a href="|100|">100</a></li>
    <li class="next"><a href="|2|">Next &rarr;</a></li>
</ul>
EOF
            , $this->renderView($options));
    }

    public function testRenderLastPage()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(100);

        $options = array();

        $this->assertRenderedView(<<<EOF
<ul class="pagination">
    <li class="prev"><a href="|99|">&larr; Previous</a></li>
    <li><a href="|1|">1</a></li>
    <li class="disabled"><a href="#">&hellip;</a></li>
    <li><a href="|94|">94</a></li>
    <li><a href="|95|">95</a></li>
    <li><a href="|96|">96</a></li>
    <li><a href="|97|">97</a></li>
    <li><a href="|98|">98</a></li>
    <li><a href="|99|">99</a></li>
    <li class="active"><a href="|100|">100</a></li>
    <li class="next disabled"><a href="#">Next &rarr;</a></li>
</ul>
EOF
            , $this->renderView($options));
    }

    public function testRenderWhenStartProximityIs2()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(4);

        $options = array();

        $this->assertRenderedView(<<<EOF
<ul class="pagination">
    <li class="prev"><a href="|3|">&larr; Previous</a></li>
    <li><a href="|1|">1</a></li>
    <li><a href="|2|">2</a></li>
    <li><a href="|3|">3</a></li>
    <li class="active"><a href="|4|">4</a></li>
    <li><a href="|5|">5</a></li>
    <li><a href="|6|">6</a></li>
    <li><a href="|7|">7</a></li>
    <li class="disabled"><a href="#">&hellip;</a></li>
    <li><a href="|100|">100</a></li>
    <li class="next"><a href="|5|">Next &rarr;</a></li>
</ul>
EOF
            , $this->renderView($options));
    }

    public function testRenderWhenStartProximityIs3()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(5);

        $options = array();

        $this->assertRenderedView(<<<EOF
<ul class="pagination">
    <li class="prev"><a href="|4|">&larr; Previous</a></li>
    <li><a href="|1|">1</a></li>
    <li><a href="|2|">2</a></li>
    <li><a href="|3|">3</a></li>
    <li><a href="|4|">4</a></li>
    <li class="active"><a href="|5|">5</a></li>
    <li><a href="|6|">6</a></li>
    <li><a href="|7|">7</a></li>
    <li><a href="|8|">8</a></li>
    <li class="disabled"><a href="#">&hellip;</a></li>
    <li><a href="|100|">100</a></li>
    <li class="next"><a href="|6|">Next &rarr;</a></li>
</ul>
EOF
            , $this->renderView($options));
    }

    public function testRenderWhenEndProximityIs2FromLast()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(97);

        $options = array();

        $this->assertRenderedView(<<<EOF
<ul class="pagination">
    <li class="prev"><a href="|96|">&larr; Previous</a></li>
    <li><a href="|1|">1</a></li>
    <li class="disabled"><a href="#">&hellip;</a></li>
    <li><a href="|94|">94</a></li>
    <li><a href="|95|">95</a></li>
    <li><a href="|96|">96</a></li>
    <li class="active"><a href="|97|">97</a></li>
    <li><a href="|98|">98</a></li>
    <li><a href="|99|">99</a></li>
    <li><a href="|100|">100</a></li>
    <li class="next"><a href="|98|">Next &rarr;</a></li>
</ul>
EOF
            , $this->renderView($options));
    }

    public function testRenderWhenEndProximityIs3FromLast()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(96);

        $options = array();

        $this->assertRenderedView(<<<EOF
<ul class="pagination">
    <li class="prev"><a href="|95|">&larr; Previous</a></li>
    <li><a href="|1|">1</a></li>
    <li class="disabled"><a href="#">&hellip;</a></li>
    <li><a href="|93|">93</a></li>
    <li><a href="|94|">94</a></li>
    <li><a href="|95|">95</a></li>
    <li class="active"><a href="|96|">96</a></li>
    <li><a href="|97|">97</a></li>
    <li><a href="|98|">98</a></li>
    <li><a href="|99|">99</a></li>
    <li><a href="|100|">100</a></li>
    <li class="next"><a href="|97|">Next &rarr;</a></li>
</ul>
EOF
            , $this->renderView($options));
    }

    public function testRenderModifyingProximity()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(10);

        $options = array('proximity' => 2);

        $this->assertRenderedView(<<<EOF
<ul class="pagination">
    <li class="prev"><a href="|9|">&larr; Previous</a></li>
    <li><a href="|1|">1</a></li>
    <li class="disabled"><a href="#">&hellip;</a></li>
    <li><a href="|8|">8</a></li>
    <li><a href="|9|">9</a></li>
    <li class="active"><a href="|10|">10</a></li>
    <li><a href="|11|">11</a></li>
    <li><a href="|12|">12</a></li>
    <li class="disabled"><a href="#">&hellip;</a></li>
    <li><a href="|100|">100</a></li>
    <li class="next"><a href="|11|">Next &rarr;</a></li>
</ul>
EOF
            , $this->renderView($options));
    }

    public function testRenderModifyingPreviousAndNextMessages()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(10);

        $options = array(
            'prev_message' => 'Anterior',
            'next_message' => 'Siguiente'
        );

        $this->assertRenderedView(<<<EOF
<ul class="pagination">
    <li class="prev"><a href="|9|">Anterior</a></li>
    <li><a href="|1|">1</a></li>
    <li class="disabled"><a href="#">&hellip;</a></li>
    <li><a href="|7|">7</a></li>
    <li><a href="|8|">8</a></li>
    <li><a href="|9|">9</a></li>
    <li class="active"><a href="|10|">10</a></li>
    <li><a href="|11|">11</a></li>
    <li><a href="|12|">12</a></li>
    <li><a href="|13|">13</a></li>
    <li class="disabled"><a href="#">&hellip;</a></li>
    <li><a href="|100|">100</a></li>
    <li class="next"><a href="|11|">Siguiente</a></li>
</ul>
EOF
            , $this->renderView($options));
    }

    public function testRenderModifyingCssClasses()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(1);

        $options = array(
            'css_container_class' => 'paginacion',
            'css_prev_class' => 'anterior',
            'css_next_class' => 'siguiente',
            'css_disabled_class' => 'deshabilitado',
            'css_dots_class' => 'puntos',
            'css_active_class' => 'activo'
        );

        $this->assertRenderedView(<<<EOF
<ul class="paginacion">
    <li class="anterior deshabilitado"><a href="#">&larr; Previous</a></li>
    <li class="activo"><a href="|1|">1</a></li>
    <li><a href="|2|">2</a></li>
    <li><a href="|3|">3</a></li>
    <li><a href="|4|">4</a></li>
    <li><a href="|5|">5</a></li>
    <li><a href="|6|">6</a></li>
    <li><a href="|7|">7</a></li>
    <li class="puntos"><a href="#">&hellip;</a></li>
    <li><a href="|100|">100</a></li>
    <li class="siguiente"><a href="|2|">Next &rarr;</a></li>
</ul>
EOF
            , $this->renderView($options));
    }

}
