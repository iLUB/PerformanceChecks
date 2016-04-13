<#1>
<?php
$fields = array(
	'id' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'is_online' => array(
		'type' => 'integer',
		'length' => 1,
		'notnull' => false
	),
	'option_one' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => false
	)
);

$ilDB->createTable("rep_robj_xpc0_data", $fields);
$ilDB->addPrimaryKey("rep_robj_xpc0_data", array("id"));
?>
<#2>
<?php
?>
