<?php
/**
 * Softphone Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Controller;

class Softphone {

    /**
     * portal
     * 
     */   
    static function portal(){

        // Get Parameters
        $app = \Slim\Slim::getInstance();
        $params = $app->request->params();
        
        // Set 'auth' false when a non-verified user comes.
        if(! isset($_SESSION['auth'])){
            $_SESSION['auth'] = false;
        
        // A verified user's 'auth' remains true.
        }

        // Verify the user if 'auth' is not set.
        if($_SESSION['auth'] == false){
 
            // Display an error and route to login page.
            $render = new \Runa_CCA\View\Render($app);
            $render->display("NOAUTH");

        // Go to the config portal page if the user has already been verified.
        }else{

            // Create and Set Twilio Information.
            $token   = (new \Runa_CCA\Model\Twilio())->getTwilioToken($_SESSION['client_name']);
            
            // DB Connection
            $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();
            
            // Update queues the operator has.
            $_SESSION['operator_queue'] = \Runa_CCA\Model\Database\Operator::find($_SESSION['operator_id'])->queue;
            
            // Set Session Data as global in Twig Template.
            $twig = $app->view()->getEnvironment();
            $twig->addGlobal("session", $_SESSION);            

            // Go to Portal page
            $render = new \Runa_CCA\View\Render($app);
            $render->display("SOFTPHONE", $token);

        }
    }
    
}
