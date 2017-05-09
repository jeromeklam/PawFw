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
abstract class AbstractQueue {

    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    use TranslationBehaviour;

    /**
     * Create grid.
     *
     * @param ViewInterface $view View object.
     */
    public function __construct ($di = null)
    {
        $this->__DIConstruct($di);
        $di = $this->getDI();
    }

    /**
     * Add job to queue
     *
     * @param QueueJob $p_job
     * @param boolean  $p_runDirect
     * 
     * @return boolean
     */
    abstract public function addJob (QueueJob $p_job, $p_runDirect = false);

    /**
     * Run one job
     * 
     * @param QueueJob $p_job
     * 
     * @return boolean
     */
    protected function runJob (QueueJob $p_job)
    {
        $p_job->setJobStatus(QueueJob::STATUS_INPROGRESS);
        $p_job->save();
        $service = $p_job->getJobService();
        if ($this->getDI()->has($service)) {
            $runService = $this->getDI()->get($service);
            if (method_exists($runService, $p_job->getJobMethod())) {
                try {
                    $runService->{$p_job->getJobMethod()}($p_job);
                } catch (\Exception $ex) {
                    $p_job
                        ->setJobStatus(QueueJob::STATUS_ERROR)
                        ->setJobMessage(print_r($ex, true))
                    ;
                    $p_job->save();
                    
                    return false;
                }
                $p_job->setJobStatus(QueueJob::STATUS_FINISH);
                $p_job->save();
                
                return true;
            } else {
                $p_job
                    ->setJobStatus(QueueJob::STATUS_ERROR)
                    ->setJobMessage(sprintf('Method %s not found !', $p_job->getJobMethod()))
                ;
                $p_job->save();
            }
        } else {
            $p_job
                ->setJobStatus(QueueJob::STATUS_ERROR)
                ->setJobMessage(sprintf('Service %s not found !', $service))
            ;
            $p_job->save();
        }
        
        return false;
    }

}