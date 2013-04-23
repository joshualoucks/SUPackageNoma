<?php

final class SF_controller_action_register_manage
	extends SF_pattern_controller_action_model
{
	
	const ELEMENT_MODEL_NAME = "DB_noma_register";
	
	protected function getRequestElementData()
	{
		return self::getRequiredInput( array (
) ) + self::getInput( array (
  'fname' => 516,
  'lname' => 516,
  'email' => 516,
  'address1' => 516,
  'address2' => 516,
  'city' => 516,
  'state' => 516,
  'zip' => 516,
  'country' => 516,
  'product' => 516,
  'product_price' => 259,
  'created_at' => 516,
  'updated_at' => 516,
) );
	}
}

?>