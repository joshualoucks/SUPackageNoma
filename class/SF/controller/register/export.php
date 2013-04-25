<?php
/**
 * SF_controller_register_export
 * Creates a csv file in /tmp and then serves it to the user.
 */
final class SF_controller_register_export extends SF_system_controller_csv
{
	protected $csv_file_name = "registrants.csv";
	
	protected function getModelCriteria() {
		return DB_noma_registerQuery::create()
			->select(array('Fname', 'Lname', 'Email', 'Address1', 'Address2', 'City', 'State', 'Zip', 'Country', 'ProductPrice', 'CreatedAt'))
			->orderByCreatedAt();
	}

	public function  authenticate() {
		return DB_user::isAdmin();
	}
}
?>