<?php
/**
 * 
 * Concrete class test.
 * 
 */
class Test_Solar_View_Helper_JsPrototype_Event extends Solar_Test {
    
    /**
     * 
     * Configuration values.
     * 
     * @var array
     * 
     */
    protected $_Test_Solar_View_Helper_JsPrototype_Event = array(
    );
    
    // -----------------------------------------------------------------
    // 
    // Support methods.
    // 
    // -----------------------------------------------------------------
    
    /**
     * 
     * Constructor.
     * 
     * @param array $config User-defined configuration parameters.
     * 
     */
    public function __construct($config)
    {
        parent::__construct($config);
    }
    
    /**
     * 
     * Destructor; runs after all methods are complete.
     * 
     * @param array $config User-defined configuration parameters.
     * 
     */
    public function __destruct()
    {
        parent::__destruct();
    }
    
    /**
     * 
     * Setup; runs before each test method.
     * 
     */
    public function setup()
    {
        parent::setup();
    }
    
    /**
     * 
     * Setup; runs after each test method.
     * 
     */
    public function teardown()
    {
        parent::teardown();
    }
    
    // -----------------------------------------------------------------
    // 
    // Test methods.
    // 
    // -----------------------------------------------------------------
    
    /**
     * 
     * Test -- Constructor.
     * 
     */
    public function test__construct()
    {
        $obj = Solar::factory('Solar_View_Helper_JsPrototype_Event');
        $this->assertInstance($obj, 'Solar_View_Helper_JsPrototype_Event');
    }
    
    /**
     * 
     * Test -- Method interface.
     * 
     */
    public function testEvent()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Fetch method called by Solar_View_Helper_Js.
     * 
     */
    public function testFetch()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns options keys whose values should be dequoted, as the values are expected to be `function() {...}` or names of pre-defined functions elsewhere in the JavaScript environment.
     * 
     */
    public function testGetFunctionKeys()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Method interface.
     * 
     */
    public function testJsLibrary()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Method interface.
     * 
     */
    public function testJsPrototype()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds an event handler function to the element defined by $selector.
     * 
     */
    public function testObserve()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds an event handler function to the JavaScript object defined by $object.
     * 
     */
    public function testObserveObject()
    {
        $this->todo('stub');
    }


}