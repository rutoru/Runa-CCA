<?php
/**
 * DB Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model;

class DB {

    /**
     * Object Variables
     */
    private $capsule;
        
    /**
     * Constructor
     * 
     */
    public function __construct(){       

        $this->capsule = new \Illuminate\Database\Capsule\Manager();

        $this->capsule->addConnection(Self::getIlluminateSettings());

        // Set the event dispatcher used by Eloquent models... (optional)
        $this->capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher(
                                        new \Illuminate\Container\Container()
                                        )
                                    );

        // Set the cache manager instance used by connections... (optional)
        //$this->capsule->setCacheManager(...);

        // Make this Capsule instance available globally via static methods... (optional)
        $this->capsule->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $this->capsule->bootEloquent();
        
    }
    
    /**
     * Illuminate Connection and Bootup
     * 
     * @return \Illuminate\Database\Connection Illuminate Connection Object
     */
    public function getIlluminateConnection(){
        
        return $this->capsule->getConnection();

    }

    /**
     * getIlluminateSettings
     * 
     * @return String[] Connection information
     */
    static function getIlluminateSettings(){
        
        return [
            'driver'    => \Base\Conf::DBDRVR,
            'host'      => \Base\Conf::DBHOST,
            'database'  => \Base\Conf::DBNAME,
            'username'  => \Base\Conf::DBUSER,
            'password'  => \Base\Conf::DBPASS,
            'charset'   => \Base\Conf::DBCHAR,
            'collation' => \Base\Conf::DBCOLL,
            'prefix'    => \Base\Conf::DBPRIF,
        ];

    }
    
}
