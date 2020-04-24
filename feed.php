<?php
/**
 * COPS (Calibre OPDS PHP Server) main script
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Sébastien Lucas <sebastien@slucas.fr>
 *
 */

    require_once 'config.php';
    require_once 'base.php';

    header('Content-Type:application/xml');
    $page = getURLParam('page', empty($config['cops_feed_default_page']) ? Base::PAGE_INDEX : $config['cops_feed_default_page']);
    $query = getURLParam('query');
    $n = getURLParam('n', '1');
    $tag_name = getURLParam('tagName');
    if (empty($tag_name) && $page == Base::PAGE_TAG_DETAIL && !empty($config['cops_feed_default_tag'])) {
        $tag_name = $config['cops_feed_default_tag'];
    }

    if ($query) {
        $page = Base::PAGE_OPENSEARCH_QUERY;

    } elseif ($tag_name) {
        $tag = Tag::getTagByName($tag_name);
        if ($tag) {
            $page = Base::PAGE_TAG_DETAIL;
            $qid = $tag->id;
        }

    } else {
        $qid = getURLParam('id');
    }

    if ($config ['cops_fetch_protect'] == '1') {
        session_start();
        if (!isset($_SESSION['connected'])) {
            $_SESSION['connected'] = 0;
        }
    }

    $OPDSRender = new OPDSRenderer();

    switch ($page) {
        case Base::PAGE_OPENSEARCH :
            echo $OPDSRender->getOpenSearch();
            return;
        default:
            $currentPage = Page::getPage($page, $qid, $query, $n);
            $currentPage->InitializeContent();
            echo $OPDSRender->render($currentPage);
            return;
    }
