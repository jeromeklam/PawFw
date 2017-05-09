<?php
namespace PhalconTech\Services;

use \PhalconTech\Models\Email;
use \PhalconTech\Models\EmailLink;
use \PhalconTech\Models\EmailConfig;
use \PhalconTech\Constants as TechCST;
use \PhalconFW\Behaviour\DIBehaviour;
use \PhalconFW\Behaviour\TranslationBehaviour;
use \PhalconFW\Tools\String as FWString;
use Phalcon\Mvc\Model\Query;

/**
 *
 * @author jklam
 *
 */
class EmailService
{

    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    use TranslationBehaviour;

    /**
     * Get an email with it's type
     *
     * @param string $p_type
     * @param string $p_language
     * @param array  $p_datas
     *
     * @return boolean
     */
    public function getEmailByType ($p_type, $p_language, $p_datas = array())
    {
        try {
            $sql = 'SELECT E.*, C.*, L.* FROM \PhalconTech\Models\Email AS E
                         INNER JOIN \PhalconTech\Models\EmailLink AS L
                         INNER JOIN \PhalconTech\Models\EmailConfig AS C
                     WHERE E.type = :type: AND (E.lang = :lang: OR E.default = 1)
                     ORDER BY E.default';
            $di    = $this->getDI();
            $query = new Query($sql, $di);
            $rows  = $query->execute(array(
                'type' => $p_type,
                'lang' => $p_language
            ));
            $rows->rewind();
            $crt = false;
            if ($rows->valid()) {
                $crt = $rows->current();
            }
            if ($crt) {
                $subject = FWString::parse($crt->E->subject, $p_datas);
                $html    = FWString::parse($crt->E->html, $p_datas);
                $text    = FWString::parse($crt->E->text, $p_datas);
                
                return array(
                    'subject' => $subject,
                    'html'    => $html,
                    'test'    => $text,
                    'config'  => array(
                        'from-name'  => $crt->C->from_name,
                        'from-email' => $crt->C->from_email,
                        'cc'         => $crt->L->cc
                    )
                );
            }
            
            return false;
        } catch (\Exception $ex) {
            var_dump($ex);die;
        }
        
        return false;
    }

}