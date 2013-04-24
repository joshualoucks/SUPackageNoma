<?php
/**
 * SF_controller_subscription_export_member_offline
 * Creates a csv file in /tmp and then serves it to the user.
 */
final class SF_controller_register_export extends SF_pattern_register_csv /*implements SF_interface_controller_router*/
{
	private static $membership_categories;

	public function getFileName()
	{
		return "registrants";
	}

	/**
	 * Called by SF_pattern_subscription_csv
	 */
	public function getRegistrants()
	{
		return DB_noma_registerQuery::create()
			->select(array('Fname', 'Lname', 'Email', 'Address1', 'Address2', 'City', 'State', 'Zip', 'Country', 'ProductPrice', 'CreatedAt'))
			->orderByCreatedAt()
			->find();
	}

	public static function formatToCsvArray( $registrants ){
		$csv_array = array();

		/*
$total_columns = array(
			'fname', 'lname', 'email', 'address1', 'address2', 'city', 'state',
			'zip', 'country', 'product_price', 'created_at'
		);

		$master_keys = array();
		foreach( $total_columns as $key ){
			$master_keys[$key] = NULL;
		}
*/
		$i = 0;
		foreach ($registrants as $registrant)
		{
			
			/*
if( ! $registrants instanceof DB_noma_register )
				throw new Exception( "Registrant not of type DB_register" );
			
			$user = $registrants->getuser();

			$id = $user->getId();

			if (isset($csv_array[$id]))
				throw new Exception( "id $id already set" );*/

			//$user_data = array_merge( $user->toArray( self::$array_type ), $registrant->toArray( self::$array_type ) );
			//$user_data = array_intersect_key($user_data, $master_keys );

			$csv_array[$i] = $registrant;
			$i++;
		}

		return $csv_array;
	}

	/**
	 * Called by SF_pattern_subscription_csv
	 */
	public function format( $noma_registrants )
	{
		
		return self::formatToCsvArray( $noma_registrants );
	}

/*
	private static function getMembershipCategories()
	{
		if( ! is_null( self::$membership_categories ) )
			return self::$membership_categories;

		self::$membership_categories = DB_subscription_membership_category::getList();
		return self::$membership_categories;
	}
*/



	public function  authenticate() {
		$user = DB_user::getSessionUser();
		if( ! $user )
			return false;

		return $user->getLevel() == "Admin" ? TRUE : FALSE;
	}
}
?>