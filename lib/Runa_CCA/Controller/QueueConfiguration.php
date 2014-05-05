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
            $render->display("LOGINERR");

        // Go to main page if the user has already been verified.
        }else{

            // Chek the operator level.
            // The operator with level SYSTEMADMIN or SUPERVISOR can log in.
            if(isset($_SESSION['operator_lv'])){
                
                if ($_SESSION['operator_lv'] > (new \Runa_CCA\Model\OperatorLevel())->getConfigBoarder()){
                    
                    // Display an error and route to login page.
                    $render = new \Runa_CCA\View\Render($app);
                    $render->display("LOGINERR");
                    
                }else{
            
                    switch ($menu){

                        // List queues.
                        case "QUEUELIST":

                            Self::listQueue($app);
                            break;

                        // New queue.
                        case "QUEUENEW":

                            Self::newQueue($app);
                            break;

                        // Add queue.
                        case "QUEUEADD":

                            Self::addQueue($app, $params);
                            break;

                        // Modify queue.
                        case "QUEUEMOD":

                            Self::modQueue($app, $params);
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
     * listQueue
     * 
     * @param \Slim\Slim $app Slim Object
     */   
    static function listQueue($app){

        // DB Connection
        \Runa_CCA\Model\DB::registerIlluminate();

        // Get all queue data.
        $queues = \Runa_CCA\Model\Queue::all();

        $alertLv    = \Runa_CCA\View\Msg::ALERT_INFO;
        $alertTitle = \Runa_CCA\View\Msg::TITLE_INFO;
        $alertMsg   = "キュー一覧画面です。";

        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);
        
        // Go to Queue List page.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "QUEUELIST",
                    $queues,
                    $alertLv,
                    $alertTitle,
                    $alertMsg
                );
    }

    /**
     * newQueue
     * 
     * @param \Slim\Slim $app Slim Object
     */   
    static function newQueue($app){

        // DB Connection
        \Runa_CCA\Model\DB::registerIlluminate();

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
                    "QUEUENEW",
                    NULL,
                    NULL,
                    NULL,
                    $alertLv,
                    $alertTitle,
                    $alertMsg
                );
    }

    /**
     * addQueue
     * 
     * @param \Slim\Slim $app Slim Object
     * @param Array $params Input parameters
     */   
    static function addQueue($app, $params){

        // DB Connection
        \Runa_CCA\Model\DB::registerIlluminate();

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
                        "QUEUENEW",
                        $params,
                        $queueValidate,
                        $flag,
                        $alertLv,
                        $alertTitle,
                        $alertMsg
                    );
            return;
        }

        // Check if the queue exists.
        $existingQueue = \Runa_CCA\Model\Queue::find($params["queue_id"]);

        // Insert the queue if the queue doesn't exists.
        if (empty($existingQueue)){

            $queue = new \Runa_CCA\Model\Queue();
            $queue->queue_id     = $params["queue_id"];
            $queue->queue_name   = $params["queue_name"];
            $queue->action_url   = $params["action_url"];
            $queue->wait_url     = $params["wait_url"];
            $queue->guidance_url = $params["guidance_url"];
            $queue->save();

            // Set Message
            $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
            $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
            $alertMsg   = "キュー追加が成功しました。";

        // If the queue exists.
        }else{

            // Delete the queue if the user wants that.
            if (isset($params["delete"])){

                // Delete the queue data.
                $affectedRowsQue
                        = \Runa_CCA\Model\Queue::where(
                                'queue_id', '=', $params["queue_id"])
                                ->delete();  

                // Set Message
                $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
                $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
                $alertMsg   = "キュー削除が成功しました。";

            // Update the queue if the user wants that.
            }elseif (isset($params["change"])){

                $existingQueue->fill(
                        [
                        "queue_id"     => $params["queue_id"],
                        "queue_name"   => $params["queue_name"],
                        "action_url"   => $params["action_url"],
                        "wait_url"     => $params["wait_url"],
                        "guidance_url" => $params["guidance_url"],
                        ]
                    );

                $existingQueue->save();

                // Set Message
                $alertLv    = \Runa_CCA\View\Msg::ALERT_SUCCESS;
                $alertTitle = \Runa_CCA\View\Msg::TITLE_SUCCESS;
                $alertMsg   = "キュー変更が成功しました。";

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
                            "QUEUENEW",
                            $params,
                            $queueValidate,
                            "ERROR",
                            $alertLv,
                            $alertTitle,
                            $alertMsg
                        );
                return;

            }

        }
        // Get all queue data.
        $queues = \Runa_CCA\Model\Queue::all();

        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);

        // Go to Queue List page.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "QUEUELIST",
                    $queues,
                    $alertLv,
                    $alertTitle,
                    $alertMsg
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
        \Runa_CCA\Model\DB::registerIlluminate();

        // Get the queue data.
        $existingQueue = \Runa_CCA\Model\Queue::find($params["queue_id"]);

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
                    "QUEUENEW",
                    $existingQueue,
                    NULL,
                    "CHANGE",
                    $alertLv,
                    $alertTitle,
                    $alertMsg
                );
    }    
}
