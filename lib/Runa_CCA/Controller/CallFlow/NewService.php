<?php
/**
 * NewService Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Controller\CallFlow;

class NewService {
    
    /**
     * start
     * 
     * @param String $method CallFlow method
     */    
    static function start($method){

        // Get Parameters
        $app = \Slim\Slim::getInstance();
        $params = $app->request->params();

        // Verify and start.
        if($params["AccountSid"] == \Base\Conf::ACCOUNT_SID){
            
                switch ($method){

                    // Go to Main CallFlow
                    case "main" :
                    
                        $response = (new \Runa_CCA\Model\CallFlow\NewService\Main())->main($params);
                        (new \Runa_CCA\View\Twiml())->createTwiml($response);
                        break;

                    // Go to Wait
                    case "wait" :
                        
                        $response = (new \Runa_CCA\Model\CallFlow\NewService\Wait())->wait($params);
                        (new \Runa_CCA\View\Twiml())->createTwiml($response);
                        break;

                    // Go to Info
                    case "info" :
                        
                        $response = (new \Runa_CCA\Model\CallFlow\NewService\Info())->info();
                        (new \Runa_CCA\View\Twiml())->createTwiml($response);
                        break;
                    
                    // Go to EnqueAction
                    case "enqueaction" :
                        
                        $response = (new \Runa_CCA\Model\CallFlow\NewService\EnqueAction())->insert($params);
                        (new \Runa_CCA\View\Twiml())->createTwiml($response);
                        break;

                    // Go to QueueUrl
                    case "guidance" :
                        
                        $response = (new \Runa_CCA\Model\CallFlow\NewService\QueueUrl())->insert($params);
                        (new \Runa_CCA\View\Twiml())->createTwiml($response);
                        break;

                    // Go to StatusCallback
                    case "statuscallback" :
                        
                        $response = (new \Runa_CCA\Model\CallFlow\NewService\StatusCallback())->insert($params);
                        (new \Runa_CCA\View\Twiml())->createTwiml($response);
                        break;

                    
                    // Go to Error
                    default :
                        
                        \Runa_CCA\Controller\Error::display("ERROR");
                        break;
                        
                }
        
        // Go to Error
        }else{

                \Runa_CCA\Controller\Error::display("ERROR");

        }
    
    }
    
}
