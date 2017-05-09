<?php
namespace PhalconFW\Grid;

use \PhalconFW\Behaviour\DIBehaviour;
use \PhalconFW\Behaviour\TranslationBehaviour;
use \PhalconFW\Grid\Source\SourceInterface;
use \Phalcon\Mvc\ViewInterface;
use Phalcon\Mvc\Url;

/**
 * 
 * @author jeromeklam
 *
 */
abstract class AbstractGrid {

    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    use TranslationBehaviour;

    /**
     * 
     * @var unknown
     */
    const TYPE_KEY      = 'KEY';
    const TYPE_RATING   = 'RATING';
    const TYPE_TEXT     = 'TEXT';
    const TYPE_DATE     = 'DATE';
    const TYPE_DATETIME = 'DATETIME';
    const TYPE_PERSO    = 'PERSO';

    /**
     * Title
     * @var string
     */
    protected $title = null;

    /**
     * Header title
     * @var string
     */
    protected $header_title = null;

    /**
     * Header menu
     * @var HeaderMenu
     */
    protected $header_menu = false;

    /**
     * Table body view
     * @var string
     */
    protected $tableBodyView = null;

    /**
     * Source
     * @var PhalconFW\Grid\Source\SourceInterface
     */
    protected $source = null;

    /**
     * Result
     * @var array
     */
    protected $result = array();

    /**
     * grid columns
     * @var array
     */
    protected $columns = array();

    /**
     * global actions
     * @var array
     */
    protected $global_actions = array();

    /**
     * line actions
     * @var array
     */
    protected $line_actions = array();

    /**
     * Rows per page
     * @var unknown
     */
    protected $rows_per_page = 14;

    /**
     * Current page
     * @var unknown
     */
    protected $current_page = 1;

    /**
     * Base url
     * @var unknown
     */
    protected $baseUrl = null;

    /**
     * Route
     * @var string
     */
    protected $route = null;

    /**
     * Primary key
     * @var string
     */
    protected $pk = false;

    /**
     * Grid complement
     * @var unknown
     */
    protected $complement = '';

    /**
     * Create grid.
     *
     * @param ViewInterface $view View object.
     */
    public function __construct ($di = null)
    {
        $this->__DIConstruct($di);
        $di               = $this->getDI();
        $this->response   = $di->getResponse();
        $this->_init();
    }

    /**
     * 
     * @param unknown $p_title
     * @return \PhalconFW\Grid\Grid
     */
    public function setTitle ($p_title)
    {
        $this->title = $p_title;
        
        return $this;
    }

    /**
     * 
     * @return unknown
     */
    public function getTitle ()
    {
        return $this->title;
    }

    /**
     * Set header title
     * 
     * @param string $p_title
     * 
     * @return \PhalconFW\Grid\AbstractGrid
     */
    public function setHeaderTitle ($p_title)
    {
        $this->header_title = $p_title;
        
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getHeaderTitle ()
    {
        return $this->header_title;
    }

    /**
     * Sets header menu
     * 
     * @param AbstractMenu $p_menu
     * 
     * @return AbstractGrid
     */
    public function setHeaderMenu ($p_menu)
    {
        $this->header_menu = $p_menu;
        
        return $this;
    }

    /**
     * Getter for header_menu
     * 
     * @return \PhalconFW\Grid\HeaderMenu
     */
    public function getHeaderMenu ()
    {
        return $this->header_menu;
    }

    /**
     * Set base url
     * 
     * @param string $p_url
     * 
     * @return AbstractGrid
     */
    public function setBaseUrl ($p_url)
    {
        $this->baseUrl = $p_url;
        
        return $this;
    }
    /**
     * 
     * @param SourceInterface $p_source
     * 
     * @return \PhalconFW\Grid\AbstractGrid
     */
    public function setSource (SourceInterface $p_source)
    {
        $this->source = $p_source;
        
        return $this;
    }

    /**
     * Set route
     * 
     * @param string $p_route
     * 
     * @return \PhalconFW\Grid\AbstractGrid
     */
    public function setRoute ($p_route)
    {
        $this->route = $p_route;
        
        return $this;
    }

    /**
     * Get route
     * 
     * @return string
     */
    public function getRoute ()
    {
        return $this->route;
    }

    /**
     * Set table body view
     * 
     * @param string $p_view
     * 
     * @return \PhalconFW\Grid\AbstractGrid
     */
    public function setTableBodyView ($p_view)
    {
        $this->tableBodyView = $p_view;
        
        return $this;
    }

    /**
     * Get table body view
     * 
     * @returns string
     */
    public function getTableBodyView ()
    {
        return $this->tableBodyView;
    }

    /**
     * Get a complete url
     * 
     * @param array $p_params
     * 
     * @return string
     */
    public function getGridUrl ($p_params)
    {
        $url    = new Url();
        $params = $this->getDI()->getDispatcher()->getParams();
        foreach ($params as $key => $value) {
            if (array_key_exists($key, $p_params)) {
                $params[$key] = $p_params[$key];
            }
        }
        if( !array_key_exists('page', $p_params)) {
            $p_params['page'] = 1;
        }
        $add = '';
        foreach ($params as $key => $value) {
            if ($key != 'page' && ! is_int($key)) {
                $add .= '/' . $key . '/' . $value;
            }
        }
        $link = $url->get($this->baseUrl . '/' . $p_params['page'] . $add);
        
        return $link;
    }

    /**
     * Render grid.
     *
     * @param string $viewName Name of the view file.
     *
     * @return string
     */
    public function get ($p_viewName = null)
    {
        // First sort, conditions, ...
        $order  = array();
        $count  = 1;
        $di     = $this->getDI();
        $assets = $di->get('assets');
        $page   = $di->getDispatcher()->getParam('page');
        if ($page != '') {
            $page = intval($page);
        } else {
            $page = 1;
        }
        $this->setCurrentPage($page);
        $assets
            ->collection('headerPrivateCSS')
            ->addCss('/vendor/datatables/media/css/dataTables.bootstrap.min.css')
        ;
        // Next
        foreach ($this->getColumns() as $oneColumn) {
            if (isset($oneColumn['sort'])) {
                $add = '';
                if (strpos($oneColumn['sort'], '-') !== false) {
                    $add = ' desc';
                }
                $order[$count] = $oneColumn['source'] . $add;
                $count++;
            }
        }
        //
        $this->source->setOrder(implode(', ',$order));
        $this->result = $this->source->getPaginate($this->getRowsPerPage(), $this->getCurrentPage());
        if ($this->getCurrentPage() > $this->getTotalPages()) {
            $this->setCurrentPage($this->getTotalPages());
            $this->result = $this->source->getPaginate($this->getRowsPerPage(), $this->getCurrentPage());
        }
        if (!$p_viewName) {
            $p_viewName = $this->getTableBodyView();
        }
        /** @var View $view */
        $view = $this->getDI()->get('view');
        ob_start();
        $view->setVars($this->_getVars());
        $view->partial($p_viewName, ['grid' => $this]);
        $html = ob_get_contents();
        ob_end_clean();
        if ($this->getDI()->getRequest()->isAjax()) {
            $view->setContent($html);
        }

        return $html;
    }

    /**
     * View vars
     */
    protected function _getVars ()
    {
        return array(
            'title' => $this->getTitle()
        );
    }

    /**
     * Init
     */
    abstract protected function _init ();

    /**
     * Add one column
     * 
     * @param array $p_col
     * 
     * @return AbstractGrid
     */
    public function addColumn ($p_col)
    {
        if (!array_key_exists('type', $p_col)) {
            $p_col['type'] = self::TYPE_TEXT;
        }
        if (!array_key_exists('display', $p_col)) {
            $p_col['display'] = true;
        }
        if (!array_key_exists('sortable', $p_col)) {
            $p_col['sortable'] = true;
        }
        if (!array_key_exists('class', $p_col)) {
            $p_col['class'] = '';
        }
        if (!array_key_exists('thumbnail', $p_col)) {
            $p_col['thumbnail'] = false;
        }
        if (!array_key_exists('fileicon', $p_col)) {
            $p_col['fileicon'] = false;
        }
        if (!array_key_exists('filter', $p_col)) {
            $p_col['filter'] = false;
        }
        if (!array_key_exists('sorted', $p_col)) {
            $p_col['sorted'] = false;
            if (array_key_exists('sort', $p_col)) {
                if (strpos($p_col['sort'], '-') !== false) {
                    $p_col['sorted'] = 'DESC';
                } else {
                    $p_col['sorted'] = 'ASC';
                }
            }
        }
        $this->columns[] = $p_col;
        if ($p_col['type'] == self::TYPE_KEY) {
            $this->pk = $p_col['source'];
        }
        
        return $this;
    }

    /**
     * Add global action
     *
     * @param unknown $p_col
     *
     * @return AbstractGrid
     */
    public function addGlobalAction ($p_col)
    {
        if (array_key_exists('route', $p_col)) {
            $di      = $this->getDI();
            $routes  = $di->get('routes');
            $myRoute = $routes->getByKey($p_col['route']);
            if ($myRoute !== false) {
                $this->global_actions[$p_col['action']] = $myRoute;
            }
            if (array_key_exists('mode', $p_col)) {
                $this->global_actions[$p_col['action']]['mode'] = $p_col['mode'];
            }
            if (array_key_exists('mode', $p_col) && array_key_exists('function', $p_col)) {
                $this->global_actions[$p_col['action']]['function'] = $p_col['function'];
            }
        } else {
            // @todo
        }
    
        return $this;
    }

    /**
     * Add line action
     * 
     * @param unknown $p_col
     * 
     * @return AbstractGrid
     */
    public function addLineAction ($p_col)
    {
        if (array_key_exists('route', $p_col)) {
            $di      = $this->getDI();
            $routes  = $di->get('routes');
            $myRoute = $routes->getByKey($p_col['route']);
            if ($myRoute !== false) {
                $this->line_actions[$p_col['action']] = $myRoute;
            }
            if (array_key_exists('mode', $p_col)) {
                $this->line_actions[$p_col['action']]['function'] = $p_col['function'];
            }
        }

        return $this;
    }

    /**
     * Has action ??
     * 
     * @return boolean
     */
    public function hasAction ()
    {
        return count($this->line_actions) > 0;
    }

    /**
     * Get all columns
     * 
     * @return array
     */
    public function getColumns ()
    {
        return $this->columns;
    }

    /**
     * 
     * @param unknown $p_row
     * @return unknown|number
     */
    public function getId ($p_row)
    {
        if ($this->pk && array_key_exists($this->pk, $p_row)) {
            return $p_row[$this->pk];
        }
        return 0;
    }

    /**
     * Get Click function for action
     * 
     * @param string $p_actionName
     * @param string $p_function
     * 
     * @return string
     */
    protected function getActionClick($p_actionName, $p_function) {
        return $p_function . '();';
    }

    /**
     *
     * @param unknown $p_row
     * @return multitype:multitype:unknown NULL
     */
    public function getGlobalActions ()
    {
        $di      = $this->getDI();
        $routes  = $di->get('routes');
        $actions = array();
        foreach ($this->global_actions as $action) {
            if (isset($action['function']) && $action['function'] != '') {
                $actions[] = array(
                    'href'  => '#',
                    'html'  => false,
                    'click' => $this->getActionClick($action['action'], $action['function']),
                    'glyph' => $action['glyph'],
                    'label' => $this->_($action['label'])
                );
            } else {
                if (array_key_exists('mode', $action) && $action['mode'] == 'dropzone') {
                    $actions[] = array(
                        'html'  => 'html_' . $action['mode'],
                        'glyph' => $action['glyph'],
                        'href'  => false,
                        'click' => false,
                        'label' => $this->_($action['label'])
                    );
                } else {
                    $actions[] = array(
                        'href'  => $routes->getUrl($action['path']),
                        'html'  => false,
                        'click' => false,
                        'glyph' => $action['glyph'],
                        'label' => $this->_($action['label'])
                    );
                }
            }
        }
    
        return $actions;
    }

    /**
     * 
     * @param unknown $p_row
     * @return multitype:multitype:unknown NULL
     */
    public function getLineActions ($p_row = array())
    {
        $di      = $this->getDI();
        $routes  = $di->get('routes');
        $actions = array();
        foreach ($this->line_actions as $action) {
            $id        = $this->getId($p_row);
            if (isset($action['function']) && $action['function'] != '') {
                $actions[] = array(
                    'href'  => '#',
                    'click' => $action['function']. '(' . $id . ');',
                    'glyph' => $action['glyph'],
                    'label' => $this->_($action['label'])
                );
            } else {
                $actions[] = array(
                    'href'  => $routes->getUrl($action['path'], array('id' => $id)),
                    'click' => false,
                    'glyph' => $action['glyph'],
                    'label' => $this->_($action['label'])
                );
            }
        }
        
        return $actions;
    }

    /**
     * Sets rows per page
     * 
     * @param number $p_count
     * 
     * @return AbstractGrid
     */
    public function setRowsPerPage ($p_count)
    {
        $this->rows_per_page = $p_count;
        
        return $this;
    }

    /**
     * Get rows per page
     * @return \PhalconFW\Grid\unknown
     */
    public function getRowsPerPage ()
    {
        return $this->rows_per_page;
    }

    /**
     * Set current page
     * 
     * @param number $p_page
     * 
     * @return AbstractGrid
     */
    public function setCurrentPage ($p_page)
    {
        $this->current_page = $p_page;
        
        return $this;
    }

    /**
     * Get first link
     * 
     * @return string
     */
    public function getFirstUrl ()
    {
        return $this->getGridUrl(array('page' => 1));
    }

    /**
     * Previuous url
     * 
     * @return string
     */
    public function getPreviousUrl ()
    {
        return $this->getGridUrl(array('page' => $this->getPreviousPage()));
    }

    /**
     * Next url
     * 
     * @return string
     */
    public function getNextUrl ()
    {
        return $this->getGridUrl(array('page' => $this->getNextPage()));
    }

    /**
     * Last url
     *
     * @return string
     */
    public function getLastUrl ()
    {
        return $this->getGridUrl(array('page' => $this->getTotalPages()));
    }

    /**
     * Array of links
     * 
     * @param number $p_max
     * 
     * @return array
     */
    public function getPaginatorUrls ($p_max = 5)
    {
        $addBefore = false;
        $addAfter  = false;
        if ($this->getTotalPages() <= $p_max) {
            $first = 1;
            $last  = $this->getTotalPages();
        } else {
            if ($this->getCurrentPage() > 1) {
                $addBefore = true;
            }
            if ($this->getCurrentPage() < $this->getTotalPages()) {
                $addAfter = true;
            }
            $first = $this->getCurrentPage() - 2;
            if ($first <= 0) {
                $first = 1;
            }
            $last = $first + $p_max - 1;
            if ($last > $this->getTotalPages()) {
                $last  = $this->getTotalPages();
                $first = $last - $p_max + 1;
            }
        }
        $links = array();
        if ($addBefore) {
            $links[] = array('label' => '...', 'url' => null, 'active' => false);
        }
        for ($i = $first; $i <= $last; $i++) {
            $url     = new Url();
            $active  = ($i == $this->getCurrentPage() ? true : false);
            $links[] = array(
                'label'  => $i,
                'url'    => $this->getGridUrl(array('page' => $i)),
                'active' => $active
            );
        }
        if ($addAfter) {
            $links[] = array('label' => '...', 'url' => null, 'active' => false);
        }
        
        return $links;
    }

    /**
     * Get previous page
     * 
     * @return number
     */
    public function getPreviousPage ()
    {
        if ($this->current_page > 1) {
            
            return $this->current_page - 1;
        }
        
        return 1;
    }

    /**
     * Get current page
     * 
     * @return number
     */
    public function getCurrentPage ()
    {
        return $this->current_page;
    }

    /**
     * Get Next oage
     * 
     * @return number
     */
    public function getNextPage ()
    {
        if ($this->current_page < $this->result->total_pages) {
        
            return $this->current_page + 1;
        }
        
        return $this->result->total_pages;
    }

    /**
     * Get results
     * 
     * @return multitype:
     */
    public function getResults ()
    {
        return $this->result->items;
    }

    /**
     * Get total count
     * 
     * @return number
     */
    public function getTotalCount ()
    {
        return $this->result->total_items;
    }

    /**
     * Total pages
     * 
     * @return number
     */
    public function getTotalPages ()
    {
        return $this->result->total_pages;
    }

    /**
     * Set header complement
     * 
     * @param string $p_complement
     * 
     * @return \PhalconFW\Grid\AbstractGrid
     */
    public function setHeaderComplement ($p_complement)
    {
        $this->complement = $p_complement;
        
        return $this;
    }

    /**
     * Get Header complememnt
     * 
     * @return string
     */
    public function getHeaderComplement ()
    {
        return $this->complement;
    }

    /**
     * Get peso field contents
     * 
     * @param unknown $p_field
     * @param unknown $p_result
     * 
     * @return array
     */
    public function getPersoField ($p_field, $p_result)
    {
        $ret    = array('full-content' => '', 'tiny-content' => '');
        $method = 'get' . ucfirst($p_field);
        if (method_exists($this, $method)) {
            $ret = $this->{$method}($p_result);
        }
        
        return $ret;
    }

    /**
     * 
     * @param unknown $p_result
     */
    public function getIdFromResult ($p_result)
    {
        if (array_key_exists($this->pk, $p_result)) {
            
            return $p_result[$this->pk];
        }
        
        return uniqid(microtime(true));
    }

    /**
     * Get filtered columns
     * 
     * @return array
     */
    public function getFilterFields ()
    {
        $params = $this->getDI()->getDispatcher()->getParams();
        $fields = array();
        foreach ($this->columns as $oneColumn) {
            if ($oneColumn['filter']) {
                $fields[] = array(
                    'source' => $oneColumn['source'],
                    'label'  => $this->_($oneColumn['title']),
                    'type'   => $oneColumn['type'],
                    'value'  => (array_key_exists($oneColumn['source'], $params) ? $params[$oneColumn['source']] : '')
                );
            }
        }
        
        return $fields;
    }

}