<?php
/**
 * 
 * Factory class for authentication adapters.
 * 
 * @category Solar
 * 
 * @package Solar_Auth
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id$
 * 
 */
class Solar_Auth extends Solar_Base
{
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
    protected $_Solar_Auth = array(
        'adapter' => 'Solar_Auth_Adapter_None',
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
        
        // return the factoried adapter object
        return Solar::factory($class, $config);
    }
}
