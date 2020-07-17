<?php

class Paginator {
    public $data = false;
    
    /**
     * Function generates page gallery data
     *
     * @param int $curPageNum
     * @param int $itemCountOnPage
     * @param int $itemTotalCount
     * @param int $chapterPageCount
     *
     * @return array
     */
    function  __construct($cur_page_num = 1, $item_total_count = 0, $itemCountOnPage = 20, $chapter_page_count = 10){
	    if ($item_total_count > 0 && $itemCountOnPage > 0){
		    $page_total_count = ceil($item_total_count / $itemCountOnPage);
		    $cur_page_num = $cur_page_num < 1 ? 1 : ($cur_page_num > $page_total_count ? $page_total_count : $cur_page_num);
		    $chapter_total_count = ceil($page_total_count / $chapter_page_count);
		    $cur_chapter_num = ceil($cur_page_num / $chapter_page_count);

		    $next_page_num = ($cur_page_num + 1) > $page_total_count ? 0 : $cur_page_num + 1;
		    $prev_page_num = ($cur_page_num - 1) < 1 ? 0 : $cur_page_num - 1;

		    $next_chapter_num = ($cur_chapter_num + 1) > $chapter_total_count ? 0 : $cur_chapter_num * $chapter_page_count + 1;
		    $prev_chapter_num = ($cur_chapter_num - 1) < 1 ? 0 : ($cur_chapter_num - 1) * $chapter_page_count;

		    $start_chapter_page_num = ($cur_chapter_num - 1) * 10 + 1;
		    $end_chapter_page_num = $cur_chapter_num * 10;
		    $end_chapter_page_num = $end_chapter_page_num > $page_total_count ? $page_total_count : $end_chapter_page_num;
		    $pages = array();
		    for ($i = $start_chapter_page_num; $i <= $end_chapter_page_num; ++ $i ){
			    $pages[] = $i;
		    }
            $this->data = array(
			    'item_total_count' => $item_total_count,
			    'cur_page_num' => $cur_page_num,
			    'page_total_count' => $page_total_count,
			    'cur_chapter_num' => $cur_chapter_num,
			    'chapter_total_count' => $chapter_total_count,
			    'chapter_page_count' => $chapter_page_count,
			    'next_page_num' => $next_page_num,
			    'prev_page_num' => $prev_page_num,
			    'next_chapter_num' => $next_chapter_num,
			    'prev_chapter_num' => $prev_chapter_num,
			    'pages' => $pages,
			    'sql_limit_from' => ($cur_page_num - 1) * $itemCountOnPage,
			    'sql_limit_count' => $itemCountOnPage
		    );
	    } else {
		    return false;
        }
        
    }
    public function get_pages() {
        return $this->data;
    }
}
?>