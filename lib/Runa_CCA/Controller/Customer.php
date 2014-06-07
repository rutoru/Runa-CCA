<?php
/**
 * Customer Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Controller;

class Customer {

    /**
     * portal
     * 
     * @param String $menu Menu Name
     */   
    static function portal($menu){

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

        // Switch the page if the user has already been verified.
        }else{
            
            // Check the operator level.
            // The operator with level SYSTEMADMIN or SUPERVISOR or OPERATOR can log in.
            if(isset($_SESSION['operator_lv']) && 
                     $_SESSION['operator_lv'] <= (new \Runa_CCA\Model\Database\OperatorLevel())->getReportBorder()){

                switch ($menu){

                    // List customers.
                    case "LISTCUSTOMER":

                        Self::listCustomers($app, $params);
                        break;

                    // New customer.
                    case "NEWCUSTOMER":

                        Self::newCustomer($app);
                        break;

                    // Add/Change/Delete customer.
                    case "MNGCUSTOMER":

                        Self::mngCustomer($app, $params);
                        break;

                    // Search customer.
                    case "SEARCHCUSTOMER":

                        Self::searchCustomer($app, $params);
                        break;
                    
                    // Modify customer.
                    case "MODCUSTOMER":

                        Self::modCustomer($app, $params);
                        break;
                    
                    // Go to Error
                    default :

                        \Runa_CCA\Controller\Error::display("ERROR");
                        break;

                    }
                    
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

    
    /**
     * listCustomers
     * 
     * @param \Slim\Slim $app Slim Object
     * @param Array $params Input parameters
     */   
    static function listCustomers($app, $params){
        
        // Initialize customerList.
        $customerList = array();
        
        $stmt = null;
        
        // DB Connection
        $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

        // Select twilio_customer_id.
        // When an error occurs, Slim will catch the error and display error message according to the setting in Route class.        
        if (isset($params['id'])){
            
            $stmt = $dbConn->getPdo()->prepare(
                                'SELECT c.customer_id, c.last_name, c.first_name, c.telnum, q.queue_id, qd.To '.
                                'FROM customer c '.
                                'INNER JOIN queue_data qd '.
                                    'ON qd.DequeingCallSid = :call_sid '.
                                    'AND c.telnum = qd.From '.
                                'INNER JOIN queue q '.
                                    'ON q.twilio_queue_id = qd.QueueSid'
                                );
            $stmt->bindValue('call_sid', $params['id']);
            $stmt->execute();
            
        }else{

            $stmt = $dbConn->getPdo()->prepare(
                                'SELECT c.customer_id, c.last_name, c.first_name, c.telnum '.
                                'FROM customer c '
                                );
            $stmt->execute();
            
        }

        // Get the customer list values.
        foreach ($stmt->fetchAll() as $customer){

            // Set Default Value.
            if(!isset($customer["queue_id"])) $customer["queue_id"] = null;
            if(!isset($customer["To"])) $customer["To"] = null;
            
            // Create Customer object.
            $customerList[] = new \Runa_CCA\Model\Customer(
                                $customer["customer_id"],
                                $customer["last_name"],
                                $customer["first_name"],
                                $customer["telnum"],
                                $customer["queue_id"],
                                $customer["To"],
                                null
                                );
            
        }

        // Set Message
        $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
        $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
        $alertMsg   = "お客様一覧画面です。";
        
        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);

        // Go to Operator Add page with the result of the validation.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "CUSTOMERLIST",     // Switch Flag
                    $customerList,      // Customer Object
                    NULL,
                    $alertLv,           // Alert Level
                    $alertTitle,        // Alert Title
                    $alertMsg           // Alert Message
                );

        
    }
    
    /**
     * newCustomer
     * 
     * @param \Slim\Slim $app Slim Object
     */   
    static function newCustomer($app){

        // Set Message
        $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
        $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
        $alertMsg   = "お客様追加画面です。";

        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);
        
        // Go to Customer Add page.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "CUSTOMERNEW",  // Switch Flag
                    NULL,        // Customer List
                    NULL,        // Result of Validation
                    NULL,        // Flag (Update or not)
                    $alertLv,    // Alert Level
                    $alertTitle, // Alert Title
                    $alertMsg    // Alert Message
                );
    }

    /**
     * mngCustomer
     * 
     * @param \Slim\Slim $app Slim Object
     * @param Array $params Input parameters
     */   
    static function mngCustomer($app, $params){
        
        $flag = null;

        // DB Connection
        $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

        // Validate
        $customerValidate = \Runa_CCA\Model\Validator::validateCustomer($params);

        // Return Operator Add page if the operation validation failed.
        if ($customerValidate){

            // Set Message
            $alertLv    = \Runa_CCA\View\Msg::ALERT_WARNING;
            $alertTitle = \Runa_CCA\View\Msg::TITLE_WARNING;
            $alertMsg   = "入力に誤りがあります。";

            // Check tag valut.
            if(isset($params["tag"])){
                $flag = $params["tag"]; 
            }else{
                $flag = "NEW";
            }
            
            // Set Session Data as global in Twig Template.
            $twig = $app->view()->getEnvironment();
            $twig->addGlobal("session", $_SESSION);
            
            // Go to Operator Add page with the result of the validation.
            $render = new \Runa_CCA\View\Render($app);
            $render->display(
                        "CUSTOMERNEW",     // Switch Flag
                        $params,        // Customer List
                        $customerValidate, // Result of Validation
                        $flag,          // Flag (Update or not)
                        $alertLv,       // Alert Level
                        $alertTitle,    // Alert Title
                        $alertMsg       // Alert Message
                    );
            return;
        }

        // Check if the customer exists.
        $existingCustomer = \Runa_CCA\Model\Database\Customer::find($params["customer_id"]);

        // Insert the customer if the customer doesn't exists.
        if (empty($existingCustomer)){

            try{
                
                    // Begin Transaction.
                    $dbConn->getPdo()->beginTransaction();
                
                    // Insert the customer data.
                    $customer = new \Runa_CCA\Model\Database\Customer();
                    $customer->customer_id    = $params["customer_id"];
                    $customer->last_name      = $params["last_name"];
                    $customer->first_name     = $params["first_name"];
                    $customer->telnum         = $params["telnum"];
                    $customer->contact_record = $params["contact_record"];
                    $customer->save();

                    // Commit.
                    $dbConn->getPdo()->commit();

                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
                    $alertMsg   = "お客様追加が成功しました。";
    
            
            }catch(\Exception $oe){
                
                // Error
                // Set Message
                $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;
                $alertMsg   = "エラーのため、Customer登録できませんでした。システム管理者に連絡してください";
            }

        // If the customer exists.
        }else{

            // Delete the customer if the user wants that.
            if (isset($params["delete"])){
                
                try{

                    // Begin Transaction.
                    $dbConn->getPdo()->beginTransaction();
                    
                    // Delete the customer data.
                    $affectedRowsQue
                            = \Runa_CCA\Model\Database\Customer::where(
                                    'customer_id', '=', $params["customer_id"])
                                    ->delete();

                    // Commit.
                    $dbConn->getPdo()->commit();
                    
                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
                    $alertMsg   = "お客様削除が成功しました。";
                    
                }catch(\Exception $pe){
                    
                    // Rollback.
                    $dbConn->getPdo()->rollback();
                    
                    // DB Error
                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;
                    $alertMsg = "DBエラーのため、Customer削除できませんでした。システム管理者に連絡してください";

                }            
                
            // Update the customer if the user wants that.
            }elseif (isset($params["change"])){

                try{

                    // Begin Transaction.
                    $dbConn->getPdo()->beginTransaction();
                    
                    // Change the customer data.
                    $existingCustomer->fill(
                             [
                             "customer_id"    => $params["customer_id"],
                             "last_name"      => $params["last_name"],
                             "first_name"     => $params["first_name"],
                             "telnum"         => $params["telnum"],
                             "contact_record" => $params["contact_record"],
                             ]
                         );

                    $existingCustomer->save();

                    // Commit.
                    $dbConn->getPdo()->commit();
                     
                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
                    $alertMsg   = "お客様変更が成功しました。";

                }catch(\Exception $pe){
                    
                    // Rollback.
                    $dbConn->getPdo()->rollback();

                    // DB Error
                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;
                    $alertMsg   = "DBエラーのため、Customer削除できませんでした。システム管理者に連絡してください";

                }

            // Go to Customer Add page because the customer id is duplicated.
            }else{

                // Set Message
                $alertLv    = \Runa_CCA\View\Msg::ALERT_WARNING;
                $alertTitle = \Runa_CCA\View\Msg::TITLE_WARNING;
                $alertMsg   = "エラーです。お客様IDが重複しています。";

                // Set Session Data as global in Twig Template.
                $twig = $app->view()->getEnvironment();
                $twig->addGlobal("session", $_SESSION);
                
                // Go to Customer Add page with the result of the ID check.
                $render = new \Runa_CCA\View\Render($app);
                $customerValidate["customer_id"] = "お客様IDが重複しています。";

                $render->display(
                            "CUSTOMERNEW",     // Switch Flag
                            $params,        // Customer List
                            $customerValidate, // Result of Validation
                            "ERROR",        // Flag (Update or not)
                            $alertLv,       // Alert Level
                            $alertTitle,    // Alert Title
                            $alertMsg       // Alert Message
                        );
                return;

            }

        }
        // Get all customer data.
        $customers = \Runa_CCA\Model\Database\Customer::all();

        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);

        // Go to Customer List page.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "CUSTOMERLIST",  // Switch Flag
                    $customers,      // Customer List
                    NULL,
                    $alertLv,        // Alert Level
                    $alertTitle,     // Alert Title
                    $alertMsg       // Alert Message
                );
    
    }

    /**
     * modCustomer
     * 
     * @param \Slim\Slim $app Slim Object
     * @param Array $params Input parameters
     */   
    static function modCustomer($app, $params){
        
        // DB Connection
        $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

        // Get the customer data.
        $existingCustomer = \Runa_CCA\Model\Database\Customer::find($params["customer_id"]);

        // Set Message
        $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
        $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
        $alertMsg   = "お客様情報です。";

        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);
        
        // Go to Customer Add page with the information of the customer.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "CUSTOMERNEW",     // Switch Flag
                    $existingCustomer, // Customer List
                    NULL,           // Result of Validation
                    "CHANGE",       // Flag (Update or not)
                    $alertLv,       // Alert Level
                    $alertTitle,    // Alert Title
                    $alertMsg       // Alert Message
                );
    }

    /**
     * searchCustomer
     * 
     * @param \Slim\Slim $app Slim Object
     * @param Array $params Input parameters
     */   
    static function searchCustomer($app, $params){

        // Initialize customerList.
        $customerList = array();
        
        // Set Default Value.
        if(!isset($params["customer_id"])) $params["customer_id"] = null;
        if(!isset($params["last_name"]))   $params["last_name"] = null;
        if(!isset($params["first_name"]))  $params["first_name"] = null;
        if(!isset($params["telnum"]))      $params["telnum"] = null;

        // Validate
        $customerSearchValidate = \Runa_CCA\Model\Validator::validateCustomerSearch($params);

        // Return Error Message if the search validation failed.
        if ($customerSearchValidate){
            
            // Set Message
            $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
            $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
            $alertMsg   = "お客様一覧です。";
            
            // Set Session Data as global in Twig Template.
            $twig = $app->view()->getEnvironment();
            $twig->addGlobal("session", $_SESSION);

            // Go to Operator Add page with the result of the validation.
            $render = new \Runa_CCA\View\Render($app);
            $render->display(
                        "CUSTOMERLIST",          // Switch Flag
                        $customerList,           // Customer Object
                        $customerSearchValidate, // Result of Validation
                        $alertLv,                // Alert Level
                        $alertTitle,             // Alert Title
                        $alertMsg                // Alert Message
                    );
            
        }else{
        
            // DB Connection
            $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

            // Search Customers.
            $stmt = $dbConn->getPdo()->prepare(
                                'SELECT c.customer_id, c.last_name, c.first_name, c.telnum '.
                                'FROM customer c '.
                                'WHERE c.customer_id LIKE :customer_id '.
                                   'AND c.last_name   LIKE :last_name '.
                                   'AND c.first_name  LIKE :first_name '.
                                   'AND c.telnum      LIKE :telnum '
                                );
            $stmt->bindValue('customer_id', "%".$params['customer_id']."%");
            $stmt->bindValue('last_name',   "%".$params['last_name']."%");
            $stmt->bindValue('first_name',  "%".$params['first_name']."%");
            $stmt->bindValue('telnum',      "%".$params['telnum']."%");
            $stmt->execute();

            // Get the customer list values.
            foreach ($stmt->fetchAll() as $customer){

                // Set Default Value.
                if(!isset($customer["queue_id"])) $customer["queue_id"] = null;
                if(!isset($customer["To"])) $customer["To"] = null;

                // Create Customer object.
                $customerList[] = new \Runa_CCA\Model\Customer(
                                    $customer["customer_id"],
                                    $customer["last_name"],
                                    $customer["first_name"],
                                    $customer["telnum"],
                                    $customer["queue_id"],
                                    $customer["To"],
                                    null
                                    );

            }

            // Set Message
            $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
            $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
            $alertMsg   = "お客様一覧です。";

            // Set Session Data as global in Twig Template.
            $twig = $app->view()->getEnvironment();
            $twig->addGlobal("session", $_SESSION);

            // Go to Operator Add page with the result of the validation.
            $render = new \Runa_CCA\View\Render($app);
            $render->display(
                        "CUSTOMERLIST",     // Switch Flag
                        $customerList,      // Customer Object
                        NULL,
                        $alertLv,           // Alert Level
                        $alertTitle,        // Alert Title
                        $alertMsg           // Alert Message
                    );
        }
        
    }
    
}
