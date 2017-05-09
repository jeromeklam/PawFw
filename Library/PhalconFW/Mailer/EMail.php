<?php
namespace PhalconFW\Mailer;

class EMail
{
    /**
     * ViewsDir
     * @var string
     */
    protected $viewsDir = null;

    /**
     * Options
     * @var array
     */
    protected $options = null;

    /**
     * 
     * @param unknown $p_viewsDir
     * @param unknown $p_options
     */
    public function __Construct ($p_viewsDir, $p_options = array())
    {
        $this->viewsDir = $p_viewsDir;
        $this->options  = $p_options;
    }

    /**
     * 
     * @param string $p_view
     * @param string $p_language
     * @param array  $p_vars
     * 
     * @return multitype:NULL
     */
    public function get ($p_view, $p_language, $p_vars = array())
    {
        $data = array();
        // Body
        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir($this->viewsDir);
        $view->start();
        $view->setVars($p_vars);
        $view->render(strtolower($p_language), $p_view . '.body');
        $view->finish();
        $data['html'] = $view->getContent();
        // Text
        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir($this->viewsDir);
        $view->start();
        $view->setVars($p_vars);
        $view->render(strtolower($p_language), $p_view . '.text');
        $view->finish();
        $data['text'] = $view->getContent();
        // Subject
        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir($this->viewsDir);
        $view->start();
        $view->setVars($p_vars);
        $view->render(strtolower($p_language), $p_view . '.subject');
        $view->finish();
        $data['subject'] = $view->getContent();
        
        return $data;
    }

}