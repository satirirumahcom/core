<?php
/**
 * 
 * Represents a single record returned from a Solar_Sql_Model.
 * 
 * @category Solar
 * 
 * @package Solar_Sql_Model
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Record.php 920 2007-07-24 04:53:21Z moraes $
 * 
 */

/**
 * 
 * Represents a single record returned from a Solar_Sql_Model.
 * 
 * @category Solar
 * 
 * @package Solar_Sql_Model
 * 
 */
class Solar_Sql_Model_Record extends Solar_Struct
{
    /**
     * 
     * The "parent" model for this record.
     * 
     * @var Solar_Sql_Model
     * 
     */
    protected $_model;
    
    /**
     * 
     * The list of accessor methods for individual column properties.
     * 
     * For example, a method called __getFooBar() will be registered for
     * ['get']['foo_bar'] => '__getFooBar'.
     * 
     * @var array
     * 
     */
    protected $_access_methods = array();
    
    /**
     * 
     * Tracks the of the status of this record.
     * 
     * Status values are:
     * 
     * `clean`
     * : The record is unmodified from the database.
     * 
     * `deleted`
     * : This record has been deleted; load(), etc. will not work.
     * 
     * `dirty`
     * : At least one record property has changed.
     * 
     * `inserted`
     * : The record was inserted successfully.
     * 
     * `invalid`
     * : Validation was attempted, with failure.
     * 
     * `new`
     * : This is a new record and has not been saved to the database.
     * 
     * `updated`
     * : The record was updated successfully.
     * 
     * @var bool
     * 
     */
    protected $_status = 'clean';
    
    /**
     * 
     * Notes which values are not valid.
     * 
     * Keyed on property name => failure message.
     * 
     * @var array
     * 
     */
    protected $_invalid = array();
    
    /**
     * 
     * Tracks which relationship pages are loaded.
     * 
     * Keys on the relationship name.
     * 
     * @param array
     * 
     */
    protected $_related_page = array();
    
    /**
     * 
     * Tells whether or not __get() should lazy-load relateds.
     * 
     * We need this so that when saving, we don't load every related record.
     * 
     * @var bool
     * 
     */
    protected $_lazy_load = true;
    
    /**
     * 
     * Magic getter for record properties; automatically calls __getColName()
     * methods when they exist.
     * 
     * @param string $key The property name.
     * 
     * @return mixed The property value.
     * 
     */
    public function __get($key)
    {
        // disallow if status is 'deleted'
        $this->_checkDeleted();
        
        // do we need to load relationship data?
        $load_related = $this->_lazy_load &&
                        empty($this->_data[$key]) &&
                        ! empty($this->_model->related[$key]);
        
        if ($load_related) {
            // the key was for a relation that has no data yet.
            // load the data.  don't return at this point, look
            // for accessor methods later.
            $this->_data[$key] = $this->_model->fetchRelatedObject(
                $this,
                $key,
                $this->_related_page[$key]
            );
        }
        
        // if an accessor method exists, use it
        if (! empty($this->_access_methods['get'][$key])) {
            // use accessor method
            $method = $this->_access_methods['get'][$key];
            return $this->$method();
        } else {
            // no accessor method; use parent method.
            return parent::__get($key);
        }
    }
    
    /**
     * 
     * Magic setter for record properties; automatically calls __setColName()
     * methods when they exist.
     * 
     * @param string $key The property name.
     * 
     * @param mixed $val The value to set.
     * 
     * @return void
     * 
     */
    public function __set($key, $val)
    {
        // disallow if status is 'deleted'
        $this->_checkDeleted();
        
        // set to dirty only if not 'new'
        if ($this->_status != 'new') {
            $this->_status = 'dirty';
        }
        
        // if an accessor method exists, use it
        if (! empty($this->_access_methods['set'][$key])) {
            // use accessor method
            $method = $this->_access_methods['set'][$key];
            $this->$method($val);
        } else {
            // no accessor method; use parent method
            parent::__set($key, $val);
        }
    }
    
    /**
     * 
     * Sets a key in the data to null.
     * 
     * @param string $key The requested data key.
     * 
     * @return void
     * 
     */
    public function __unset($key)
    {
        // disallow if status is 'deleted'
        $this->_checkDeleted();
        
        // if an accessor method exists, use it
        if (! empty($this->_access_methods['unset'][$key])) {
            // use accessor method
            $method = $this->_access_methods['unset'][$key];
            $this->$method();
        } else {
            // no accessor method; use parent method
            parent::__unset($key);
        }
    }
    
    /**
     * 
     * Checks if a data key is set.
     * 
     * @param string $key The requested data key.
     * 
     * @return void
     * 
     */
    public function __isset($key)
    {
        // disallow if status is 'deleted'
        $this->_checkDeleted();
        
        // if an accessor method exists, use it
        if (! empty($this->_access_methods['isset'][$key])) {
            // use accessor method
            $method = $this->_access_methods['isset'][$key];
            $result = $this->$method();
        } else {
            // no accessor method; use parent method
            $result = parent::__isset($key);
        }
        
        // done
        return $result;
    }
    
    /**
     * 
     * Loads the struct with data from an array or another struct.
     * 
     * Also unserializes columns per the "serialize_cols" model property.
     * 
     * This is a complete override from the parent load() method.
     * 
     * @param array|Solar_Struct $spec The data to load into the object.
     * 
     * @param array $cols Load only these columns.
     * 
     * @return void
     * 
     */
    public function load($spec, $cols = null)
    {
        // force to array
        if ($spec instanceof Solar_Struct) {
            // we can do this because $spec is of the same class
            $data = $spec->_data;
        } elseif (is_array($spec)) {
            $data = $spec;
        } else {
            $data = array();
        }
        
        // remove columns not in the whitelist
        if (! empty($cols)) {
            $cols = (array) $cols;
            foreach ($data as $key => $val) {
                if (! in_array($key, $cols)) {
                    unset($data[$key]);
                }
            }
        }
        
        // pull out belongs_to/has_one related data.
        foreach ($data as $key => $val) {
            // if the key has double-underscores, it's an eager-load record.
            if (strpos($key, '__') !== false) {
                list($rel_name, $rel_key) = explode('__', $key);
                $this->_data[$rel_name][$rel_key] = $val;
                unset($data[$key]);
            }
        }
        
        // unserialize as needed, then add remaining "real" columns.
        // it's not enough to just merge the data.  although slower, we need
        // to loop so that __set() is honored.
        $this->_model->unserializeCols($data);
        
        foreach ($data as $key => $val) {
            $this->__set($key, $val);
        }
        
        // load related data as records and collections
        $list = array_keys($this->_model->related);
        foreach ($list as $name) {
            
            $related = $this->_model->getRelated($name);
            
            // is this a "to-one" association with data already in place?
            $type = $related->type;
            if (($type == 'has_one' || $type == 'belongs_to') && ! empty($this->_data[$name])) {
                    
                // create a record object from the related model
                $model = Solar::factory($related->foreign_class, array(
                    'sql' => $this->_model->sql
                ));
                $this->_data[$name] = $model->newRecord($this->_data[$name]);
                
            } elseif ($type == 'has_many' && ! empty($this->_data[$name])) {
                
                // create a collection object from the related model
                $model = Solar::factory($related->foreign_class, array(
                    'sql' => $this->_model->sql
                ));
                $this->_data[$name] = $model->newCollection($this->_data[$name]);
                
            } else {
                // set a placeholder for lazy-loading in __get()
                $this->_data[$name] = null;
            }
            
            // by default get all related records
            $this->_related_page[$name] = 0;
        }
    }
    
    // -----------------------------------------------------------------
    //
    // Model
    //
    // -----------------------------------------------------------------
    
    /**
     * 
     * Injects the model from which the data originates.
     * 
     * Also loads accessor method lists for column and related properties.
     * 
     * These let users override how the column properties are accessed
     * through the magic __get, __set, etc. methods.
     * 
     * @param Solar_Sql_Model $model The origin model object.
     * 
     * @return void
     * 
     */
    public function setModel(Solar_Sql_Model $model)
    {
        $this->_model = $model;
        
        // get a list of table-column and related-data properties names
        $vars = array_merge(
            array_keys($this->_model->table_cols),
            array_keys($this->_model->related),
            (array) $this->_model->calculate_cols
        );
        
        // look for access methods on each one
        foreach ($vars as $var) {
            $name = str_replace('_', ' ', $var);
            $name = str_replace(' ', '', ucwords($name));
            $list = array(
                "get"   => "__get$name",
                "set"   => "__set$name",
                "isset" => "__isset$name",
                "unset" => "__unset$name",
            );
            foreach ($list as $type => $method) {
                if (method_exists($this, $method)) {
                    $this->_access_methods[$type][$var] = $method;
                }
            }
            
            // put placeholders for each variable; these will be reset by
            // the load() and/or __set() methods.  need to have this here
            // because load() uses __set(), and primary keys will be ignored
            // in that case, leaving the data key unset.  at the same time,
            // we don't want to override values that are already present.
            if (! isset($this->_data[$var])) {
                $this->_data[$var] = null;
            }
        }
    }
    
    /**
     * 
     * Returns the model from which the data originates.
     * 
     * @return Solar_Sql_Model $model The origin model object.
     * 
     */
    public function getModel()
    {
        return $this->_model;
    }
    
    // -----------------------------------------------------------------
    //
    // Record data
    //
    // -----------------------------------------------------------------
    
    /**
     * 
     * Converts the properties of this model Record or Collection to an array,
     * including related models stored in properties.
     * 
     * @return array
     * 
     */
    public function toArray()
    {
        $data = array();
        $keys = array_keys($this->_data);
        
        foreach ($keys as $key) {
            
            // is the key a related record/collection, but not fetched yet?
            $empty_related = ! empty($this->_model->related[$key]) &&
                             empty($this->_data[$key]);
            
            if ($empty_related) {
                
                $related = $this->_model->getRelated($key);
                
                // do not fetch related just for array convertion, this leads
                // to some deep (perhaps infinite?) recurstion
                if ($related->type == 'has_many') {
                    $val = array();
                } else {
                    $val = null;
                }
                
            } else {
                
                // not an empty-related. get the existing value.
                $val = $this->$key;
                
                // get the sub-value if any
                if ($val instanceof Solar_Sql_Model_Record ||
                    $val instanceof Solar_Sql_Model_Collection) {
                    $val = $val->toArray();
                }
            }
            
            // keep the sub-value
            $data[$key] = $val;
        }
        
        // done!
        return $data;
    }
    
    // -----------------------------------------------------------------
    //
    // Database operations
    //
    // -----------------------------------------------------------------
    
    /**
     * 
     * Saves this record to the database, inserting or updating as needed.
     * 
     * Hook methods:
     * 
     * 1. `_preSave()` runs before all save operations.
     * 
     * 2. `_preInsert()` and `_preUpdate()` run before the insert or update.
     * 
     * 3. The record is validated, then inserted or updated.
     * 
     * 4. `_postInsert()` and `_postUpdate()` run after the insert or update.
     * 
     * 5. `_postSave()` runs after all save operations, but before related
     *    records are saved.
     * 
     * 
     * After _postSave(), we save each related Record and Collection object.
     * 
     * @param array $data An associative array of data to merge with existing
     * record data.
     * 
     * @return bool
     * 
     * @todo Automatic connection of related IDs to each other?
     * 
     */
    public function save($data = null)
    {
        $this->_checkDeleted();
        
        // load data at save-time?
        if ($data) {
            $this->load($data);
            $this->setStatus('dirty');
        }
        
        // only save if we're not clean
        if ($this->_status != 'clean') {
            
            // pre-save routine
            $this->_preSave();
            
            // turn off lazy loading
            $this->_lazy_load = false;
            
            // insert or update based on primary key value
            $primary = $this->_model->primary_col;
            if (empty($this->$primary)) {
                $this->_insert();
            } else {
                $this->_update();
            }
            
            // turn on lazy-loading for post-save routines
            $this->_lazy_load = true;
            
            // post-save routine
            $this->_postSave();
        }
        
        // now save each related, but only if instantiated
        foreach ($this->_model->related as $name => $info) {
            
            // use $this->_data[$name] **instead of** $this->$name,
            // to avoid lazy-loading the related record (which in turn
            // causes infinite recursion)
            if (empty($this->_data[$name])) {
                continue;
            }
            
            if ($this->_data[$name] instanceof Solar_Sql_Model_Record ||
                $this->_data[$name] instanceof Solar_Sql_Model_Collection) {
                // is a record or collection, save them
                $this->_data[$name]->save();
            }
        }
    }
    
    protected function _insert()
    {
        try {
            $this->_preInsert();
            $this->_model->insert($this);
            $this->setStatus('inserted');
            $this->_postInsert();
        } catch (Solar_Sql_Adapter_Exception_QueryFailed $e) {
            // failed at at the database for some reason
            $this->setStatus('invalid');
            $this->setInvalid('*', $e->getInfo('pdo_text'));
            throw $e;
        }
    }
    
    protected function _update()
    {
        try {
            $this->_preUpdate();
            $where = null;
            $this->_model->update($this, $where);
            $this->setStatus('updated');
            $this->_postUpdate();
        } catch (Solar_Sql_Adapter_Exception_QueryFailed $e) {
            // failed at at the database for some reason
            $this->setStatus('invalid');
            $this->setInvalid('*', $e->getInfo('pdo_text'));
            throw $e;
        }
    }
    
    protected function _preSave()
    {
    }
    
    protected function _postSave()
    {
    }
    
    protected function _preInsert()
    {
    }
    
    protected function _postInsert()
    {
    }
    
    protected function _preUpdate()
    {
    }
    
    protected function _postUpdate()
    {
    }
    
    protected function _preDelete()
    {
    }
    
    protected function _postDelete()
    {
    }
    
    protected function _preFilter()
    {
    }
    
    protected function _postFilter()
    {
    }
    
    /**
     * 
     * Deletes this record from the database.
     * 
     * @return void
     * 
     */
    public function delete()
    {
        $this->_checkDeleted();
        $this->_preDelete();
        $this->_model->delete($this);
        $this->_postDelete();
    }
    
    /**
     * 
     * Refreshes data for this record from the database.
     * 
     * Note that this does not refresh any related or calculated values.
     * 
     * @return void
     * 
     */
    public function refresh()
    {
        if ($this->_status != 'new') {
            $primary = $this->_model->primary_col;
            $result = $this->_model->fetch($this->$primary);
            $this->load($result);
            $this->_status = 'clean';
        }
    }
    
    // -----------------------------------------------------------------
    //
    // Record relationships
    //
    // -----------------------------------------------------------------
    
    /**
     * 
     * Returns the current page number for a named relation.
     * 
     * @param string $name The relationship name.
     * 
     * @return int
     * 
     */
    public function getRelatedPage($name)
    {
        $this->_checkDeleted();
        
        if (array_key_exists($name, $this->_data)) {
            return $this->_model->related_page[$name];
        }
    }
    
    /**
     * 
     * Sets the page number for a named relation, so that only records from
     * that page are loaded.
     * 
     * Resets the loaded records to NULL so that the new records are lazy-
     * loaded on demand.
     * 
     * @param string $name The relationship name.
     * 
     * @param int $page The page number of records to load.
     * 
     * @return void
     * 
     */
    public function setRelatedPage($name, $page)
    {
        $this->_checkDeleted();
        
        if (array_key_exists($name, $this->_data)) {
            $this->_model->related_page[$name] = (int) $page;
            $this->_data[$name] = null;
        }
    }
    
    // -----------------------------------------------------------------
    //
    // Record status
    //
    // -----------------------------------------------------------------
    
    /**
     * 
     * Forces the status of this record.
     * 
     * @param string $status The new status: 'clean', 'deleted', 'dirty',
     * 'inserted', 'invalid', 'new' or 'updated'.
     * 
     * @return void
     * 
     */
    public function setStatus($status)
    {
        $this->_status = $status;
    }
    
    /**
     * 
     * Returns the status of this record.
     * 
     * @return string $status Current status: 'clean', 'deleted', 'dirty',
     * 'inserted', 'invalid', 'new' or 'updated'.
     * 
     */
    public function getStatus()
    {
        return $this->_status;
    }
    
    /**
     * 
     * Forces one property to be "invalid" and sets a validation failure message
     * for it.
     * 
     * @param string $key The property name.
     * 
     * @param string $message The validation failure message.
     * 
     * @return void
     * 
     */
    public function setInvalid($key, $message)
    {
        $this->_status = 'invalid';
        $this->_invalid[$key][] = $message;
    }
    
    public function setInvalids($list)
    {
        $this->_status = 'invalid';
        foreach ($list as $key => $messages) {
            foreach ((array) $messages as $message) {
                $this->_invalid[$key][] = $message;
            }
        }
    }
    
    /**
     * 
     * Returns the validation failure message for one or more properties.
     * 
     * @param string $key Return the message for this property; if empty,
     * returns messages for all invalid properties.
     * 
     * @return string|array
     * 
     */
    public function getInvalid($key = null)
    {
        if ($key) {
            return $this->_invalid[$key];
        } else {
            return $this->_invalid;
        }
    }
    
    /**
     * 
     * Throws an exception if this record status is 'deleted'.
     * 
     * @return void
     * 
     * @throws Solar_Sql_Model_Exception_Deleted Indicates that the
     * record object has been deleted and cannot be used.
     * 
     */
    protected function _checkDeleted()
    {
        if ($this->_status == 'deleted') {
            throw $this->_exception('ERR_DELETED');
        }
    }
    
    /**
     * 
     * Filter the data.
     * 
     * @return void
     * 
     */
    public function filter()
    {
        $this->_preFilter();
        
        // create a filter object based on the model's filter class
        $filter = Solar::factory($this->_model->filter_class);
        
        // set filters as specified by the model
        foreach ($this->_model->filters as $key => $list) {
            $filter->addChainFilters($key, $list);
        }
        
        // set which elements are required by the table itself
        foreach ($this->_model->table_cols as $key => $info) {
            if ($info['autoinc']) {
                // autoinc are not required
                $flag = false;
            } elseif (in_array($key, $this->_model->sequence_cols)) {
                // auto-sequence are not required
                $flag = false;
            } else {
                // go with the col info
                $flag = $info['require'];
            }
            
            // set the requirement flag
            $filter->setChainRequire($key, $flag);
        }
        
        // tell the filter to use the model for locale strings
        $filter->setChainLocaleObject($this->_model);
        
        // apply filters and retain invalids
        $valid = $filter->applyChain($this);
        $invalid = $filter->getChainInvalid();
        
        // reclaim memory
        $filter->__destruct();
        unset($filter);
        
        // was it valid?
        if (! $valid) {
            $this->_status = 'invalid';
            $this->_invalid = $invalid;
            throw $this->_exception('ERR_INVALID', array($this->_invalid));
        }
        
        // post-logic, and done
        $this->_postFilter();
    }
    
    public function form($cols = null)
    {
        $array_name = $this->_model->model_name;
        if (empty($cols)) {
            $cols = '*';
        }
        
        $form = Solar::factory('Solar_Form');
        $form->load('Solar_Form_Load_Model', $this->_model, $cols, $array_name);
        $form->setValues($this->toArray(), $array_name);
        $form->addInvalids($this->_invalid, $array_name);
        
        // set the form status
        switch (true) {
        case $this->_status == 'invalid':
            $form->setStatus(false);
            break;
        case $this->_status == 'inserted' || $this->_status == 'updated':
            $form->setStatus(true);
            break;
        }
        
        // @todo: add invalidation messages to the form itself, where
        // elements are missing.
        
        return $form;
    }
}