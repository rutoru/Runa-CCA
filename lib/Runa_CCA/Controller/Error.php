<?php
/**
 * Error Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Controller;

class Error {
    
    /**
     * display
     * 
     * @args String $msg Message
     */    
    static function display($msg){

        // Destroy Session
        $_SESSION = array();
        
        if (isset($_COOKIE[session_name()])){
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
        
        // Go to Login page.
        $app = \Slim\Slim::getInstance();
        $render = new \Runa_CCA\View\Render($app);
        $render->error($msg);
    }

    
}
