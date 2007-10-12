<?php
/**
 * 
 * Example for testing a model of content "taggings" (nodes-to-tags mappings).
 * 
 * @category Solar
 * 
 * @package Solar_Example
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Exception.php 2804 2007-10-06 14:01:27Z pmjones $
 * 
 */

/**
 * 
 * Example for testing a model of content "taggings" (nodes-to-tags mappings).
 * 
 * @category Solar
 * 
 * @package Solar_Example
 * 
 */
class Solar_Example_Model_Taggings extends Solar_Sql_Model {
    
    /**
     * 
     * Model setup.
     * 
     * @return void
     * 
     */
    protected function _setup()
    {
        $dir = str_replace('_', DIRECTORY_SEPARATOR, __CLASS__)
             . DIRECTORY_SEPARATOR
             . 'Setup'
             . DIRECTORY_SEPARATOR;
        
        $this->_table_name = Solar::run($dir . 'table_name.php');
        $this->_table_cols = Solar::run($dir . 'table_cols.php');
        
        $this->_belongsTo('node', array(
            'foreign_class' => 'nodes',
        ));
        
        $this->_belongsTo('tag', array(
            'foreign_class' => 'tags',
        ));
        
        $this->_index = array(
            'node_id',
            'tag_id',
        );
    }
}