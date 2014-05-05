<?php
/**
 * Render Class
 * TwigにRenderするクラス
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\View;

class Render {

    /**
     * Object Variables
     */
    private $app;
 
    /**
     * Constructor
     * 
     * @param Slim Object 
     */
    public function __construct($app){
        
        $this->app = $app;
        
    }

    /**
     * Display Function
     * 
     * @param String Display Name
     * @param Object[] Display Contents
     */    
    public function display(){
        
        $args = func_get_args();
        
        switch($args[0])
        {
            
            // LOGIN
            case "LOGIN":

                $this->app->render('Login/login_msg.twig');
                break;
            
            // MAIN
            case "MAIN":
                
                $this->app->render('Main/softphone.twig');
                break;
            
            // LOGINERR
            case "LOGINERR":
                
                $this->app->render(
                    'Login/login_msg.twig', 
                    [
                        'alert_level'         => 'alert-danger',
                        'alert_title'         => \Runa_CCA\View\Msg::TITLE_LOGINERR,
                        'login_error_message' => \Runa_CCA\View\Msg::MSG_LOGINERR,
                    ]
                );  
                
                break;

            // CONFIG Portal
            case "CONFIGPORTAL":
                
                // Set Default Value.
                if(!isset($args[1])) $args[1] = 'alert-success';
                if(!isset($args[2])) $args[2] = \Runa_CCA\View\Msg::TITLE_CONFLOGIN;
                if(!isset($args[3])) $args[3] = \Runa_CCA\View\Msg::MSG_CONFLOGIN;
                
                $this->app->render(
                    'Main/config_frame.twig', 
                    [
                        'alert_level'         => $args[1],  // Alert Level
                        'alert_title'         => $args[2],  // Alert Title
                        'popup_msg'           => $args[3],  // Alert Message
                    ]
                );  
                
                break;

            // Operator List
            case "OPERATORLIST":
                
                $this->app->render(
                    'Main/oplist.twig', 
                    [
                        'operators'           => $args[1],  // Operator List
                        'oplevels'            => $args[2],  // Operator Levels
                        'alert_level'         => $args[3],  // Alert Level
                        'alert_title'         => $args[4],  // Alert Title
                        'popup_msg'           => $args[5],  // Alert Message
                        ]
                );  
                
                break;

            // Operator New
            case "OPERATORNEW":
                
                $this->app->render(
                    'Main/opnew.twig', 
                    [
                        'queues'              => $args[1],  // Queue List
                        'operators'           => $args[2],  // Operator List
                        'opqueues'            => $args[3],  // Queues the operator has
                        'oplevels'            => $args[4],  // Operator Levels
                        'oplevel'             => $args[5],  // Operator Level the operator has
                        'validation'          => $args[6],  // Result of Validation
                        'flag'                => $args[7],  // Flag (Update or not)
                        'alert_level'         => $args[8],  // Alert Level
                        'alert_title'         => $args[9],  // Alert Title
                        'popup_msg'           => $args[10]  // Alert Message
                    ]
                );  
                
                break;
            
            // Queue List
            case "QUEUELIST":
                
                $this->app->render(
                    'Main/queuelist.twig', 
                    [
                        'queues'              => $args[1],
                        'alert_level'         => $args[2],
                        'alert_title'         => $args[3],
                        'popup_msg'           => $args[4],
                    ]
                );  
                
                break;

            // Queue New
            case "QUEUENEW":
                
                $this->app->render(
                    'Main/queuenew.twig', 
                    [
                        'queues'              => $args[1],
                        'validation'          => $args[2],
                        'flag'                => $args[3],
                        'alert_level'         => $args[4],
                        'alert_title'         => $args[5],
                        'popup_msg'           => $args[6],
                    ]
                );  
                
                break;
            
            // LOGOUT
            case "LOGOUT":
                
                $this->app->render(
                    'Login/login_msg.twig', 
                    [
                        'alert_level'         => 'alert-danger',
                        'alert_title'         => \Runa_CCA\View\Msg::TITLE_LOGOUT,
                        'login_error_message' => \Runa_CCA\View\Msg::MSG_LOGOUT,
                    ]
                );  
                
                break;

                // PASSWORDCHANGE
                case "PASSWORDCHANGE":

                    $this->app->render(
                        'Main/pass_change.twig', 
                        [
                            'operator_id'         => $args[1],  // Operator ID
                            'validation'          => $args[2],  // Result of Validation
                            'flag'                => $args[3],  // Flag (Update or not)
                            'alert_level'         => $args[4],  // Alert Level
                            'alert_title'         => $args[5],  // Alert Title
                            'popup_msg'           => $args[6],  // Alert Message
                        ]
                    ); 

                    break;
            
        }
    }

    /**
     * Error Function
     * 
     */    
    public function error(){

        $args = func_get_args();
        
        switch($args[0])
        {
            // NOTFOUND
            case "NOTFOUND":

                $this->app->render(
                    'Error/error_frame.twig', 
                    [
                        'error_title'   => \Runa_CCA\View\Msg::TITLE_NOTFOUND,
                        'error_message' => \Runa_CCA\View\Msg::MSG_NOTFOUND,
                    ]
                );

                break;
            
            // ERROR
            case "ERROR":

                $this->app->render(
                    'Error/error_frame.twig', 
                    [
                        'error_title'   => \Runa_CCA\View\Msg::TITLE_ERROR,
                        'error_message' => \Runa_CCA\View\Msg::MSG_ERROR,
                    ]
                );

                break;

            
        }
    }
}