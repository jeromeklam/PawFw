<?php
namespace PhalconFW\Grid\Source;

interface SourceInterface
{

    /**
     * Get content as array
     * 
     * @param number $p_rowsPerPage
     * @param number $p_currentPage
     * 
     * @return array
     */
    public function getPaginate ($p_rowsPerPage, $p_currentPage);

}