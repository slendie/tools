<?php
namespace Slendie\Tools;

class Pagination
{
    protected $range = 5;
    protected $limit = 10;
    protected $rows = 0;
    protected $template = 'partials.pagination';

    public $page = 1;
    public $first_page = 1;
    public $last_page = 5;
    public $previous_page = '';
    public $next_page = '';
    public $start_page = 1;

    public function __construct() { }

    public function setRange( $range )
    {
        $rest = $range % 2;
        if ( $rest == 0 ) {
            $range++;
        }
        $this->range = $range;
    }

    public function setLimit( $limit )
    {
        $this->limit = $limit;
    }

    public function setMax( $rows )
    {
        $this->rows = $rows;
    }

    public function setPage( $page )
    {
        $this->page = $page;
    }

    public function update()
    {
        $middle = (int) ( $this->range / 2 );
        $n_pages = (int) ( $this->rows / $this->limit );

        $rest = $this->rows % $this->limit;
        if ( $rest > 0 ) {
            $n_pages++;
        }

        /* Define start page */
        if ( $n_pages < ( $this->range + 1 ) ) {
            $this->start_page = 1;
            $bottom_limit = 1;
        } else {
            $bottom_limit = ( $n_pages - $middle );

            if ( $this->page < ( $middle + 1 ) ) {
                $this->start_page = 1;
            } else {
                if ( $this->page > $bottom_limit ) {
                    $this->start_page = $n_pages - $this->range + 1;
                } else {
                    $this->start_page = $this->page - $middle;
                }
            }
        }

        /* Define end page */
        if ( $n_pages < ( $this->range + 1 ) ) {
            $this->end_page = $n_pages;
            $top_limit = 1;
        } else {
            $top_limit = $n_pages - $middle;

            if ( $this->page > $top_limit ) {
                $this->end_page = $n_pages;
            } else {
                if ( $this->page < ( $middle + 1 ) ) {
                    if ( $n_pages > $this->range ) {
                        $this->end_page = $this->range;
                    } else {
                        $this->end_page = $n_pages;
                    }
                } else {
                    $this->end_page = $this->page + $middle;
                }
            }
        }

        $this->first_page = ( $this->page > ( $middle + 1 ) ? 1 : '');
        $this->last_page = ( $this->page < $top_limit ? $n_pages : '' );
        $this->previous_page = ( $this->page > 1 ? $this->page - 1 : '' );
        $this->next_page = ( $this->page < $n_pages ? $this->page + 1 : '');
    }

    public function paginate()
    {
        $this->update();
        return view( $this->template, ['pagination' => $this] );
    }
}