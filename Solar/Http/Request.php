<?php
/**
 *
 * Factory to return an HTTP request adapter instance.
 * 
 * @category Solar
 *
 * @package Solar_Http
 *
 * @author Paul M. Jones <pmjones@solarphp.com>
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 * @version $Id: Request.php 2126 2007-01-25 00:42:51Z pmjones $
 *
 */

/**
 * 
 * Factory to return an HTTP request adapter instance.
 * 
 * @category Solar
 *
 * @package Solar_Http
 *
 */
class Solar_Http_Request extends Solar_Base {
    
    /**
     * 
     * User-supplied configuration values.
     * 
     * Keys are ...
     * 
     * `adapter`
     * : (string) The adapter class, for example 'Solar_Auth_Adapter_File'.
     * 
     * @var array
     * 
     */
    protected $_Solar_Http_Request = array(
        'adapter' => 'Solar_Http_Request_Adapter_Stream',
    );
    
    /**
     * 
     * Factory method for returning adapters.
     * 
     * @return Solar_Auth_Adapter
     * 
     */
    public function solarFactory()
    {
        // bring in the config and get the adapter class.
        $config = $this->_config;
        $class = $config['adapter'];
        unset($config['adapter']);
        
        // factory the new class with its config
        return Solar::factory($class, $config);
    }
}