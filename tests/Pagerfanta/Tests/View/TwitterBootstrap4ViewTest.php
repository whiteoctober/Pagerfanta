<?php

namespace Pagerfanta\Tests\View;

use Pagerfanta\View\TwitterBootstrap4View;

class TwitterBootstrap4ViewTest extends TwitterBootstrapViewTest
{
    protected function createView()
    {
        return new TwitterBootstrap4View();
    }

    public function testRenderNormal()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(10);

        $options = array();

        $this->assertRenderedView(<<<EOF
<ul class="pagination">
    <li class="page-item prev"><a class="page-link" href="|9|">&larr; Previous</a></li>
    <li class="page-item"><a class="page-link" href="|1|">1</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|7|">7</a></li>
    <li class="page-item"><a class="page-link" href="|8|">8</a></li>
    <li class="page-item"><a class="page-link" href="|9|">9</a></li>
    <li class="page-item active"><span class="page-link">10 <span class="sr-only">(current)</span></span></li>
    <li class="page-item"><a class="page-link" href="|11|">11</a></li>
    <li class="page-item"><a class="page-link" href="|12|">12</a></li>
    <li class="page-item"><a class="page-link" href="|13|">13</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|100|">100</a></li>
    <li class="page-item next"><a class="page-link" href="|11|">Next &rarr;</a></li>
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
    <li class="page-item prev disabled"><span class="page-link">&larr; Previous</span></li>
    <li class="page-item active"><span class="page-link">1 <span class="sr-only">(current)</span></span></li>
    <li class="page-item"><a class="page-link" href="|2|">2</a></li>
    <li class="page-item"><a class="page-link" href="|3|">3</a></li>
    <li class="page-item"><a class="page-link" href="|4|">4</a></li>
    <li class="page-item"><a class="page-link" href="|5|">5</a></li>
    <li class="page-item"><a class="page-link" href="|6|">6</a></li>
    <li class="page-item"><a class="page-link" href="|7|">7</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|100|">100</a></li>
    <li class="page-item next"><a class="page-link" href="|2|">Next &rarr;</a></li>
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
    <li class="page-item prev"><a class="page-link" href="|99|">&larr; Previous</a></li>
    <li class="page-item"><a class="page-link" href="|1|">1</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|94|">94</a></li>
    <li class="page-item"><a class="page-link" href="|95|">95</a></li>
    <li class="page-item"><a class="page-link" href="|96|">96</a></li>
    <li class="page-item"><a class="page-link" href="|97|">97</a></li>
    <li class="page-item"><a class="page-link" href="|98|">98</a></li>
    <li class="page-item"><a class="page-link" href="|99|">99</a></li>
    <li class="page-item active"><span class="page-link">100 <span class="sr-only">(current)</span></span></li>
    <li class="page-item next disabled"><span class="page-link">Next &rarr;</span></li>
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
    <li class="page-item prev"><a class="page-link" href="|3|">&larr; Previous</a></li>
    <li class="page-item"><a class="page-link" href="|1|">1</a></li>
    <li class="page-item"><a class="page-link" href="|2|">2</a></li>
    <li class="page-item"><a class="page-link" href="|3|">3</a></li>
    <li class="page-item active"><span class="page-link">4 <span class="sr-only">(current)</span></span></li>
    <li class="page-item"><a class="page-link" href="|5|">5</a></li>
    <li class="page-item"><a class="page-link" href="|6|">6</a></li>
    <li class="page-item"><a class="page-link" href="|7|">7</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|100|">100</a></li>
    <li class="page-item next"><a class="page-link" href="|5|">Next &rarr;</a></li>
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
    <li class="page-item prev"><a class="page-link" href="|4|">&larr; Previous</a></li>
    <li class="page-item"><a class="page-link" href="|1|">1</a></li>
    <li class="page-item"><a class="page-link" href="|2|">2</a></li>
    <li class="page-item"><a class="page-link" href="|3|">3</a></li>
    <li class="page-item"><a class="page-link" href="|4|">4</a></li>
    <li class="page-item active"><span class="page-link">5 <span class="sr-only">(current)</span></span></li>
    <li class="page-item"><a class="page-link" href="|6|">6</a></li>
    <li class="page-item"><a class="page-link" href="|7|">7</a></li>
    <li class="page-item"><a class="page-link" href="|8|">8</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|100|">100</a></li>
    <li class="page-item next"><a class="page-link" href="|6|">Next &rarr;</a></li>
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
    <li class="page-item prev"><a class="page-link" href="|96|">&larr; Previous</a></li>
    <li class="page-item"><a class="page-link" href="|1|">1</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|94|">94</a></li>
    <li class="page-item"><a class="page-link" href="|95|">95</a></li>
    <li class="page-item"><a class="page-link" href="|96|">96</a></li>
    <li class="page-item active"><span class="page-link">97 <span class="sr-only">(current)</span></span></li>
    <li class="page-item"><a class="page-link" href="|98|">98</a></li>
    <li class="page-item"><a class="page-link" href="|99|">99</a></li>
    <li class="page-item"><a class="page-link" href="|100|">100</a></li>
    <li class="page-item next"><a class="page-link" href="|98|">Next &rarr;</a></li>
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
    <li class="page-item prev"><a class="page-link" href="|95|">&larr; Previous</a></li>
    <li class="page-item"><a class="page-link" href="|1|">1</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|93|">93</a></li>
    <li class="page-item"><a class="page-link" href="|94|">94</a></li>
    <li class="page-item"><a class="page-link" href="|95|">95</a></li>
    <li class="page-item active"><span class="page-link">96 <span class="sr-only">(current)</span></span></li>
    <li class="page-item"><a class="page-link" href="|97|">97</a></li>
    <li class="page-item"><a class="page-link" href="|98|">98</a></li>
    <li class="page-item"><a class="page-link" href="|99|">99</a></li>
    <li class="page-item"><a class="page-link" href="|100|">100</a></li>
    <li class="page-item next"><a class="page-link" href="|97|">Next &rarr;</a></li>
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
    <li class="page-item prev"><a class="page-link" href="|9|">&larr; Previous</a></li>
    <li class="page-item"><a class="page-link" href="|1|">1</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|8|">8</a></li>
    <li class="page-item"><a class="page-link" href="|9|">9</a></li>
    <li class="page-item active"><span class="page-link">10 <span class="sr-only">(current)</span></span></li>
    <li class="page-item"><a class="page-link" href="|11|">11</a></li>
    <li class="page-item"><a class="page-link" href="|12|">12</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|100|">100</a></li>
    <li class="page-item next"><a class="page-link" href="|11|">Next &rarr;</a></li>
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
            'next_message' => 'Siguiente',
        );

        $this->assertRenderedView(<<<EOF
<ul class="pagination">
    <li class="page-item prev"><a class="page-link" href="|9|">Anterior</a></li>
    <li class="page-item"><a class="page-link" href="|1|">1</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|7|">7</a></li>
    <li class="page-item"><a class="page-link" href="|8|">8</a></li>
    <li class="page-item"><a class="page-link" href="|9|">9</a></li>
    <li class="page-item active"><span class="page-link">10 <span class="sr-only">(current)</span></span></li>
    <li class="page-item"><a class="page-link" href="|11|">11</a></li>
    <li class="page-item"><a class="page-link" href="|12|">12</a></li>
    <li class="page-item"><a class="page-link" href="|13|">13</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|100|">100</a></li>
    <li class="page-item next"><a class="page-link" href="|11|">Siguiente</a></li>
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
            'css_active_class' => 'activo',
        );

        $this->assertRenderedView(<<<EOF
<ul class="paginacion">
    <li class="page-item anterior deshabilitado"><span class="page-link">&larr; Previous</span></li>
    <li class="page-item activo"><span class="page-link">1 <span class="sr-only">(current)</span></span></li>
    <li class="page-item"><a class="page-link" href="|2|">2</a></li>
    <li class="page-item"><a class="page-link" href="|3|">3</a></li>
    <li class="page-item"><a class="page-link" href="|4|">4</a></li>
    <li class="page-item"><a class="page-link" href="|5|">5</a></li>
    <li class="page-item"><a class="page-link" href="|6|">6</a></li>
    <li class="page-item"><a class="page-link" href="|7|">7</a></li>
    <li class="page-item puntos"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="|100|">100</a></li>
    <li class="page-item siguiente"><a class="page-link" href="|2|">Next &rarr;</a></li>
</ul>
EOF
            , $this->renderView($options));
    }
}
