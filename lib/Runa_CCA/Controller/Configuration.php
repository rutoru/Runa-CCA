<?php
/**
 * Configuration Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Controller;

class Configuration {

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
            $render->display("LOGINERR");

        // Go to the config portal page if the user has already been verified.
        }else{

            // Check the operator level.
            if(isset($_SESSION['operator_lv'])){
                
                // Set Session Data as global in Twig Template.
                $twig = $app->view()->getEnvironment();
                $twig->addGlobal("session", $_SESSION);            

                // Go to Portal page
                $render = new \Runa_CCA\View\Render($app);
                $render->display("CONFIGPORTAL");
                                
            // No Session value operator_lv
            }else{
            
                // Destroy Session
                $_SESSION = array();

                if (isset($_COOKIE[session_name()])){
                    setcookie(session_name(), '', time() - 3600, '/');
                }

                session_destroy();

                // Display an error and route to login page.
                $render = new \Runa_CCA\View\Render($app);
                $render->display("NOAUTH");

            }
            
        }
    }
    
}
