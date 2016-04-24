<?php
namespace Pagerfanta\View\Template;

use Pagerfanta\View\Template\Template;
/**
 * @author Vitor Mattos <vitor.mattos@phprio.org>
 */
class TelegramInlineTemplate extends Template
{
    static protected $defaultOptions = array(
        'first_page_template' => '« %text%',
        'last_page_template'  => '%text% »',
        'previous_template'   => '‹ %text%',
        'next_template'       => '%text% ›',
        'page_template'       => '%text%',
        'current_template'    => '· %text% ·',
    );

    public function container() { }

    public function page($page)
    {
        $text = $page;

        return $this->pageWithText($page, $text);
    }

    public function pageWithText($page, $text)
    {
        return array(
            'text' => str_replace('%text%', $text, $this->option('page_template')),
            'callback_data' => $this->generateRoute($page)
        );
    }

    public function previousDisabled() { }

    public function previousEnabled($page)
    {
        return $this->pageWithText($page, str_replace('%text%', $page, $this->option('previous_template')));
    }

    public function nextDisabled() { }

    public function nextEnabled($page)
    {
        return $this->pageWithText($page, str_replace('%text%', $page, $this->option('next_template')));
    }

    public function first()
    {
        $text = str_replace('%text%', 1, $this->option('first_page_template'));

        return $this->pageWithText(1, $text);
    }

    public function last($page)
    {
        $text = str_replace('%text%', $page, $this->option('last_page_template'));

        return $this->pageWithText($page, $text);
    }

    public function current($page)
    {
        $text = str_replace('%text%', $page, $this->option('current_template'));

        return $this->pageWithText($page, $text);
    }

    public function separator() { }

    private function generateSpan($class, $page)
    {
        $search = array('%class%', '%text%');
        $replace = array($class, $page);

        return str_replace($search, $replace, $this->option('span_template'));
    }
}
