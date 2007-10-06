<?php
/**
 * 
 * Validates that a value is not blank whitespace.
 * 
 * @category Solar
 * 
 * @package Solar_Filter
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id$
 * 
 */

/**
 * 
 * Validates that a value is not blank whitespace.
 * 
 * @category Solar
 * 
 * @package Solar_Filter
 * 
 */
class Solar_Filter_ValidateNotBlank extends Solar_Filter_Abstract {
    
    /**
     * 
     * Validates that the value is not blank whitespace.
     * 
     * Boolean, integer, and float types are never "blank". All other types
     * are converted to string and trimmed; if '', then the value is blank.
     * 
     * @param mixed $value The value to validate.
     * 
     * @return bool True if valid, false if not.
     * 
     */
    public function validateNotBlank($value)
    {
        if (is_bool($value) || is_int($value) || is_float($value)) {
            return true;
        }
        
        return (trim((string)$value) != '');
    }
}