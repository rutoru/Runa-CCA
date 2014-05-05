<?php
/**
 * Main Class
 * 
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model\CallFlow\NewService;

class Main {

    /**
     * Object Variables
     */
    private $response;
    
    /**
     * Constructor
     * 
     */
    public function __construct()
    {
        // Twiml Object
        $this->response = new \Services_Twilio_Twiml();
        
    }

    /**
     * main
     * 
     * @param Array $params Post Parameter
     * @return \Twilio $response Twilio Object
     */
    public function main($params){

        // 2nd Access
        if (isset($params['Digits'])){

            // DTMF input
            switch ($params['Digits']) {

                // If '1', then connect to an operator.
                case '1':
                    
                    // DB Connection
                    \Runa_CCA\Model\DB::registerIlluminate();
                    $existingQueue = \Runa_CCA\Model\Queue::find(Conf::QUEUE);
                    
                    $this->response->say('オペレータにおつなぎします。しばらくお待ちください。',
                                            ['language' => \Base\Conf::LANG]);

                    // Enter Queue.
                    // Call WaitURL if the client has to wait.
                    $this->response->enqueue(Conf::QUEUE, 
                                                [
                                                    'waitUrl' => $existingQueue->wait_url,
                                                    'action'  => $existingQueue->action_url,
                                                    'method'  => \Base\Conf::METHOD,
                                                ]
                                            );
                    break;

                // If '2', then play information.
                case '2':

                    // Call Information URL.
                    $this->response->redirect(Conf::INFO_URL);
                    break;

                // Get the client to input a digit.
                default:
                    
                    // DB Connection
                    \Runa_CCA\Model\DB::registerIlluminate();
                    $existingQueue = \Runa_CCA\Model\Queue::find(Conf::QUEUE);

                    $gather = $this->response->gather(
                                                    [
                                                    'numDigits' => 1,
                                                    'timeout'   => '10',
                                                    'method'    => \Base\Conf::METHOD,
                                                    ]
                                                );
                    
                    $gather->say('再度入力をお願いします。'
                                .'お問い合わせは1を、'
                                .'最新の製品情報をお聞きになりたい場合は2を押してください。',
                                    ['language' => \Base\Conf::LANG]);

                    // Connect to an operator if the client meets timeout.
                    $this->response->say('入力が確認できませんでした。'
                                        .'オペレータにおつなぎします。しばらくお待ちください。',
                                            ['language' => \Base\Conf::LANG]);
                    
                    // Enter Queue.
                    // Call WaitURL if the client has to wait.
                    $this->response->enqueue(Conf::QUEUE, 
                                                [
                                                    'waitUrl' => $existingQueue->wait_url,
                                                    'action'  => $existingQueue->action_url,
                                                    'method'  => \Base\Conf::METHOD,
                                                ]
                                            );
                    break ;

                }   

        // First Access
        }else{   

            // DB Connection
            \Runa_CCA\Model\DB::registerIlluminate();
            $existingQueue = \Runa_CCA\Model\Queue::find(Conf::QUEUE);
            
            // Get the client to input a digit.
            $gather = $this->response->gather(
                                            [
                                            'numDigits' => 1,
                                            'timeout'   => '10',
                                            'method'    => \Base\Conf::METHOD,
                                            ]
                                        );
            
            $gather->say('こちらは、サンプルコールセンタです。'
                        .'お問い合わせは1を、最新の製品情報をお聞きになりたい場合は2を押してください。',
                          ['language' => \Base\Conf::LANG]);

             // Connect to an operator if the client meets timeout.
            $this->response->say('入力が確認できませんでした。'
                                .'オペレータにおつなぎします。しばらくお待ちください。',
                                    ['language' => \Base\Conf::LANG]);           
            // Enter Queue.
            // Call WaitURL if the client has to wait.
            $this->response->enqueue(Conf::QUEUE, 
                                        [
                                            'waitUrl' => $existingQueue->wait_url,
                                            'action'  => $existingQueue->action_url,
                                            'method'  => \Base\Conf::METHOD,
                                        ]
                                    );

        }

        // TwiML
        return $this->response;
    }
}