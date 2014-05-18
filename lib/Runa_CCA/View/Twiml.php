<?php
/**
 * Twiml Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\View;

class Twiml {

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
     * createTwiMl
     * 
     * @param \Twilio $twilioObj Twilio Object
     */    
    public function createTwiml($twilioObj){
        
        $this->app->render(
            'TwiML/twiml.twig', 
            [
                'twiml'         => $twilioObj,
            ]
        ); 
        
    }
 
}