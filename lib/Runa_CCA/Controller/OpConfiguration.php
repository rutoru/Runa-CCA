<?php
/**
 * OpConfiguration Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Controller;

class OpConfiguration {

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
            // The operator with level SYSTEMADMIN or SUPERVISOR can log in.
            if(isset($_SESSION['operator_lv']) && 
                     $_SESSION['operator_lv'] <= (new \Runa_CCA\Model\Database\OperatorLevel())->getOpConfigBorder()){

                switch ($menu){

                    // List operators.
                    case "LISTOPERATOR":

                        Self::listOperator($app);
                        break;

                    // New operator.
                    case "NEWOPERATOR":

                        Self::newOperator($app);
                        break;

                    // Add/Change/Delete operator.
                    case "MNGOPERATOR":

                        Self::mngOperator($app,$params);
                        break;

                    // Modify operator.
                    case "MODOPERATOR":

                        Self::modOperator($app,$params);
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
     * listOperator
     * 
     * @param \Slim\Slim $app Slim Object
     */   
    static function listOperator($app){
        
        // DB Connection
        $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

        // Set Message
        $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
        $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
        $alertMsg   = "オペレータ一覧画面です。";

        // Get All operators data.
        $operators = \Runa_CCA\Model\Database\Operator::all();
        // Get All operator level.
        $oplevels = (new \Runa_CCA\Model\Database\OperatorLevel())->getOperatorLevels();

        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);

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
    
    /**
     * newOperator
     * 
     * @param \Slim\Slim $app Slim Object
     */   
    static function newOperator($app){
        
        // DB Connection
        $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

        // Get All queue data
        $queues   = \Runa_CCA\Model\Database\Queue::all();
        $oplevels = (new \Runa_CCA\Model\Database\OperatorLevel())->getOperatorLevels();
        
        // Set Message
        $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
        $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
        $alertMsg   = "オペレータ追加画面です。";

        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);
        
        // Go to Operator Add page.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "OPERATORNEW",  // Switch Flag
                    $queues,        // Queue List
                    NULL,           // Operator List
                    NULL,           // Queues the operator has
                    $oplevels,      // Operator Levels
                    NULL,           // Operator Level the operator has
                    NULL,           // Result of Validation
                    NULL,           // Flag (Update or not)
                    $alertLv,       // Alert Level
                    $alertTitle,    // Alert Title
                    $alertMsg       // Alert Message
                );
        
    }

    /**
     * mngOperator
     * 
     * @param \Slim\Slim $app Slim Object
     * @param Array $params Input parameters
     */   
    static function mngOperator($app, $params){
        
        // DB Connection
        $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

        // Validate operator
        $operatorValidate = \Runa_CCA\Model\Validator::validateOperator($params);
        
        // Validate Operator Levels
        $operatorLevels = (new \Runa_CCA\Model\Database\OperatorLevel())->getOperatorLevels();
        $operatorLevelValidate = \Runa_CCA\Model\Validator::validateOpLevel($operatorLevels, $params["operator_level"]);

        // Validate operator queue data
        $queues = \Runa_CCA\Model\Database\Queue::all();
        $queueValidate = \Runa_CCA\Model\Validator::validateOpQueue($queues,$params["operator_queues"]);

        // Go to Error page if the queue and operator level validation failed. This is an illegal access.
        if($queueValidate || $operatorLevelValidate){

            \Runa_CCA\Controller\Error::display("ERROR");
            return;

        // Return Operator Add page if the operation validation failed.
        }elseif ($operatorValidate){

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
                        "OPERATORNEW",              // Switch Flag
                        $queues,                    // Queue List
                        $params,                    // Operator List
                        $params["operator_queues"], // Queues the operator has
                        $operatorLevels,            // Operator Levels
                        $params["operator_level"],  // Operator Level the operator has
                        $operatorValidate,          // Result of Validation
                        $flag,                      // Flag (Update or not)
                        $alertLv,                   // Alert Level
                        $alertTitle,                // Alert Title
                        $alertMsg                   // Alert Message   
                    );
                     
            return;
        }

        // Check if the operator exists.
        $existingOperator = \Runa_CCA\Model\Database\Operator::find($params["operator_id"]);

        // Insert the operator if the operator doesn't exist.
        if (empty($existingOperator)){

            try{
                
                // Begin Transaction.
                $dbConn->getPdo()->beginTransaction();
                
                // Insert the operator data.
                $operator = new \Runa_CCA\Model\Database\Operator();
                $operator->operator_id       = $params["operator_id"];
                $operator->password          = password_hash($params["password"], PASSWORD_DEFAULT);
                $operator->last_name         = $params["last_name"];
                $operator->first_name        = $params["first_name"];
                $operator->client_name       = $params["client_name"];
                $operator->telnum            = $params["telnum"];
                $operator->operator_level_id = $params["operator_level"];
                $operator->save();

                // Insert the queues the operator has.
                foreach ($params["operator_queues"] as $queue){

                    $existingQueue = new \Runa_CCA\Model\Database\OperatorQueue();
                    $existingQueue->fill(
                            [
                            "operator_id" => $params["operator_id"],
                            "queue_id"    => $queue,
                            ]
                        );

                    $existingQueue->save();
                }

                // Commit.
                $dbConn->getPdo()->commit();
                
                // Set Message
                $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
                $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
                $alertMsg   = "オペレータ追加が成功しました。";

                
            }catch(\Exception $e){

                // Rollback.
                $dbConn->getPdo()->rollback();

                // Debug
                $app->log->debug(strftime("[%Y/%m/%d %H:%M:%S]:".__FILE__.":".__LINE__));
                $app->log->debug("Exception:".$e->getCode().":".$e->getMessage());

                // Set Message
                $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;
                $alertMsg   = "DBエラーのため、オペレータ追加ができませんでした。システム管理者に連絡してください";
                
            }            


        // If the operator exists.
        }else{

            // Delete the operator if the user wants that.
            if (isset($params["delete"])){

                try{

                    // Begin Transaction.
                    $dbConn->getPdo()->beginTransaction();
                
                    // Delete the operator_queue data.
                    $affectedRowsQue
                            = \Runa_CCA\Model\Database\OperatorQueue::where(
                                    'operator_id', '=', $params["operator_id"])
                                    ->delete();

                    // Delete operator data.
                    $affectedRowsOpe
                            = \Runa_CCA\Model\Database\Operator::where(
                                    'operator_id', '=', $params["operator_id"])
                                    ->delete();  

                    // Commit.
                    $dbConn->getPdo()->commit();

                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
                    $alertMsg   = "オペレータ削除が成功しました。";

                }catch(\Exception $e){

                    // Rollback.
                    $dbConn->getPdo()->rollback();

                    // Debug
                    $app->log->debug(strftime("[%Y/%m/%d %H:%M:%S]:".__FILE__.":".__LINE__));
                    $app->log->debug("Exception:".$e->getCode().":".$e->getMessage());

                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;
                    $alertMsg   = "DBエラーのため、オペレータ削除ができませんでした。システム管理者に連絡してください";

                }
                
            // Update the queue if the user wants that.
            }elseif (isset($params["change"])){

                try{
                
                    // Begin Transaction.
                    $dbConn->getPdo()->beginTransaction();

                    // Change the operator data.
                    $existingOperator->fill(
                            [
                            "operator_id"       => $params["operator_id"],
                            "last_name"         => $params["last_name"],
                            "first_name"        => $params["first_name"],
                            "client_name"       => $params["client_name"],
                            "telnum"            => $params["telnum"],
                            "operator_level_id" => $params["operator_level"],
                            ]
                        );

                    $existingOperator->save();

                    // Delete queue data.
                    $affectedRows = \Runa_CCA\Model\Database\OperatorQueue::where(
                                        'operator_id', '=', $params["operator_id"])
                                        ->delete();

                    // Set queue data.
                    foreach ($params["operator_queues"] as $queue){

                        $existingQueue = new \Runa_CCA\Model\Database\OperatorQueue();
                        $existingQueue->fill(
                                [
                                "operator_id" => $params["operator_id"],
                                "queue_id"    => $queue,
                                ]
                            );

                        $existingQueue->save();

                    }                        

                    // Commit.
                    $dbConn->getPdo()->commit();

                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
                    $alertMsg   = "オペレータ変更が成功しました。";
                
                }catch(\Exception $e){

                    // Rollback.
                    $dbConn->getPdo()->rollback();

                    // Debug
                    $app->log->debug(strftime("[%Y/%m/%d %H:%M:%S]:".__FILE__.":".__LINE__));
                    $app->log->debug("Exception:".$e->getCode().":".$e->getMessage());

                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;
                    $alertMsg   = "DBエラーのため、オペレータ変更ができませんでした。システム管理者に連絡してください";

                }

            // Go to Queue Add page because the operator id is duplicated.
            }else{

                // Set Message
                $alertLv    = \Runa_CCA\View\Msg::ALERT_WARNING;
                $alertTitle = \Runa_CCA\View\Msg::TITLE_WARNING;
                $alertMsg   = "エラーです。オペレータIDが重複しています。";

                // Go to Operator Add page with the result of the ID check.
                $render = new \Runa_CCA\View\Render($app);
                $operatorValidate["operator_id"] = "オペレータIDが重複しています。";

                // Set Session Data as global in Twig Template.
                $twig = $app->view()->getEnvironment();
                $twig->addGlobal("session", $_SESSION);
                
                $render->display(
                            "OPERATORNEW",              // Switch Flag
                            $queues,                    // Queue List
                            $params,                    // Operator List
                            $params["operator_queues"], // Queues the operator has
                            $operatorLevels,            // Operator Levels
                            $params["operator_level"],  // Operator Level the operator has
                            $operatorValidate,          // Result of Validation
                            "ERROR",                    // Flag (Update or not)
                            $alertLv,                   // Alert Level
                            $alertTitle,                // Alert Title
                            $alertMsg                   // Alert Message   
                        );
                return;
                
            }

        }
        // Get All operators data
        $operators = \Runa_CCA\Model\Database\Operator::all();

        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);
        
        // Go to Operator List page.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "OPERATORLIST", // Switch Flag
                    $operators,     // Operator List
                    $operatorLevels,// Operator Level List
                    $alertLv,       // Alert Level
                    $alertTitle,    // Alert Title
                    $alertMsg       // Alert Message 
                );        
    }        
    
    /**
     * modOperator
     * 
     * @param \Slim\Slim $app Slim Object
     * @param Array $params Input parameters
     */   
    static function modOperator($app,$params){
        
        // Go to Password Change page.
        if (isset($params["change_pass"])){

            // Set Message
            $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
            $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
            $alertMsg   = "パスワード変更画面です。";

            // Set Session Data as global in Twig Template.
            $twig = $app->view()->getEnvironment();
            $twig->addGlobal("session", $_SESSION);
            
            // Go to Operator Add page with the information of the operator.
            $render = new \Runa_CCA\View\Render($app);
            $render->display(
                        "PASSWORDCHANGE",       // Switch Flag
                        $params['operator_id'], // Operator ID
                        NULL,                   // Result of Validation
                        NULL,                   // Flag (Update or not)
                        $alertLv,               // Alert Level
                        $alertTitle,            // Alert Title
                        $alertMsg               // Alert Message 
                    );
            return;

        }else{

            // Declar for the operator who doesn't have any queues.
            $selectedQueues = array();
            
            // DB Connection
            $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

            // Get all queue data
            $queues = \Runa_CCA\Model\Database\Queue::all();

            // Get Operator Levels
            $operatorLevels = (new \Runa_CCA\Model\Database\OperatorLevel())->getOperatorLevels();
            
            // Get all queue data of the operator.
            $opqueues = \Runa_CCA\Model\Database\OperatorQueue::where('operator_id', '=', $params["operator_id"])->get();
            foreach ($opqueues as $opqueue){
                $selectedQueues[] = $opqueue->queue_id;
            }

            // Get the operator data.
            $existingOperator = \Runa_CCA\Model\Database\Operator::find($params["operator_id"]);

            // Set Message
            $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
            $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
            $alertMsg   = "オペレータ情報です。";

            // Set Session Data as global in Twig Template.
            $twig = $app->view()->getEnvironment();
            $twig->addGlobal("session", $_SESSION);

            // Go to Operator Add page with the information of the operator.
            $render = new \Runa_CCA\View\Render($app);
            $render->display(
                        "OPERATORNEW",                       // Switch Flag
                        $queues,                             // Queue List
                        $existingOperator,                   // Operator List
                        $selectedQueues,                     // Queues the operator has
                        $operatorLevels,                     // Operator Levels
                        $existingOperator["operator_level_id"], // Operator Level the operator has
                        NULL,                                // Result of Validation
                        "CHANGE",                            // Flag (Update or not)
                        $alertLv,                            // Alert Level
                        $alertTitle,                         // Alert Title
                        $alertMsg                            // Alert Message  
                    );

            return;
        }        
    }
    
}
