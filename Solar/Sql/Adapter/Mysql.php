<?php
/**
 * 
 * Class for MySQL behaviors.
 * 
 * @category Solar
 * 
 * @package Solar_Sql
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id$
 * 
 */

/**
 * Abstract SQL adapter.
 */
Solar::loadClass('Solar_Sql_Adapter');

/**
 * 
 * Class for MySQL behaviors.
 * 
 * @category Solar
 * 
 * @package Solar_Sql
 * 
 */
class Solar_Sql_Adapter_Mysql extends Solar_Sql_Adapter {
    
    /**
     * 
     * Map of Solar generic column types to RDBMS native declarations.
     * 
     * @var array
     * 
     */
    protected $_native = array(
        'bool'      => 'TINYINT(1)',
        'char'      => 'CHAR(:size) BINARY',
        'varchar'   => 'VARCHAR(:size) BINARY',
        'smallint'  => 'SMALLINT',
        'int'       => 'INTEGER',
        'bigint'    => 'BIGINT',
        'numeric'   => 'DECIMAL(:size,:scope)',
        'float'     => 'DOUBLE',
        'clob'      => 'LONGTEXT',
        'date'      => 'CHAR(10)',
        'time'      => 'CHAR(8)',
        'timestamp' => 'CHAR(19)'
    );
    
    protected $_describe = array(
        
        // numeric
        'smallint'          => 'smallint',
        'int'               => 'int',
        'integer'           => 'int',
        'bigint'            => 'bigint',
        'dec'               => 'numeric',
        'decimal'           => 'numeric',
        'double'            => 'float',
        
        // date & time
        'date'              => 'date',
        'datetime'          => 'timestamp',
        'timestamp'         => 'integer',
        'time'              => 'time',
        
        // string
        'national char'     => 'char',
        'nchar'             => 'char',
        'char'              => 'char',
        'binary'            => 'char',
        'national varchar'  => 'varchar',
        'nvarchar'          => 'varchar',
        'varchar'           => 'varchar',
        'varbinary'         => 'varchar',
        
        // clob
        'longtext'          => 'clob',
        'longblob'          => 'clob',
    );
        
    
    /**
     * 
     * The PDO adapter type.
     * 
     * @var string
     * 
     */
    protected $_pdo_type = 'mysql';
    
    /**
     * 
     * Builds a SELECT statement from its component parts.
     * 
     * Adds LIMIT clause.
     * 
     * @param array $parts The component parts of the statement.
     * 
     * @return void
     * 
     */
    protected function _buildSelect($parts)
    {
        // build the baseline statement
        $stmt = parent::_buildSelect($parts);
        
        // determine count
        $count = ! empty($parts['limit']['count'])
            ? (int) $parts['limit']['count']
            : 0;
        
        // determine offset
        $offset = ! empty($parts['limit']['offset'])
            ? (int) $parts['limit']['offset']
            : 0;
      
        /*
            // for mysql 3.23.x?
            if ($count > 0) {
                $offset = ($offset > 0) ? $offset : 0;    
                $stmt .= "LIMIT $offset, $count";
            }
        */
        
        // add the count and offset
        if ($count > 0) {
            $stmt .= " LIMIT $count";
            if ($offset > 0) {
                $stmt .= " OFFSET $offset";
            }
        }
        
        // done!
        return $stmt;
    }
    
    /**
     * 
     * Returns a list of database tables.
     * 
     * @return array The list of tables in the database.
     * 
     */
    public function fetchTableList()
    {
        $result = $this->query('SHOW TABLES');
        $list = $result->fetchAll(PDO::FETCH_COLUMN, 0);
        return $list;
    }
    
    /**
     * 
     * Describes the columns in a table.
     * 
     *     mysql> DESCRIBE table_name;
     *     +--------------+--------------+------+-----+---------+-------+
     *     | Field        | Type         | Null | Key | Default | Extra |
     *     +--------------+--------------+------+-----+---------+-------+
     *     | id           | int(11)      |      | PRI | 0       |       |
     *     | created      | varchar(19)  | YES  | MUL | NULL    |       |
     *     | updated      | varchar(19)  | YES  | MUL | NULL    |       |
     *     | name         | varchar(127) |      | UNI |         |       |
     *     | owner_handle | varchar(32)  | YES  | MUL | NULL    |       |
     *     | subj         | varchar(255) | YES  |     | NULL    |       |
     *     | prefs        | longtext     | YES  |     | NULL    |       |
     *     +--------------+--------------+------+-----+---------+-------+
     * 
     * @param string $table The table to describe.
     * 
     * @return array
     * 
     */
    public function fetchTableCols($table)
    {
        // strip non-word characters to try and prevent SQL injections
        $table = preg_replace('/[^\w]/', '', $table);
        
        // get the native PDOStatement result
        $result = $this->query("DESCRIBE $table");
        
        // where the description will be stored
        $descr = array();
        
        // loop through the result rows; each describes a column.
        $cols = $result->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $val) {
            
            $name = $val['field'];
            
            list($type, $size, $scope) = $this->_getTypeSizeScope($val['type']);
            
            // override $type to find tinyint(1) as boolean
            if ($val['type'] == 'tinyint(1)') {
                $type = 'bool';
            }
            
            // save the column description
            $descr[$name] = array(
                'name'    => $name,
                'type'    => $type,
                'size'    => $size,
                'scope'   => $scope,
                'default' => $this->_getDefault($val['default']),
                'require' => (bool) ($val['null'] != 'YES'),
                'primary' => (bool) ($val['key'] == 'PRI'),
                'autoinc' => (bool) (strpos($val['extra'], 'auto_increment') !== false),
            );
        }
            
        // done!
        return $descr;
    }
    
    /**
     * 
     * Given a native column SQL default value, finds a PHP literal value.
     * 
     * SQL NULLs are converted to PHP nulls.  Non-literal values (such as
     * keywords and functions) are also returned as null.
     * 
     * @param string $default The column default SQL value.
     * 
     * @return scalar A literal PHP value.
     * 
     */
    protected function _getDefault($default)
    {
        $upper = strtoupper($default);
        if ($upper == 'NULL' || $upper == 'CURRENT_TIMESTAMP') {
            // the only non-literal allowed by MySQL is "CURRENT_TIMESTAMP"
            return null;
        } else {
            // return the literal default
            return $default;
        }
    }
    
    /**
     * 
     * Builds a CREATE TABLE command string.
     * 
     * @param string $name The table name to create.
     * 
     * @param string $cols The column definitions.
     * 
     * @return string A CREATE TABLE command string.
     * 
     */
    protected function _buildCreateTable($name, $cols)
    {
        $stmt = parent::_buildCreateTable($name, $cols);
        $stmt .= " TYPE=InnoDB"; // for transactions
        return $stmt;
    }
    
    /**
     * 
     * Drops an index.
     * 
     * @param string $table The table of the index.
     * 
     * @param string $name The full index name.
     * 
     * @return void
     * 
     */
    protected function _dropIndex($table, $name)
    {
        $this->query("DROP INDEX $name ON $table");
    }
    
    /**
     * 
     * Creates a sequence, optionally starting at a certain number.
     * 
     * @param string $name The sequence name to create.
     * 
     * @param int $start The first sequence number to return.
     * 
     * @return void
     * 
     */
    protected function _createSequence($name, $start = 1)
    {
        $start -= 1;
        $this->query("CREATE TABLE $name (id INT NOT NULL) TYPE=InnoDB");
        $this->query("INSERT INTO $name (id) VALUES ($start)");
    }
    
    /**
     * 
     * Drops a sequence.
     * 
     * @param string $name The sequence name to drop.
     * 
     * @return void
     * 
     */
    protected function _dropSequence($name)
    {
        $this->query("DROP TABLE $name");
    }
    
    /**
     * 
     * Gets a sequence number; creates the sequence if it does not exist.
     * 
     * @param string $name The sequence name.
     * 
     * @return int The next sequence number.
     * 
     */
    protected function _nextSequence($name)
    {
        $cmd = "UPDATE $name SET id = LAST_INSERT_ID(id+1)";
        
        // first, try to increment the sequence number, assuming
        // the table exists.
        try {
            $this->query($cmd);
        } catch (Exception $e) {
            // error when updating the sequence.
            // assume we need to create it, then
            // try to increment again.
            $this->_createSequence($name);
            $this->query($cmd);
        }
        
        // get the sequence number
        return $this->_pdo->lastInsertID();
    }
}
?>