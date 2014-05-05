<?php
/**
 * TwiMLApp Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Controller;

class TwiMLApp {
    
    /**
     * createTwiMLApp
     * 
     */    
    static function createTwiMLApp(){

        // Get Parameters
        $app = \Slim\Slim::getInstance();
        $params = $app->request->params();
        
        // Verify and start.
        if($params["AccountSid"] == \Base\Conf::ACCOUNT_SID){

            $response = (new \Runa_CCA\Model\TwiMLApp())->createTwiMLApp($params);
            (new \Runa_CCA\View\Twiml())->createTwiml($response);
            
        }else{
            
            \Runa_CCA\Controller\Error::display("ERROR");
            
        }
    
    }
}
