<?php
/**
 * QueueConfiguration Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Controller;

class QueueConfiguration {

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
 
            // Render
            $render = new \Runa_CCA\View\Render($app);
            $render->display("NOAUTH");

        // Go to main page if the user has already been verified.
        }else{

            // Check the operator level.
            // The operator with level SYSTEMADMIN can log in.
            if(isset($_SESSION['operator_lv']) && 
                     $_SESSION['operator_lv'] <= (new \Runa_CCA\Model\Database\OperatorLevel())->getQueueConfigBorder()){

                switch ($menu){

                    // List queues.
                    case "LISTQUEUE":

                        Self::listQueue($app);
                        break;

                    // New queue.
                    case "NEWQUEUE":

                        Self::newQueue($app);
                        break;

                    // Add/Change/Delete queue.
                    case "MNGQUEUE":

                        Self::mngQueue($app, $params);
                        break;

                    // Modify queue.
                    case "MODQUEUE":

                        Self::modQueue($app, $params);
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
     * listQueue
     * 
     * @param \Slim\Slim $app Slim Object
     */   
    static function listQueue($app){

        // DB Connection
        $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

        // Get all queue data.
        $queues = \Runa_CCA\Model\Database\Queue::all();

        $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
        $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
        $alertMsg   = "キュー一覧画面です。";

        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);
        
        // Go to Queue List page.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "QUEUELIST",  // Switch Flag
                    $queues,      // Queue List
                    $alertLv,     // Alert Level
                    $alertTitle,  // Alert Title
                    $alertMsg     // Alert Message
                );
    }

    /**
     * newQueue
     * 
     * @param \Slim\Slim $app Slim Object
     */   
    static function newQueue($app){

        // Set Message
        $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
        $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
        $alertMsg   = "キュー追加画面です。";

        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);
        
        // Go to Queue Add page.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "QUEUENEW",  // Switch Flag
                    NULL,        // Queue List
                    NULL,        // Result of Validation
                    NULL,        // Flag (Update or not)
                    $alertLv,    // Alert Level
                    $alertTitle, // Alert Title
                    $alertMsg    // Alert Message
                );
    }

    /**
     * mngQueue
     * 
     * @param \Slim\Slim $app Slim Object
     * @param Array $params Input parameters
     */   
    static function mngQueue($app, $params){

        // DB Connection
        $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

        // Validate
        $queueValidate = \Runa_CCA\Model\Validator::validateQueue($params);

        // Return Operator Add page if the operation validation failed.
        if ($queueValidate){

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
                        "QUEUENEW",     // Switch Flag
                        $params,        // Queue List
                        $queueValidate, // Result of Validation
                        $flag,          // Flag (Update or not)
                        $alertLv,       // Alert Level
                        $alertTitle,    // Alert Title
                        $alertMsg       // Alert Message
                    );
            return;
        }

        // Check if the queue exists.
        $existingQueue = \Runa_CCA\Model\Database\Queue::find($params["queue_id"]);

        // Insert the queue if the queue doesn't exists.
        if (empty($existingQueue)){

            try{
                
                try{
                    // Begin Transaction.
                    $dbConn->getPdo()->beginTransaction();

                    // Create Twilio information.
                    $twilioClient = (new \Runa_CCA\Model\Twilio())->getTwilioClient();
                    $twilioQueue = $twilioClient->account->queues->create($params["queue_id"],
                                                                   ['MaxSize' => $params["max_size"]]);

                    // Insert the queue data.
                    $queue = new \Runa_CCA\Model\Database\Queue();
                    $queue->queue_id        = $params["queue_id"];
                    $queue->queue_name      = $params["queue_name"];
                    $queue->max_size        = $params["max_size"];
                    $queue->action_url      = $params["action_url"];
                    $queue->wait_url        = $params["wait_url"];
                    $queue->guidance_url    = $params["guidance_url"];
                    $queue->twilio_queue_id = $twilioQueue->sid;
                    $queue->save();

                    // Commit.
                    $dbConn->getPdo()->commit();

                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
                    $alertMsg   = "キュー追加が成功しました。";
    
                }catch(\Services_Twilio_RestException $tre){

                    // Rollback.
                    $dbConn->getPdo()->rollback();

                    // Debug
                    $app->log->debug(strftime("[%Y/%m/%d %H:%M:%S]:".__FILE__.":".__LINE__));
                    $app->log->debug("Services_Twilio_RestException:".$tre->getStatus().":".$tre->getInfo());

                    // Twilio Error
                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;
                    $alertMsg   = "Twilioエラーのため、Queue登録できませんでした。システム管理者に連絡してください";

                }catch(\Exception $pe){

                    // Rollback.
                    $dbConn->getPdo()->rollback();

                    // Delete Twilio information.
                    // The exception will be caught at outer try-catch.
                    $twilioClient = (new \Runa_CCA\Model\Twilio())->getTwilioClient();
                    $twilioClient->account->queues->delete($twilioQueue->sid);

                    // Get Child Exception because PDO Exception is nested.
                    $e = $pe->getPrevious();

                    // Set error message.
                    if($e == NULL){
                        $errorMsg = $pe->getCode().":".$pe->getMessage();
                    }else{
                        $errorMsg = $e->getCode().":".$e->getMessage();
                    }

                    // Debug
                    $app->log->debug(strftime("[%Y/%m/%d %H:%M:%S]:".__FILE__.":".__LINE__));
                    $app->log->debug("Exception:".$errorMsg);

                    // DB Error
                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;
                    $alertMsg   = "DBエラーのため、Queue登録できませんでした。システム管理者に連絡してください";

                }
            
            }catch(\Exception $oe){

                // Debug
                $app->log->debug(strftime("[%Y/%m/%d %H:%M:%S]:".__FILE__.":".__LINE__));
                $app->log->debug("Exception:".$oe->getCode().":".$oe->getMessage());
                
                // Error
                // Set Message
                $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;
                $alertMsg   = "エラーのため、Queue登録できませんでした。Twilioとのデータ不整合の可能性もあります。システム管理者に連絡してください";
            }

        // If the queue exists.
        }else{

            // Delete the queue if the user wants that.
            if (isset($params["delete"])){
                
                try{

                    // Begin Transaction.
                    $dbConn->getPdo()->beginTransaction();
                    
                    // Delete the queue data.
                    $affectedRowsQue
                            = \Runa_CCA\Model\Database\Queue::where(
                                    'queue_id', '=', $params["queue_id"])
                                    ->delete();

                    // Delete Twilio information.
                    $twilioClient = (new \Runa_CCA\Model\Twilio())->getTwilioClient();
                    $twilioClient->account->queues->delete($existingQueue->twilio_queue_id);
                    
                    // Commit.
                    $dbConn->getPdo()->commit();
                    
                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
                    $alertMsg   = "キュー削除が成功しました。";

                }catch(\Services_Twilio_RestException $tre){

                    // Rollback.
                    $dbConn->getPdo()->rollback();
                    
                    // Debug
                    $app->log->debug(strftime("[%Y/%m/%d %H:%M:%S]:".__FILE__.":".__LINE__));
                    $app->log->debug("Services_Twilio_RestException:".$tre->getStatus().":".$tre->getInfo());

                    // Twilio Error
                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;
                    $alertMsg   = "Twilioエラーのため、Queue削除できませんでした。システム管理者に連絡してください";
                    
                }catch(\Exception $pe){
                    
                    // Rollback.
                    $dbConn->getPdo()->rollback();
                    
                    // Get Child Exception because PDO Exception is nested.
                    $e = $pe->getPrevious();
                    
                    // Set error message.
                    if($e == NULL){
                        $errorMsg  = $pe->getCode().":".$pe->getMessage();
                        $errorCode = $pe->getCode();
                    }else{
                        $errorMsg = $e->getCode().":".$e->getMessage();
                        $errorCode = $e->getCode();
                    }

                    // Debug
                    $app->log->debug(strftime("[%Y/%m/%d %H:%M:%S]:".__FILE__.":".__LINE__));
                    $app->log->debug("Exception:".$errorMsg);

                    // DB Error
                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;

                    // Integrity constraint violation
                    if ($errorCode == 23000) {
                        $alertMsg = "Queueに所属しているオペレータが存在するため削除できませんでした。まずオペレータをQueueから外してください。";
                    }else{
                        $alertMsg = "DBエラーのため、Queue削除できませんでした。システム管理者に連絡してください";
                    }

                }            
                
            // Update the queue if the user wants that.
            }elseif (isset($params["change"])){

                try{

                    // Begin Transaction.
                    $dbConn->getPdo()->beginTransaction();
                    
                    // Change the queue data.
                    $existingQueue->fill(
                             [
                             "queue_id"     => $params["queue_id"],
                             "queue_name"   => $params["queue_name"],
                             "max_size"     => $params["max_size"],
                             "action_url"   => $params["action_url"],
                             "wait_url"     => $params["wait_url"],
                             "guidance_url" => $params["guidance_url"],
                             ]
                         );

                    $existingQueue->save();

                    // Update Twilio information.
                    $twilioClient = (new \Runa_CCA\Model\Twilio())->getTwilioClient();
                    $twilioQueue = $twilioClient->account->queues->get($existingQueue->twilio_queue_id);
                    $twilioQueue->update(["MaxSize" => $params["max_size"]]);

                    // Commit.
                    $dbConn->getPdo()->commit();
                     
                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
                    $alertMsg   = "キュー変更が成功しました。";

                }catch(\Services_Twilio_RestException $tre){

                    // Rollback.
                    $dbConn->getPdo()->rollback();
                    
                    // Debug
                    $app->log->debug(strftime("[%Y/%m/%d %H:%M:%S]:".__FILE__.":".__LINE__));
                    $app->log->debug("Services_Twilio_RestException:".$tre->getStatus().":".$tre->getInfo());

                    // Twilio Error
                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;
                    $alertMsg   = "Twilioエラーのため、Queue変更できませんでした。システム管理者に連絡してください";

                }catch(\Exception $pe){
                    
                    // Rollback.
                    $dbConn->getPdo()->rollback();
                    
                    // Get Child Exception because PDO Exception is nested.
                    $e = $pe->getPrevious();
                    
                    // Set error message.
                    if($e == NULL){
                        $errorMsg = $pe->getCode().":".$pe->getMessage();
                    }else{
                        $errorMsg = $e->getCode().":".$e->getMessage();
                    }

                    // Debug
                    $app->log->debug(strftime("[%Y/%m/%d %H:%M:%S]:".__FILE__.":".__LINE__));
                    $app->log->debug("Exception:".$errorMsg);

                    // DB Error
                    // Set Message
                    $alertLv    = \Runa_CCA\View\Msg::ALERT_DANGER;
                    $alertTitle = \Runa_CCA\View\Msg::TITLE_DANGER;
                    $alertMsg   = "DBエラーのため、Queue削除できませんでした。システム管理者に連絡してください";

                }

            // Go to Queue Add page because the queue id is duplicated.
            }else{

                // Set Message
                $alertLv    = \Runa_CCA\View\Msg::ALERT_WARNING;
                $alertTitle = \Runa_CCA\View\Msg::TITLE_WARNING;
                $alertMsg   = "エラーです。キューIDが重複しています。";

                // Set Session Data as global in Twig Template.
                $twig = $app->view()->getEnvironment();
                $twig->addGlobal("session", $_SESSION);
                
                // Go to Queue Add page with the result of the ID check.
                $render = new \Runa_CCA\View\Render($app);
                $queueValidate["queue_id"] = "キューIDが重複しています。";

                $render->display(
                            "QUEUENEW",     // Switch Flag
                            $params,        // Queue List
                            $queueValidate, // Result of Validation
                            "ERROR",        // Flag (Update or not)
                            $alertLv,       // Alert Level
                            $alertTitle,    // Alert Title
                            $alertMsg       // Alert Message
                        );
                return;

            }

        }
        // Get all queue data.
        $queues = \Runa_CCA\Model\Database\Queue::all();

        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);

        // Go to Queue List page.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "QUEUELIST",  // Switch Flag
                    $queues,      // Queue List
                    $alertLv,     // Alert Level
                    $alertTitle,  // Alert Title
                    $alertMsg     // Alert Message
                );
    
    }

    /**
     * modQueue
     * 
     * @param \Slim\Slim $app Slim Object
     * @param Array $params Input parameters
     */   
    static function modQueue($app, $params){
        
        // DB Connection
        $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

        // Get the queue data.
        $existingQueue = \Runa_CCA\Model\Database\Queue::find($params["queue_id"]);

        // Set Message
        $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
        $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
        $alertMsg   = "キュー情報です。";

        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);
        
        // Go to Queue Add page with the information of the queue.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "QUEUENEW",     // Switch Flag
                    $existingQueue, // Queue List
                    NULL,           // Result of Validation
                    "CHANGE",       // Flag (Update or not)
                    $alertLv,       // Alert Level
                    $alertTitle,    // Alert Title
                    $alertMsg       // Alert Message
                );
    }    
}