<?php
/**
 * Wait Class
 * 
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model\CallFlow\NewService;

class Wait {

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
     * wait
     * 
     * @param Array $params Post Parameter
     * @return \Twilio $response Twilio Object
     */
    public function wait($params){

        // Get QueuePosition
        // We don't need to sanitize, because Services_Twilio_Twiml will sanitize it.
        // Just in case.
        $waitnumber = \Base\Conf::h($params['QueuePosition']);

        // Wait.
        $this->response->pause('3');

        // Qeueing Message.
        $this->response->say("お待たせしております。現在、".$waitnumber."番目にお待ちです。",
                            ['language' => \Base\Conf::LANG]);

        // Play MOH.
        $this->response->play(Conf::MOH_LONG);

        // TwiML
        return $this->response;
    }

}