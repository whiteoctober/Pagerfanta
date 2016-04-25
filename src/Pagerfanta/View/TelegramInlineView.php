<?php
namespace Pagerfanta\View;
use Pagerfanta\View\ViewInterface;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\Template\TemplateInterface;
use TelegramPagerfanta\View\Template\TelegramInlineTemplate;

class TelegramInlineView implements ViewInterface
{
    /**
     * @var TelegramInlineTemplate
     */
    private $template;

    /**
     * @var PagerfantaInterface
     */
    private $pagerfanta;

    private $currentPage;
    private $nbPages;

    private $startPage;
    private $endPage;
    private $maxPerPage;
    
    private $buttons;

    public function __construct(TemplateInterface $template = null)
    {
        $this->template = $template ?: $this->createDefaultTemplate();
        $this->buttons = new \stdClass();
    }

    protected function createDefaultTemplate()
    {
        return new \Pagerfanta\View\Template\TelegramInlineTemplate();
    }

    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = array())
    {
        $this->initializePagerfanta($pagerfanta);
        $this->initializeOptions($options);

        $this->configureTemplate($routeGenerator, $options);

        return $this->generatePages();
    }

    private function initializePagerfanta(PagerfantaInterface $pagerfanta)
    {
        $this->pagerfanta = $pagerfanta;

        $this->currentPage = $pagerfanta->getCurrentPage();
        $this->nbPages = $pagerfanta->getNbPages();
        $this->maxPerPage = $pagerfanta->getMaxPerPage();
    }

    private function initializeOptions($options)
    {
        $this->proximity = isset($options['proximity']) ?
                           (int) $options['proximity'] :
                           $this->getDefaultProximity();
    }

    protected function getDefaultProximity()
    {
        $proximityEstimated = floor($this->maxPerPage / 2);
        if($proximityEstimated < 2) {
            $proximity = $proximityEstimated;
        } else {
            $proximity = 2;
        }
        return $proximity;
    }

    private function configureTemplate($routeGenerator, $options)
    {
        $this->template->setRouteGenerator($routeGenerator);
        $this->template->setOptions($options);
    }

    private function generatePages()
    {
        $this->calculateStartAndEndPage();

        $this->first();
        if($this->currentPage > $this->maxPerPage) {
            $this->previous();
            if($this->nbPages - $this->maxPerPage <= $this->startPage) {
                $this->startPage++;
            }
        }
        if($this->pagerfanta->hasNextPage() && $this->currentPage >= $this->maxPerPage && $this->nbPages - $this->endPage >= 1) {
            $this->endPage--;
            $this->pages();
        } else {
            $this->pages();
        }
        $this->next();
        if($this->pagerfanta->hasNextPage() && $this->currentPage >= $this->maxPerPage && $this->nbPages > $this->endPage) {
            $this->endPage++;
        }
        $this->last();
        return (string)$this;
    }

    private function calculateStartAndEndPage()
    {
        $startPage = $this->currentPage - $this->proximity;
        $endPage = $this->currentPage + $this->proximity;

        if ($this->startPageUnderflow($startPage)) {
            $startPage = 1;
            $endPage = $this->maxPerPage;
            $endPage = $this->calculateEndPageForStartPageUnderflow($startPage, $endPage);
        }
        if ($this->endPageOverflow($endPage)) {
            $startPage = $this->calculateStartPageForEndPageOverflow($startPage, $endPage);
            $endPage = $this->nbPages;
        }

        $this->startPage = $startPage;
        $this->endPage = $endPage;
    }

    private function startPageUnderflow($startPage)
    {
        return $startPage < 1;
    }

    private function endPageOverflow($endPage)
    {
        return $endPage > $this->nbPages;
    }

    private function calculateEndPageForStartPageUnderflow($startPage, $endPage)
    {
        return min($endPage + (1 - $startPage), $this->nbPages);
    }

    private function calculateStartPageForEndPageOverflow($startPage, $endPage)
    {
        return max($startPage - ($endPage - $this->nbPages), 1);
    }

    private function previous()
    {
        if ($this->currentPage > $this->maxPerPage) {
            if($this->nbPages - $this->maxPerPage > $this->startPage) {
                $this->pushNewButton(
                    $this->template->previousEnabled($this->startPage - 1)
                );
            } else {
                $this->pushNewButton(
                    $this->template->previousEnabled($this->startPage)
                );
            }
        }
    }

    private function first()
    {
        if ($this->startPage > 1) {
            if($this->currentPage > $this->maxPerPage) {
                $this->pushNewButton(
                    $this->template->first()
                );
            } else {
                $this->pushNewButton(
                    $this->template->page(1)
                );
            }
        }
    }

    private function pages()
    {
        foreach (range($this->startPage, $this->endPage) as $page) {
            $this->page($page);
        }
    }

    private function page($page)
    {
        if ($page == $this->currentPage) {
            $this->pushNewButton(
                $this->template->current($page)
            );
        } else {
            $this->pushNewButton(
                $this->template->page($page)
            );
        }
    }
    
    private function pushNewButton(array $params) {
        $this->buttons->inline_keyboard[0][] = (object)$params;
    }

    private function toLast($n)
    {
        return $this->pagerfanta->getNbPages() - ($n - 1);
    }

    private function last()
    {
        if ($this->pagerfanta->getNbPages() > $this->endPage) {
            if($this->nbPages - $this->endPage <= 1) {
                $this->pushNewButton(
                    $this->template->page($this->pagerfanta->getNbPages())
                );
            } else {
                $this->pushNewButton(
                    $this->template->last($this->pagerfanta->getNbPages())
                );
            }
        }
    }

    private function next()
    {
        if ($this->pagerfanta->hasNextPage()) {
            if($this->nbPages - $this->endPage <= 2) {
                $this->pushNewButton(
                    $this->template->page($this->endPage + 1)
                );
            } else {
                $this->pushNewButton(
                    $this->template->nextEnabled($this->endPage + 1)
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'TelegramInline';
    }
    

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->buttons, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}