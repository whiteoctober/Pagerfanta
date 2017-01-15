<?php

namespace Pagerfanta\Tests\View;

use Pagerfanta\View\SemanticUiView;

/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
class SemanticUiViewTest extends ViewTestCase
{
    protected function createView()
    {
        return new SemanticUiView();
    }

    public function testRenderNormal()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(10);

        $options = array();

        $this->assertRenderedView(<<<EOF
<div class="ui stackable fluid pagination menu">
    <a class="item prev" href="|9|">&larr; Previous</a>
    <a class="item " href="|1|">1</a>
    <div class="item disabled">&hellip;</div>
    <a class="item " href="|7|">7</a>
    <a class="item " href="|8|">8</a>
    <a class="item " href="|9|">9</a>
    <div class="item active">10</div>
    <a class="item " href="|11|">11</a>
    <a class="item " href="|12|">12</a>
    <a class="item " href="|13|">13</a>
    <div class="item disabled">&hellip;</div>
    <a class="item " href="|100|">100</a>
    <a class="item next" href="|11|">Next &rarr;</a>
</div>
EOF
            , $this->renderView($options));
    }

    public function testRenderFirstPage()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(1);

        $options = array();

        $this->assertRenderedView(<<<EOF
<div class="ui stackable fluid pagination menu">
    <div class="item prev disabled">&larr; Previous</div>
    <div class="item active">1</div>
    <a class="item " href="|2|">2</a>
    <a class="item " href="|3|">3</a>
    <a class="item " href="|4|">4</a>
    <a class="item " href="|5|">5</a>
    <a class="item " href="|6|">6</a>
    <a class="item " href="|7|">7</a>
    <div class="item disabled">&hellip;</div>
    <a class="item " href="|100|">100</a>
    <a class="item next" href="|2|">Next &rarr;</a>
</div>
EOF
            , $this->renderView($options));
    }

    public function testRenderLastPage()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(100);

        $options = array();

        $this->assertRenderedView(<<<EOF
<div class="ui stackable fluid pagination menu">
    <a class="item prev" href="|99|">&larr; Previous</a>
    <a class="item " href="|1|">1</a>
    <div class="item disabled">&hellip;</div>
    <a class="item " href="|94|">94</a>
    <a class="item " href="|95|">95</a>
    <a class="item " href="|96|">96</a>
    <a class="item " href="|97|">97</a>
    <a class="item " href="|98|">98</a>
    <a class="item " href="|99|">99</a>
    <div class="item active">100</div>
    <div class="item next disabled">Next &rarr;</div>
</div>
EOF
            , $this->renderView($options));
    }

    public function testRenderWhenStartProximityIs2()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(4);

        $options = array();

        $this->assertRenderedView(<<<EOF
<div class="ui stackable fluid pagination menu">
    <a class="item prev" href="|3|">&larr; Previous</a>
    <a class="item " href="|1|">1</a><a class="item " href="|2|">2</a>
    <a class="item " href="|3|">3</a>
    <div class="item active">4</div>
    <a class="item " href="|5|">5</a>
    <a class="item " href="|6|">6</a>
    <a class="item " href="|7|">7</a>
    <div class="item disabled">&hellip;</div>
    <a class="item " href="|100|">100</a>
    <a class="item next" href="|5|">Next &rarr;</a>
</div>
EOF
            , $this->renderView($options));
    }

    public function testRenderWhenStartProximityIs3()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(5);

        $options = array();

        $this->assertRenderedView(<<<EOF
<div class="ui stackable fluid pagination menu">
    <a class="item prev" href="|4|">&larr; Previous</a>
    <a class="item " href="|1|">1</a>
    <a class="item " href="|2|">2</a>
    <a class="item " href="|3|">3</a>
    <a class="item " href="|4|">4</a>
    <div class="item active">5</div>
    <a class="item " href="|6|">6</a>
    <a class="item " href="|7|">7</a>
    <a class="item " href="|8|">8</a>
    <div class="item disabled">&hellip;</div>
    <a class="item " href="|100|">100</a>
    <a class="item next" href="|6|">Next &rarr;</a>
</div>
EOF
            , $this->renderView($options));
    }

    public function testRenderWhenEndProximityIs2FromLast()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(97);

        $options = array();

        $this->assertRenderedView(<<<EOF
<div class="ui stackable fluid pagination menu">
    <a class="item prev" href="|96|">&larr; Previous</a>
    <a class="item " href="|1|">1</a>
    <div class="item disabled">&hellip;</div>
    <a class="item " href="|94|">94</a>
    <a class="item " href="|95|">95</a>
    <a class="item " href="|96|">96</a>
    <div class="item active">97</div>
    <a class="item " href="|98|">98</a>
    <a class="item " href="|99|">99</a>
    <a class="item " href="|100|">100</a>
    <a class="item next" href="|98|">Next &rarr;</a>
</div>
EOF
            , $this->renderView($options));
    }

    public function testRenderWhenEndProximityIs3FromLast()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(96);

        $options = array();

        $this->assertRenderedView(<<<EOF
<div class="ui stackable fluid pagination menu">
    <a class="item prev" href="|95|">&larr; Previous</a>
    <a class="item " href="|1|">1</a>
    <div class="item disabled">&hellip;</div>
    <a class="item " href="|93|">93</a>
    <a class="item " href="|94|">94</a>
    <a class="item " href="|95|">95</a>
    <div class="item active">96</div>
    <a class="item " href="|97|">97</a>
    <a class="item " href="|98|">98</a>
    <a class="item " href="|99|">99</a>
    <a class="item " href="|100|">100</a>
    <a class="item next" href="|97|">Next &rarr;</a>
</div>
EOF
            , $this->renderView($options));
    }

    public function testRenderModifyingProximity()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(10);

        $options = array('proximity' => 2);

        $this->assertRenderedView(<<<EOF
<div class="ui stackable fluid pagination menu">
    <a class="item prev" href="|9|">&larr; Previous</a>
    <a class="item " href="|1|">1</a>
    <div class="item disabled">&hellip;</div>
    <a class="item " href="|8|">8</a>
    <a class="item " href="|9|">9</a>
    <div class="item active">10</div>
    <a class="item " href="|11|">11</a>
    <a class="item " href="|12|">12</a>
    <div class="item disabled">&hellip;</div>
    <a class="item " href="|100|">100</a>
    <a class="item next" href="|11|">Next &rarr;</a>
</div>
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
<div class="ui stackable fluid pagination menu">
    <a class="item prev" href="|9|">Anterior</a>
    <a class="item " href="|1|">1</a>
    <div class="item disabled">&hellip;</div>
    <a class="item " href="|7|">7</a>
    <a class="item " href="|8|">8</a>
    <a class="item " href="|9|">9</a>
    <div class="item active">10</div>
    <a class="item " href="|11|">11</a>
    <a class="item " href="|12|">12</a>
    <a class="item " href="|13|">13</a>
    <div class="item disabled">&hellip;</div>
    <a class="item " href="|100|">100</a>
    <a class="item next" href="|11|">Siguiente</a>
</div>
EOF
            , $this->renderView($options));
    }

    public function testRenderModifyingCssClasses()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(1);

        $options = array(
            'css_container_class' => 'paginacion',
            'css_item_class' => 'itemo',
            'css_prev_class' => 'anterior',
            'css_next_class' => 'siguiente',
            'css_disabled_class' => 'deshabilitado',
            'css_dots_class' => 'puntos',
            'css_active_class' => 'activo',
        );

        $this->assertRenderedView(<<<EOF
<div class="paginacion">
    <div class="itemo anterior deshabilitado">&larr; Previous</div>
    <div class="itemo activo">1</div>
    <a class="itemo " href="|2|">2</a>
    <a class="itemo " href="|3|">3</a>
    <a class="itemo " href="|4|">4</a>
    <a class="itemo " href="|5|">5</a>
    <a class="itemo " href="|6|">6</a>
    <a class="itemo " href="|7|">7</a>
    <div class="itemo puntos">&hellip;</div>
    <a class="itemo " href="|100|">100</a>
    <a class="itemo siguiente" href="|2|">Next &rarr;</a>
</div>
EOF
            , $this->renderView($options));
    }

    protected function filterExpectedView($expected)
    {
        return $this->removeWhitespacesBetweenTags($expected);
    }
}
