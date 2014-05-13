<?php
/**
 * Login Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Controller;

class Login {
    
    /**
     * Start
     * 
     */    
    static function start(){
        
        // Go to login page.
        $app = \Slim\Slim::getInstance();
        $render = new \Runa_CCA\View\Render($app);
        $render->display("LOGIN");
    }
    
    /**
     * Login
     * 
     */
    static function login(){

        // Get Parameters
        $app = \Slim\Slim::getInstance();
        $params = $app->request->params();
                
        // Set 'auth' false when a non-verified user comes.
        if(! isset($_SESSION['auth'])){
            $_SESSION['auth'] = false;
        
        // Set 'auth' false when a verified user who wants to relogin comes.
        }elseif($_SESSION['auth'] == true && 
                (isset($params['id']) || isset($params['password']))){
            $_SESSION['auth'] = false;

        // A verified user's 'auth' remains true.
        }

        // Verify the user if 'auth' is not set.
        if($_SESSION['auth'] == false){
            
            // Verify the user if 'id' and 'password' are set.
            if(isset($params['id']) && isset($params['password'])){

                // DB Connection
                $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();
                
                // Search the operator.
                $operator = \Runa_CCA\Model\Database\Operator::find($params['id']);

                // Go to main page if 'id' exists and 'password' is verified.
                if(isset($operator) &&
                        password_verify($params['password'], $operator->password)){

                    // Strengthen Security.
                    session_regenerate_id(true);
                    
                    // Set Session Data.
                    $_SESSION['auth']           = true;
                    $_SESSION['operator_id']    = $operator->operator_id;
                    $_SESSION['operator_lv']    = $operator->operator_level_id;
                    $_SESSION['operator_name']  = $operator->last_name.$operator->first_name;
                    $_SESSION['client_name']    = $operator->client_name;
                    // Set queues the operator has.
                    $_SESSION['operator_queue'] = \Runa_CCA\Model\Database\Operator::find($operator->operator_id)->queue;
                    
                    // Set Session Data as global in Twig Template.
                    $twig = $app->view()->getEnvironment();
                    $twig->addGlobal("session", $_SESSION);
                    
                    // Go to Main page.
                    $render = new \Runa_CCA\View\Render($app);
                    $render->display("CONFIGPORTAL");

                // Display error msg if 'id' and 'password' are not verified.
                }else{

                    // Go to Login page.
                    $render = new \Runa_CCA\View\Render($app);
                    $render->display("LOGINERR");

                }

            // Display error msg if 'id' or 'password' is null.
            }else{

                // Go to Login page.
                $render = new \Runa_CCA\View\Render($app);
                $render->display("LOGINERR");

            }

        // Go to main page if the user has already been verified.
        }else{

            // Set Session Data as global in Twig Template.
            $twig = $app->view()->getEnvironment();
            $twig->addGlobal("session", $_SESSION);

            // Render
            $render = new \Runa_CCA\View\Render($app);
            $render->display("CONFIGPORTAL");

        }
        
    }

    /**
     * Logout
     * 
     */    
    static function logout(){
        
        // Destroy Session
        $_SESSION = array();
        
        if (isset($_COOKIE[session_name()])){
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
        
        // Go back to login page.
        $app = \Slim\Slim::getInstance();
        $render = new \Runa_CCA\View\Render($app);
        $render->display("LOGOUT");
        
    }

    /**
     * password
     * 
     * @param String $menu Menu Name
     */   
    static function password($menu){

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

        // Switch the page if the user has already been verified.
        }else{
            
            // Check the operator level.
            // The operator with level SYSTEMADMIN or SUPERVISOR can log in.
            if(isset($_SESSION['operator_lv'])){
                
                if ($_SESSION['operator_lv'] > (new \Runa_CCA\Model\Database\OperatorLevel())->getOpConfigBorder()){
                    
                    // If the operator wants to change his/her own password, change it.
                    if($params["operator_id"] == $_SESSION["operator_id"]){
                        
                        Self::changePassword($app,$params);
                        
                    // If the operator wants to change other's password, deny it.
                    }else{
                        
                        // Display an error and route to login page.
                        $render = new \Runa_CCA\View\Render($app);
                        $render->display("LOGINERR");
                        
                    }
                    
                }else{
            
                    switch ($menu){

                        // Change password.
                        case "CHANGEPASSWORD":

                            Self::changePassword($app,$params);
                            break;


                        // Go to Error
                        default :

                            \Runa_CCA\Controller\Error::display("ERROR");
                            break;

                    }
                }
                
            // No Session value operator_lv
            }else{

                // Display an error and route to login page.
                $render = new \Runa_CCA\View\Render($app);
                $render->display("LOGINERR");
                
            }

        }
    }
    
    /**
     * changePassword
     * 
     * @param \Slim\Slim $app Slim Object
     * @param Array $params Input parameters
     */   
    static function changePassword($app, $params){
            
        // DB Connection
        $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

        // Validate
        $passwordValidate = \Runa_CCA\Model\Validator::validatePassword($params);

        // Get the operator information.
        $existingOperator = \Runa_CCA\Model\Database\Operator::find($params["operator_id"]);

        // Go to Error page if the operator validation failed. This is an illegal access.
        if(empty($existingOperator)){

            \Runa_CCA\Controller\Error::display("ERROR");

        // Return Password Change page if the password validation failed.
        }elseif ($passwordValidate){

            // Set Message
            $alertLv    = \Runa_CCA\View\Msg::ALERT_WARNING;
            $alertTitle = \Runa_CCA\View\Msg::TITLE_WARNING;
            $alertMsg   = "パスワードを確認してください。";

            // Set Session Data as global in Twig Template.
            $twig = $app->view()->getEnvironment();
            $twig->addGlobal("session", $_SESSION);

            // Go back to Password Change page with the result of the validation.
            $render = new \Runa_CCA\View\Render($app);
            $render->display(
                            "PASSWORDCHANGE",         // Switch Flag
                            $params["operator_id"],   // Operator ID
                            $passwordValidate,        // Result of Validation
                            "ERROR",                  // Flag (Update or not)
                            $alertLv,                 // Alert Level
                            $alertTitle,              // Alert Title
                            $alertMsg                 // Alert Message 
                       );

        // Change password.
        }else{

            // Update the operator password.
            $existingOperator->fill(
                    [
                    "operator_id" => $params["operator_id"],
                    "password"    => password_hash($params["password1"], PASSWORD_DEFAULT),
                    ]
                );

            $existingOperator->save();

            // Get All operators data.
            $operators = \Runa_CCA\Model\Database\Operator::all();

            // Get All operator level.
            $oplevels = (new \Runa_CCA\Model\Database\OperatorLevel())->getOperatorLevels();

            // Set Message
            $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
            $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
            $alertMsg   = "パスワード変更に成功しました。";

            // Set Session Data as global in Twig Template.
            $twig = $app->view()->getEnvironment();
            $twig->addGlobal("session", $_SESSION);

            // If the operator wants to change its password, return to the config portal.
            if($params["operator_id"] == $_SESSION["operator_id"]){

                    // Go to Portal page
                    $render = new \Runa_CCA\View\Render($app);
                    $render->display(
                                    "CONFIGPORTAL",  // Switch Flag
                                    $alertLv,        // Alert Level
                                    $alertTitle,     // Alert Title
                                    $alertMsg        // Alert Message
                                );

            }else{

                // Go to Operator List page.
                $render = new \Runa_CCA\View\Render($app);
                $render->display(
                                "OPERATORLIST",  // Switch Flag
                                $operators,      // Operator List
                                $oplevels,       // Operator Level List
                                $alertLv,        // Alert Level
                                $alertTitle,     // Alert Title
                                $alertMsg        // Alert Message
                            );

            }
        }
    }
    
    /**
     * changePasswordSelf
     * 
     */   
    static function changePasswordSelf(){

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

        // Go to Password Change page if the user has already been verified.
        }else{
            
            // Set Message
            $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
            $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
            $alertMsg   = "パスワード変更画面です。";

            // Set Session Data as global in Twig Template.
            $twig = $app->view()->getEnvironment();
            $twig->addGlobal("session", $_SESSION);
            
            // Go to Password Change page with the information of the operator.
            $render = new \Runa_CCA\View\Render($app);
            $render->display(
                            "PASSWORDCHANGE",         // Switch Flag
                            $_SESSION['operator_id'], // Operator ID
                            NULL,                     // Result of Validation
                            NULL,                     // Flag (Update or not)
                            $alertLv,                 // Alert Level
                            $alertTitle,              // Alert Title
                            $alertMsg                 // Alert Message 
                        );
        }
    }    
}
