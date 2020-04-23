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

    # Make sure the feed page default has a value
    if (!array_key_exists('cops_feed_default_page', $config)) {
        $config['cops_feed_default_page'] = Base::PAGE_INDEX;
    }
    header('Content-Type:application/xml');
    $page = getURLParam('page', $config['cops_feed_default_page']);
    $query = getURLParam('query');
    $n = getURLParam('n', '1');
    if ($query) {
        $page = Base::PAGE_OPENSEARCH_QUERY;
    }
    $qid = getURLParam('id');

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
