<?php

namespace Pagerfanta\Tests\View;

use Pagerfanta\View\TelegramInlineView;

class TelegramInlineViewTest extends ViewTestCase
{
    protected function createView()
    {
        return new TelegramInlineView();
    }

    public function testRenderNormal()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(10);

        $options = array();

        $this->assertRenderedView(<<<EOF
{"inline_keyboard":[[{"text":"1","callback_data":"|1|"},{"text":"8","callback_data":"|8|"},{"text":"9","callback_data":"|9|"},{"text":"· 10 ·","callback_data":"|10|"},{"text":"11","callback_data":"|11|"},{"text":"12 ›","callback_data":"|12|"},{"text":"100 »","callback_data":"|100|"}]]}
EOF
        , $this->renderView($options));
    }

    public function testRenderFirstPage()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(1);

        $options = array();

        $this->assertRenderedView(<<<EOF
{"inline_keyboard":[[{"text":"· 1 ·","callback_data":"|1|"},{"text":"2","callback_data":"|2|"},{"text":"3","callback_data":"|3|"},{"text":"4","callback_data":"|4|"},{"text":"5","callback_data":"|5|"},{"text":"6","callback_data":"|6|"},{"text":"7","callback_data":"|7|"},{"text":"8","callback_data":"|8|"},{"text":"9","callback_data":"|9|"},{"text":"10","callback_data":"|10|"},{"text":"11 ›","callback_data":"|11|"},{"text":"100 »","callback_data":"|100|"}]]}
EOF
        , $this->renderView($options));
    }

    public function testRenderLastPage()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(100);

        $options = array();

        $this->assertRenderedView(<<<EOF
{"inline_keyboard":[[{"text":"« 1","callback_data":"|1|"},{"text":"‹ 96","callback_data":"|96|"},{"text":"97","callback_data":"|97|"},{"text":"98","callback_data":"|98|"},{"text":"99","callback_data":"|99|"},{"text":"· 100 ·","callback_data":"|100|"}]]}
EOF
        , $this->renderView($options));
    }

    public function testRenderWhenStartProximityIs2()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(4);

        $options = array();

        $this->assertRenderedView(<<<EOF
{"inline_keyboard":[[{"text":"1","callback_data":"|1|"},{"text":"2","callback_data":"|2|"},{"text":"3","callback_data":"|3|"},{"text":"· 4 ·","callback_data":"|4|"},{"text":"5","callback_data":"|5|"},{"text":"6","callback_data":"|6|"},{"text":"7 ›","callback_data":"|7|"},{"text":"100 »","callback_data":"|100|"}]]}
EOF
        , $this->renderView($options));
    }

    public function testRenderWhenStartProximityIs3()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(5);

        $options = array();

        $this->assertRenderedView(<<<EOF
{"inline_keyboard":[[{"text":"1","callback_data":"|1|"},{"text":"3","callback_data":"|3|"},{"text":"4","callback_data":"|4|"},{"text":"· 5 ·","callback_data":"|5|"},{"text":"6","callback_data":"|6|"},{"text":"7","callback_data":"|7|"},{"text":"8 ›","callback_data":"|8|"},{"text":"100 »","callback_data":"|100|"}]]}
EOF
        , $this->renderView($options));
    }

    public function testRenderWhenEndProximityIs2FromLast()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(97);

        $options = array();

        $this->assertRenderedView(<<<EOF
{"inline_keyboard":[[{"text":"« 1","callback_data":"|1|"},{"text":"‹ 95","callback_data":"|95|"},{"text":"96","callback_data":"|96|"},{"text":"· 97 ·","callback_data":"|97|"},{"text":"98","callback_data":"|98|"},{"text":"99","callback_data":"|99|"},{"text":"100","callback_data":"|100|"}]]}
EOF
        , $this->renderView($options));
    }

    public function testRenderWhenEndProximityIs3FromLast()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(96);

        $options = array();

        $this->assertRenderedView(<<<EOF
{"inline_keyboard":[[{"text":"« 1","callback_data":"|1|"},{"text":"‹ 94","callback_data":"|94|"},{"text":"95","callback_data":"|95|"},{"text":"· 96 ·","callback_data":"|96|"},{"text":"97","callback_data":"|97|"},{"text":"98 ›","callback_data":"|98|"},{"text":"100 »","callback_data":"|100|"}]]}
EOF
        , $this->renderView($options));
    }

    public function testRenderModifyingProximity()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(10);

        $options = array('proximity' => 3);

        $this->assertRenderedView(<<<EOF
{"inline_keyboard":[[{"text":"1","callback_data":"|1|"},{"text":"7","callback_data":"|7|"},{"text":"8","callback_data":"|8|"},{"text":"9","callback_data":"|9|"},{"text":"· 10 ·","callback_data":"|10|"},{"text":"11","callback_data":"|11|"},{"text":"12","callback_data":"|12|"},{"text":"13 ›","callback_data":"|13|"},{"text":"100 »","callback_data":"|100|"}]]}
EOF
        , $this->renderView($options));
    }

    public function testRenderModifyingPreviousAndNextTemplates()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(10);

        $options = array(
            'previous_template' => '< %text%',
            'next_template'     => '%text% >',
        );

        $this->assertRenderedView(<<<EOF
{"inline_keyboard":[[{"text":"1","callback_data":"|1|"},{"text":"8","callback_data":"|8|"},{"text":"9","callback_data":"|9|"},{"text":"· 10 ·","callback_data":"|10|"},{"text":"11","callback_data":"|11|"},{"text":"12 >","callback_data":"|12|"},{"text":"100 »","callback_data":"|100|"}]]}
EOF
        , $this->renderView($options));
    }

    public function testRenderModifiyingStringTemplate()
    {
        $this->setNbPages(100);
        $this->setCurrentPage(1);

        $options = array(
            'first_page_template' => '<< %text%',
            'last_page_template'  => '%text% >>',
            'previous_template'   => '< %text%',
            'next_template'       => '%text% >',
            'container_template'  => '%pages%',
            'page_template'       => '(%text%)',
            'current_template'    => '| %text% |',
        );

        $this->assertRenderedView(<<<EOF
{"inline_keyboard":[[{"text":"(| 1 |)","callback_data":"|1|"},{"text":"(2)","callback_data":"|2|"},{"text":"(3)","callback_data":"|3|"},{"text":"(4)","callback_data":"|4|"},{"text":"(5)","callback_data":"|5|"},{"text":"(6)","callback_data":"|6|"},{"text":"(7)","callback_data":"|7|"},{"text":"(8)","callback_data":"|8|"},{"text":"(9)","callback_data":"|9|"},{"text":"(10)","callback_data":"|10|"},{"text":"(11 >)","callback_data":"|11|"},{"text":"(100 >>)","callback_data":"|100|"}]]}
EOF
        , $this->renderView($options));
    }
}
