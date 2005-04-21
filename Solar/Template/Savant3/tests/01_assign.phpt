--TEST--
assign variables and references
--FILE--
<?php
require_once '00_prepend.php';
$tpl = new Savant3();
$result = array();

// assign a null by name
$result['null'] = $tpl->assign('nullvar', null);

// assign scalar by name
$result['name'] = $tpl->assign('variable', 'value');

// assign array
$array = array(
	'arr1' => 'val1',
	'arr2' => 'val2'
);
$result['array']  = $tpl->assign($array);

// assign object
$object = new StdClass();
$object->obj1 = 'foo';
$object->obj2 = 'bar';
$object->obj3 = 'baz';
$result['object'] = $tpl->assign($object);

// assign var withough value
$result['novalue'] = $tpl->assign('novalue');

// assign direct
$tpl->direct = 'direct';

// assign reference
$ref = 'ref';
$result['ref'] = $tpl->assignRef('ref', $ref);

// assign direct reference (this will fail)
$ref_direct = 'ref_direct';
$tpl->ref_direct =& $ref_direct;

// get variables and generate output
dump(get_object_vars($tpl));

// get results and generate output
dump($result);

?>
--EXPECT--
array(10) {
  ["nullvar"] => NULL
  ["variable"] => string(5) "value"
  ["arr1"] => string(4) "val1"
  ["arr2"] => string(4) "val2"
  ["obj1"] => string(3) "foo"
  ["obj2"] => string(3) "bar"
  ["obj3"] => string(3) "baz"
  ["direct"] => string(6) "direct"
  ["ref"] => &string(3) "ref"
  ["ref_direct"] => &string(10) "ref_direct"
}
array(6) {
  ["null"] => bool(true)
  ["name"] => bool(true)
  ["array"] => bool(true)
  ["object"] => bool(true)
  ["novalue"] => bool(false)
  ["ref"] => bool(true)
}