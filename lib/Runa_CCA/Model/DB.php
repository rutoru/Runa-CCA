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
     * Illuminate Connection and Bootup
     * 
     */
    static function registerIlluminate(){

        $capsule = new \Illuminate\Database\Capsule\Manager();

        $capsule->addConnection(Self::getIlluminateSettings());

        // Set the event dispatcher used by Eloquent models... (optional)
        $capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher(
                                        new \Illuminate\Container\Container()
                                        )
                                    );

        // Set the cache manager instance used by connections... (optional)
        //$capsule->setCacheManager(...);

        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();
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

    /**
     * PDO Data Source Name
     * 
     * @return String PDO Data Source Name
     */
    static function getPdoDsn(){
        
        return \Base\Conf::DBDRVR
                .":dbname=".\Base\Conf::DBNAME
                .";host=".\Base\Conf::DBHOST
                .";charset=".\Base\Conf::DBCHAR;
        
    }    

    /**
     * Get PDO Object
     * 
     * @return PDO Object
     */
    static function getPdo(){
        
        // Create PDO Object
        return new \PDO(
                    Self::getPdoDsn(),
                    \Base\Conf::DBUSER,
                    \Base\Conf::DBPASS,
                    array(
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_EMULATE_PREPARES => false,
                        \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    )
                );
    }
    
}
