<?php
namespace PhalconTech\Queue;

use \PhalconFW\Behaviour\DIBehaviour;
use \PhalconFW\Behaviour\TranslationBehaviour;
use \PhalconTech\Queue\Job as QueueJob;
use \Phalcon\Mvc\ViewInterface;
use \Phalcon\Mvc\Url;

/**
 *
 * @author jeromeklam
 *
 */
class PDOQueue extends AbstractQueue {

    /**
     * Setter for connexion
     * 
     * @param unknown $p_cnx
     * 
     * @return \PhalconTech\Queue\PDOQueue
     */
    public function setCnx ($p_cnx)
    {
        $this->getDI()->set('tech-cnx', $p_cnx);
        
        return $this;
    }

    /**
     * Add job to queue
     *
     * @param QueueJob $p_job
     * @param boolean  $p_runDirect
     * 
     * @return boolean
     */
    public function addJob (QueueJob $p_job, $p_runDirect = false)
    {
        $p_job->setJobStatus(QueueJob::STATUS_PREPROCESS);
        if ($p_job->save()) {
            if ($p_runDirect) {
                return $this->runJob($p_job);
            }
            
            return true;
        } else {
            
            return false;
        }
    }

}