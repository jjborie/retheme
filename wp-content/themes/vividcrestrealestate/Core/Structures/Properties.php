<?php
namespace Vividcrestrealestate\Core\Structures;

class Properties extends \Vividcrestrealestate\Core\Libs\Data 
{
	public $table = "properties";
    
	public $fields = [
		'id' => [
			'type' => "%d",
			'default' => null,
			'editable_fl' => false
		],
        'mls_id' => [
			'type' => "%d",
			'default' => "",
			'editable_fl' => true
		],
        'country' => [
			'type' => "%s",
			'default' => "",
			'editable_fl' => true
		],
        'city' => [
			'type' => "%s",
			'default' => "",
			'editable_fl' => true
		],
        'address' => [
			'type' => "%s",
			'default' => "",
			'editable_fl' => true
		],
        'sublocality' => [
			'type' => "%s",
			'default' => "",
			'editable_fl' => true
		],
        'neighborhood' => [
			'type' => "%s",
			'default' => "",
			'editable_fl' => true
		],
        'latitude' => [
			'type' => "%f",
			'default' => 0,
			'editable_fl' => true
		],
        'longitude' => [
			'type' => "%f",
			'default' => 0,
			'editable_fl' => true
		],
        'bedrooms' => [
			'type' => "%d",
			'default' => 0,
			'editable_fl' => true
		],
        'bathrooms' => [
			'type' => "%d",
			'default' => 0,
			'editable_fl' => true
		],
        'type' => [
			'type' => "%s",
			'default' => "House",
			'editable_fl' => true
		],
        'deal_type' => [
			'type' => "%d",
			'default' => "buy",
			'editable_fl' => true
		],
        'price' => [
			'type' => "%f",
			'default' => 0,
			'editable_fl' => true
		],
        'size' => [
			'type' => "%d",
			'default' => 0,
			'editable_fl' => true
		]
	];
    
    
    
    protected function getOne($primary) 
    {
        // Get the property
        $property = parent::getOne($primary);
        
        // Init additional section
        $property->additional = new \stdClass;
        
        // Get additional info
        $info = (new PropertyInfo)->get(["`property_id`='{$property->id}'"]);
        
        // Attach additional info to the property
        foreach ($info as $param) {
            $property->additional->{$param->key} = $param->value;
        }
        
        
        return $property;
    }
}
