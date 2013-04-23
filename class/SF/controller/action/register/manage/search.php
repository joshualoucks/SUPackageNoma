<?php

final class SF_controller_action_register_manage_search
	extends SF_system_controller_json_action_pagination_basic
{
	
	protected function getModelCriteria()
	{
		$input = self::getRequiredInput( array(
			"sort_by" => FILTER_UNSAFE_RAW,
			"is_ascending" => FILTER_VALIDATE_BOOLEAN
		) ) + self::getInput( array(
			"search" => FILTER_UNSAFE_RAW,
		) );
		
		$query = DB_noma_registerQuery::create();
		
		//filter search input
		if( $input["search"] != "" ) {
			$query
				->filterByFname( '%'.$input["search"].'%' )
				->_or()->filterByLname( '%'.$input["search"].'%' )
				->_or()->filterByEmail( '%'.$input["search"].'%' );
		}
		$query->orderBy( $input["sort_by"], $input["is_ascending"] ? Criteria::ASC : Criteria::DESC );
		return $query;		
	}
	
}
?>
