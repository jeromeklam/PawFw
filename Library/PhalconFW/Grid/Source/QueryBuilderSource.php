<?php
namespace PhalconFW\Grid\Source;

use \PhalconFW\Behaviour\DIBehaviour;
use PhalconFW\Grid\Source\SourceInterface;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;

class QueryBuilderSource implements SourceInterface
{

    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    /**
     * 
     * @var unknown
     */
    protected $querybuilder = null;

    /**
     * 
     * @param array $p_params
     */
    public function __construct ($p_params = array(), $di=null)
    {
        $this->__DIConstruct($di);
        $di                 = $this->getDI();
        $manager            = $di->get('modelsManager');
        if (count($p_params) > 0) {
            $this->setQueryBuilder(new \Phalcon\Mvc\Model\Query\Builder($p_params));
        }
    }

    /**
     * Set query builder
     * 
     * @param \Phalcon\Mvc\Model\Query\Builder $p_builder
     * 
     * @return \PhalconFW\Grid\Source\QueryBuilderSource
     */
    public function setQueryBuilder ($p_builder)
    {
        $this->querybuilder = $p_builder;
        
        return $this;
    }
    /**
     * 
     * @param unknown $p_fields
     * @return \PhalconFW\Grid\Source\QueryBuilderSource
     */
    public function setOrder ($p_fields)
    {
        $this->querybuilder->orderBy($p_fields);
        
        return $this;
    }

    /**
     * Get content as array
     * 
     * @param number $p_rowsPerPage
     * @param number $p_currentPage
     * 
     * @return array
     */
    public function getPaginate ($p_rowsPerPage, $p_currentPage)
    {
        $paginator = new PaginatorQueryBuilder(array(
            "builder" => $this->querybuilder,
            "limit"   => $p_rowsPerPage,
            "page"    => $p_currentPage
        ));
        $result = $paginator->getPaginate();
        
        return $result;
    }

}