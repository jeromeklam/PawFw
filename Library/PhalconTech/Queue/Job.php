<?php
namespace PhalconTech\Queue;

use \PhalconFW\Mvc\Model;
use \PhalconFW\Tools\Date as MyDate;

/**
 * 
 * @author jeromeklam
 *
 */
class Job extends Model
{

    /**
     * Status
     * @var string
     */
    const STATUS_PREPROCESS = 'PREPROCESS';
    const STATUS_INPROGRESS = 'IN_PROGRESS';
    const STATUS_ERROR      = 'ERROR';
    const STATUS_WAITING    = 'WAITING';
    const STATUS_FINISH     = 'FINISH';

    /**
     * id
     * @var bigint(20)
     */
    protected $job_id = null;

    /**
     * name
     * @var varchar(255)
     */
    protected $job_name = null;

    /**
     * histo
     * @var text
     */
    protected $job_histo   = null;

    /**
     * status
     * @var varchar(32)
     */
    protected $job_status = null;

    /**
     * message
     * @var text
     */
    protected $job_message = null;

    /**
     * service
     * @var varchar(255)
     */
    protected $job_service = null;

    /**
     * method
     * @var varchar(255)
     */
    protected $job_method = null;

    /**
     * params
     * @var text
     */
    protected $job_params = array();

    /**
     * 
     */
    public function initialize ()
    {
        $this->setConnectionService('tech-cnx');
        $this->setSource('que_job');
    }

    /**
     *
     */
    public function columnMap ()
    {
        return array(
            'job_id'      => 'job_id',
            'job_name'    => 'job_name',
            'job_histo'   => 'job_histo  ',
            'job_status'  => 'job_status',
            'job_message' => 'job_message',
            'job_service' => 'job_service',
            'job_method'  => 'job_method',
            'job_params'  => 'job_params'
        );
    }

    /**
     * Setter for job_id
     *
     * @param bigint $p_job_id
     *
     * @return \PhalconTech\Queue\Job
     */
    public function setJobId ($p_job_id)
    {
        $this->job_id = $p_job_id;
        
        return $this;
    }

    /**
     * Getter for job_id
     *
     * @return bigint
     */
    public function getJobId ()
    {
        return $this->job_id;
    }

    /**
     * Setter for job_name
     *
     * @param varchar $p_job_name
     *
     * @return \PhalconTech\Queue\Job
     */
    public function setJobName ($p_job_name)
    {
        $this->job_name = $p_job_name;
        
        return $this;
    }

    /**
     * Getter for job_name
     *
     * @return varchar
     */
    public function getJobName ()
    {
        return $this->job_name;
    }

    /**
     * Setter for job_histo  
     *
     * @param text $p_job_histo  
     *
     * @return \PhalconTech\Queue\Job
     */
    public function setJobHisto   ($p_job_histo  )
    {
        $this->job_histo   = $p_job_histo  ;
        
        return $this;
    }

    /**
     * Getter for job_histo  
     *
     * @return text
     */
    public function getJobHisto   ()
    {
        return $this->job_histo  ;
    }

    /**
     * Setter for job_status
     *
     * @param varchar $p_job_status
     *
     * @return \PhalconTech\Queue\Job
     */
    public function setJobStatus ($p_job_status)
    {
        $this->job_status = $p_job_status;
        
        return $this;
    }

    /**
     * Getter for job_status
     *
     * @return varchar
     */
    public function getJobStatus ()
    {
        return $this->job_status;
    }

    /**
     * Setter for job_message
     *
     * @param text $p_job_message
     *
     * @return \PhalconTech\Queue\Job
     */
    public function setJobMessage ($p_job_message)
    {
        $this->job_message = $p_job_message;
        
        return $this;
    }

    /**
     * Getter for job_message
     *
     * @return text
     */
    public function getJobMessage ()
    {
        return $this->job_message;
    }

    /**
     * Setter for job_service
     *
     * @param varchar $p_job_service
     *
     * @return \PhalconTech\Queue\Job
     */
    public function setJobService ($p_job_service)
    {
        $this->job_service = $p_job_service;
        
        return $this;
    }

    /**
     * Getter for job_service
     *
     * @return varchar
     */
    public function getJobService ()
    {
        return $this->job_service;
    }

    /**
     * Setter for job_method
     *
     * @param varchar $p_job_method
     *
     * @return \PhalconTech\Queue\Job
     */
    public function setJobMethod ($p_job_method)
    {
        $this->job_method = $p_job_method;
        
        return $this;
    }

    /**
     * Getter for job_method
     *
     * @return varchar
     */
    public function getJobMethod ()
    {
        return $this->job_method;
    }

    /**
     * Setter for job_params
     *
     * @param text $p_job_params
     *
     * @return \PhalconTech\Queue\Job
     */
    public function setJobParams ($p_job_params)
    {
        if (!is_array($p_job_params)) {
            $this->job_params = json_decode($p_job_params, true);
        } else {
            $this->job_params = $p_job_params;
        }
        
        return $this;
    }

    /**
     * Getter for job_params
     *
     * @return text
     */
    public function getJobParams ()
    {
        return json_encode($this->job_params);
    }

    /**
     * Get params as array
     * 
     * @return array
     */
    public function getJobParamsAsArray ()
    {
        return $this->job_params;
    }

}