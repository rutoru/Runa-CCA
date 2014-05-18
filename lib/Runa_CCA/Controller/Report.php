<?php
/**
 * Report Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Controller;

class Report {

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

                    // Wallboard.
                    case "WALLBOARD":

                        Self::displayQueues($app, $params);
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
     * displayQueues
     * 
     * @param \Slim\Slim $app Slim Object
     * @param Array $params Input parameters
     */   
    static function displayQueues($app, $params){
        
        // Initialize waitTimes value.
        $waitTimes = [0];
        
        // Initialize wallboard.
        $wallboard = array();
        
        // Get Twilio information.
        $twilioClient = (new \Runa_CCA\Model\Twilio())->getTwilioClient();
        
        // DB Connection
        $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();

        // Select twilio_queue_id.
        // When an error occurs, Slim will catch the error and display error message according to the setting in Route class.
        $stmt = $dbConn->getPdo()->prepare(
                            'SELECT queue.twilio_queue_id FROM queue '.
                            'INNER JOIN operator_queue '.
                                'ON operator_queue.queue_id = queue.queue_id '.
                                'AND operator_queue.operator_id = :operator_id'
                            );
        $stmt->bindValue('operator_id', $_SESSION['operator_id']);
        $stmt->execute();

        // Get the queue values.
        foreach ($stmt->fetchAll() as $twilioQueueId){
 
            // Get data from Twilio.
            $twilioQueue = $twilioClient->account->queues->get($twilioQueueId["twilio_queue_id"]);
                
            // Get wait_time.
            foreach ($twilioQueue->members as $member) {
                $waitTimes[] = $member->wait_time;
            }

            // Create Wallboard object.
            $wallboard[] = new \Runa_CCA\Model\Wallboard(
                                $twilioQueue->sid, 
                                $twilioQueue->friendly_name, 
                                $twilioQueue->current_size,
                                $twilioQueue->max_size,
                                $twilioQueue->average_wait_time,
                                max($waitTimes)
                                );
                
        }
        
        // Set Session Data as global in Twig Template.
        $twig = $app->view()->getEnvironment();
        $twig->addGlobal("session", $_SESSION);

        // Go to Operator Add page with the result of the validation.
        $render = new \Runa_CCA\View\Render($app);
        $render->display(
                    "WALLBOARD",     // Switch Flag
                    $wallboard       // Wallboard Object
                );

        
    }
    
}
