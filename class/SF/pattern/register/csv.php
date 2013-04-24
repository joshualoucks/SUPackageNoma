<?php
abstract class SF_pattern_register_csv extends SF_system_controller /*implements SF_interface_controller_router*/
{

	public static $array_type = BasePeer::TYPE_FIELDNAME;

	private $_temp_file;

	public function main()
	{
		$this->buildExportFile( $this->getRegistrants(), $this->getFileName() );
	}

	public function display()
	{
		parent::display();
		return file_get_contents( $this->_temp_file );
	}

	abstract public function getFileName();
	abstract public function getRegistrants();
	abstract public function format( $noma_registrants );

	private function buildExportFile( $registrants, $filename)
	{
		if( ! $registrants )
			throw new Exception( "No registrants." );

		/* Open the file in the tmp directory, with a generated name based on the current
		 * date and time.
		 */
		$filepath = '/tmp/'.$filename.date('-Ymd-His').'.csv';
		$file = fopen($filepath, 'wb');

		$lines = $this->format( $registrants );
		if( empty( $lines ) )
			$headers_line = array();
		else
			$headers_line = array_keys( current( $lines ) );
		fputcsv($file, $headers_line);
		foreach( $lines as $line ) {
			fputcsv($file, $line);
		}
		fclose($file);
       	header('Content-type: text/csv');
		header('Content-disposition: attachment;filename='.basename( $filepath ) );
		$this->_temp_file = $filepath;
	}
}

?>