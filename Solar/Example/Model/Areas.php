<?php
class Solar_Example_Model_Areas extends Solar_Sql_Model {
    
    protected function _setup()
    {
        $dir = str_replace('_', DIRECTORY_SEPARATOR, __CLASS__)
             . DIRECTORY_SEPARATOR
             . 'Setup'
             . DIRECTORY_SEPARATOR;
        
        $this->_table_name = Solar::run($dir . 'table_name.php');
        $this->_table_cols = Solar::run($dir . 'table_cols.php');
        
        $this->_hasMany('nodes', array(
            'foreign_class' => 'nodes',
            'foreign_key'   => 'area_id',
        ));
        
        $this->_belongsTo('user', array(
            'foreign_class' => 'users',
            'foreign_key'   => 'user_id',
        ));
        
        $this->_index = array(
            'created',
            'updated',
            'user_id',
        );
    }
}