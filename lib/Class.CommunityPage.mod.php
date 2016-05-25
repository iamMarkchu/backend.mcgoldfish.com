<?

/*
 * FileName: Class.CommunityPage.php
 * Author: Luke
 * Create Date: 2011-9-13
 * Package: package_name
 * Project: project_name
 * Remark: 
 */
if (!defined("__CLASS_COMMUNITYPAGE__")) {
    define("__CLASS_COMMUNITYPAGE__", 1);

    class CommunityPage {

        private $total;
        private $onepage;
        private $num;
        private $page;
        private $totalPage;
        private $offset;
        private $linkhead;

        public function CommunityPage($total, $onepage) {
            $page = array_key_exists('page', $_GET) ? intval($_GET['page']) : '';
            $this->total = $total;
            $this->onepage = $onepage;
            $this->totalPage = ceil($total / $onepage);

            if ($page == '') {
                $this->page = 1;
                $this->offset = 0;
            } else {
                $this->page = $page;
                $this->offset = ($page - 1) * $onepage;
            }

            parse_str($_SERVER['QUERY_STRING'], $output);
            if (isset($output["page"]))
                unset($output["page"]);
            $query_string_without_page = http_build_query($output);
            if ($query_string_without_page)
                $query_string_without_page .= "&";
            $this->linkhead = $_SERVER['PHP_SELF'] . "?" . $query_string_without_page;
        }

        public function getOffset() {
            return $this->offset;
        }

        private function getFirstPageString() {
            $linkhead = $this->linkhead;
            $page = $this->page;
            if ($this->total != 0 && $page > 1) {
                return " <a class=\"prev\" href=\"$linkhead" . "page=1\">First</a> ";
            }
        }

        private function getLastPageString() {
            $linkhead = $this->linkhead;
            $totalPage = $this->totalPage;
            $page = $this->page;
            if ($this->total != 0 && $page < $this->totalPage) {
                return " <a class=\"next\" href=\"$linkhead" . "page=$totalPage\">Last</a> ";
            }
        }

        private function getPrePageString() {
            $linkhead = $this->linkhead;
            $page = $this->page;
            if ($this->total != 0 && $page > 1) {
                $prePage = $page - 1;
                return " <a class=\"prev\" href=\"$linkhead" . "page=$prePage\">Prev</a> ";
            }
        }

        private function getNextPageString() {
            $linkhead = $this->linkhead;
            $totalPage = $this->totalPage;
            $page = $this->page;
            if ($this->total != 0 && $page < $this->totalPage) {
                $nextPage = $page + 1;
                return " <a class=\"next\" href=\"$linkhead" . "page=$nextPage\">Next</a> ";
            }
        }

        private function getNumBar($num = 8) {
            $this->num = $num;
            $mid = floor($num / 2);
            $last = $num - 1;
            $page = $this->page;
            $totalpage = $this->totalPage;
            $linkhead = $this->linkhead;
            $minpage = ($page - $mid) < 1 ? 1 : $page - $mid;
            $maxpage = $minpage + $last;
            if ($maxpage > $totalpage) {
                $maxpage = $totalpage;
                $minpage = $maxpage - $last;
                $minpage = $minpage < 1 ? 1 : $minpage;
            }

            $linkbar = "";
            for ($i = $minpage; $i <= $maxpage; $i++) {
                if ($i == $this->page) {
                    $linkchar = " <span>$i</span> ";
                } else {
                    $linkchar = " <a href='$linkhead" . "page=$i'>" . $i . "</a> ";
                }
                $linkbar .= $linkchar;
            }
            return $linkbar;
        }

        public function getWholeNumBar($num=8) {
            $numBar = $this->getNumBar($num);
            return $this->getFirstPageString() .
            $this->getPrePageString() .
            $numBar .
            $this->getNextPageString() .
            $this->getLastPageString();
        }

        public function getLimitString() {
            return "LIMIT {$this->offset}, {$this->onepage}";
        }

    }

}
?>