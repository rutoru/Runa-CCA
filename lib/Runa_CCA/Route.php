<?php
/**
 * Route Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA;

class Route{
    
    /**
     * registration
     * 
     * @param \Slim\Slim Object
     */    
    static function registration($app){
    
        // Login
        $app->get('/',                 function(){\Runa_CCA\Controller\Login::start();});
        $app->map('/login',            function(){\Runa_CCA\Controller\Login::login();})->via('GET', 'POST');
        $app->get('/logout',           function(){\Runa_CCA\Controller\Login::logout();});

        // Configuration
        $app->map ('/conf',            function(){\Runa_CCA\Controller\Configuration::portal();})->via('GET', 'POST');
        $app->map ('/conf/oplist',     function(){\Runa_CCA\Controller\OpConfiguration::portal("OPERATORLIST");})->via('GET', 'POST');
        $app->map ('/conf/opnew',      function(){\Runa_CCA\Controller\OpConfiguration::portal("OPERATORNEW");})->via('GET', 'POST');
        $app->post('/conf/opadd',      function(){\Runa_CCA\Controller\OpConfiguration::portal("OPERATORADD");});
        $app->post('/conf/opmod',      function(){\Runa_CCA\Controller\OpConfiguration::portal("OPERATORMOD");});
        $app->post('/conf/passchg',    function(){\Runa_CCA\Controller\Login::password("CHANGEPASSWORD");});
        $app->get ('/self/passchgdisp',function(){\Runa_CCA\Controller\Login::changePasswordSelf();});
        $app->map ('/conf/queuelist',  function(){\Runa_CCA\Controller\QueueConfiguration::portal("QUEUELIST");})->via('GET', 'POST');
        $app->map ('/conf/queuenew',   function(){\Runa_CCA\Controller\QueueConfiguration::portal("QUEUENEW");})->via('GET', 'POST');
        $app->post('/conf/queueadd',   function(){\Runa_CCA\Controller\QueueConfiguration::portal("QUEUEADD");});
        $app->post('/conf/queuemod',   function(){\Runa_CCA\Controller\QueueConfiguration::portal("QUEUEMOD");});

        // Softphone
        $app->map ('/softphone',       function(){\Runa_CCA\Controller\Softphone::portal();})->via('GET', 'POST');
        
        // Twilio
        // TwiML for Operators(Client Application)
        $app->post('/twilio/operator', '\Runa_CCA\Controller\TwiMLApp:createTwiMLApp');
        
        // CallFlow
        // "NewService"
        $app->map ('/twilio/callflow/newservice/main', 
                    function () {
                        \Runa_CCA\Controller\CallFlow\NewService::start("main");
                    })->via('GET', 'POST');
        $app->map ('/twilio/callflow/newservice/wait', 
                    function () {
                        \Runa_CCA\Controller\CallFlow\NewService::start("wait");
                    })->via('GET', 'POST');
        $app->map ('/twilio/callflow/newservice/info', 
                    function () {
                        \Runa_CCA\Controller\CallFlow\NewService::start("info");
                    })->via('GET', 'POST');
        $app->map ('/twilio/callflow/newservice/enqueaction', 
                    function () {
                        \Runa_CCA\Controller\CallFlow\NewService::start("enqueaction");
                    })->via('GET', 'POST');
        $app->map ('/twilio/callflow/newservice/guidance', 
                    function () {
                        \Runa_CCA\Controller\CallFlow\NewService::start("guidance");
                    })->via('GET', 'POST');
        $app->map ('/twilio/callflow/newservice/statuscallback', 
                    function () {
                        \Runa_CCA\Controller\CallFlow\NewService::start("statuscallback");
                    })->via('GET', 'POST');
                   
        // Error
        $app->notFound(function(){\Runa_CCA\Controller\Error::display("NOTFOUND");});
        $app->error(function(){\Runa_CCA\Controller\Error::display("ERROR");});
        
    }
}
