<?php

/**
 * SF_controller_register class.
 * 
 *
 * @extends SF_system_controller_twig_template
 * @final
 */
final class SF_controller_register extends SF_system_controller_twig_template implements SF_interface_controller_router
{

	protected $message = "This is the Register controller for NOMA";
	protected $showStory = TRUE;
	
	public $view = "register.html";
	public $template_name = "register";

	protected $use_bootstrap = TRUE;
	
	private $products = array();
	
	public function init()
	{
		$slug_array = $this->getSuffixSlugArray();
		
		//language_code must be set in init()
		if( in_array( "spanish", $slug_array ) ) {
			$this->language_code = "es_ES";
		} else if( in_array( "romanian", $slug_array ) ) {
			$this->language_code = "ro_RO";
		}
		
		//template also needs to be set in init()
		if( in_array( "print", $slug_array ) ) {
			$this->template_name = "print";
		}
	}
	
	
	/**
	 * Set necessary twig variables here via $this->set()
	 */
	public function do_()
	{
		$slug_array = $this->getSuffixSlugArray();
		
		if( in_array( "error", $slug_array ) )
			trigger_error( "this is an error" );
		
		if( in_array( "fatalerror", $slug_array ) )
			$something->method();
		
		if( in_array( "redirect", $slug_array ) )
			$this->redirect( "/register/".implode( "/", str_replace( "redirect", "moved", $slug_array ) ) );
		
		if( in_array( "noaccess", $slug_array ) )
			throw new SF_exception_route_noaccess;
		
		if( in_array( "404", $slug_array ) )
			throw new SF_exception_route_notfound;
		
		if( in_array( "hide", $slug_array ) ) {
			$this->showStory = FALSE;
		}
		
		$this->set( "products", $this->_available_products );
		
	}
	
	private $_available_products = array(
		1 => array( "name" => "NOMA Member: On-site Registration", "price" => "350.00" ),
		2 => array( "name" => "non NOMA Member: On-site Registration", "price" => "450.00" ),
		3 => array( "name" => "Student: On-site Registration", "price" => "160.00" ),
		4 => array( "name" => "Guest: On-site Registration", "price" => "200.00" ),
		5 => array( "name" => "NOMA Member: Single Day Pass", "price" => "150.00" ),
		6 => array( "name" => "non NOMA Member: Single Day Pass", "price" => "250.00" ),
		7 => array( "name" => "Student: Single Day Pass", "price" => "100.00" ),
		8 => array( "name" => "Guest: Single Day Pass", "price" => "100.00" ),
	);
	
	/**
	*
	*/
	public function doSubmit() {
		if( ! array_key_exists( "results", $_POST ) ) 
			throw new RuntimeException( "Missing key: results." );
		$failed_validation = FALSE;
		
		$results = $_POST["results"];
		//var_dump($results); exit;
		//-----------------------------------------

			//$this->checkout();
			
		//-----------------------------------------
		
		foreach( $results as $key => $result ) {	
			//$price = $result["prouct"]
			//var_dump($result);exit;
			if ($result["product"]) {
				$result["product_price"] = $this->_available_products[$result["product"]]["price"];
				$result["product"] = $this->_available_products[$result["product"]]["name"];
			} else {
				
			}
			$input = self::getInput( array(
				"fname" => FILTER_UNSAFE_RAW,
				"lname" => FILTER_UNSAFE_RAW,
				"email" => FILTER_UNSAFE_RAW,
				"address1" => FILTER_UNSAFE_RAW,
				"address2" => FILTER_UNSAFE_RAW,
				"city" => FILTER_UNSAFE_RAW,
				"state" => FILTER_UNSAFE_RAW,
				"zip" => FILTER_UNSAFE_RAW,
				"country" => FILTER_UNSAFE_RAW,
				"product" => FILTER_UNSAFE_RAW,
				"product_price" => FILTER_VALIDATE_FLOAT,
			), $result );
			$register = new DB_noma_register();
			$register->fromArray( $input, BasePeer::TYPE_FIELDNAME );
			$register->save();
			
			$this->products[] = $this->buildProductFromRegistrations($result);
			
			//-------------------------------
			
			//-------------------------------
			
		}
		$this->checkout();
		//var_dump($this->products);exit;
		//throw new SF_exception_route_redirect( "/register/manage" );
		$this->view = "register/cart.html";
		
		//throw new SF_exception_route_redirect( "/transaction.php" );
		
	}
	
	public function checkout() {
		
		$payment_total = 0;
		$my_session = SF_static_global::getSession();
		$pdo = SF_static_global::getPDO();
		/* End Vars */

		//$_SESSION['module'] = $module;
		//$my_session["user_id"] = $this->user->getId();
		/* needs to be unencrypted for new users */
		//$my_session["password"] = $this->user->getPassword();

		if( empty($this->products ) )
			throw new Exception( "There are no products!" );

		$product = current($this->products);
		$products_table = $product->table_name;

		/* Clearing the cart products */
		$_SESSION['quantity'] = array();
		$_SESSION['productid'] = array();
		unset( $_SESSION['nr_products'] );
		$pdo->query("DROP TABLE IF EXISTS $products_table");
		$pdo->query("CREATE TABLE $products_table (
              product_id smallint(5) unsigned NOT NULL auto_increment,
              site_id tinyint(3) unsigned default NULL,
              product_title varchar(255) default NULL,
              product_description1 mediumtext,
              product_description2 mediumtext,
              product_number varchar(30) default NULL,
              product_price float(10,2) default NULL,
              product_image varchar(255) default NULL,
              product_thumbnail varchar(255) default NULL,
              product_start_date date default NULL,
              product_end_date date default NULL,
              product_popup_width varchar(6) default NULL,
              product_popup_height varchar(6) default NULL,
              product_taxable tinyint(1) unsigned default '0',
              product_shipping tinyint(1) unsigned default '0',
              product_status enum('active','inactive') default 'active',
              product_category varchar(5) default NULL,
              product_sale_price float(10,2) default NULL,
              product_sale_start_date date default NULL,
              product_sale_end_date date default NULL,
              product_discount_volume smallint(5) default NULL,
              product_discount float(10,2) default NULL,
              KEY product_id (product_id)
              ) ENGINE=MyISAM");

		$one_index = 1;
		$product_title_array = array();
		foreach ($this->products as $product)
		{
			$stm = $pdo->prepare( "INSERT INTO $products_table (
					site_id,
					product_title,
					product_description1,
					product_description2,
					product_number,
					product_price,
					product_image,
					product_thumbnail,
					product_start_date,
					product_end_date,
					product_taxable,
					product_shipping,
					product_status,
					product_category,
					product_sale_price,
					product_sale_start_date,
					product_sale_end_date,
					product_discount_volume,
					product_discount
					)
					VALUES
					(
					:site_id,
					:product_title,
					:product_description1,
					:product_description2,
					:product_number,
					:product_price,
					:product_image,
					:product_thumbnail,
					:product_start_date,
					:product_end_date,
					:product_taxable,
					:product_shipping,
					:product_status,
					:product_category,
					:product_sale_price,
					:product_sale_start_date,
					:product_sale_end_date,
					:product_discount_volume,
					:product_discount
					)" );
			if( ! $stm )
				throw new Exception( $pdo->errorInfo() );

			$stm->execute( array(
				':site_id' => $product->site_id,
				':product_title' => $product->title,
				':product_description1' => $product->description1,
				':product_description2' => $product->description2,
				':product_number' => $product->number,
				':product_price' => $product->price,
				':product_image' => $product->image,
				':product_thumbnail' => $product->thumbnail,
				':product_start_date' => $product->start_date,
				':product_end_date' => $product->end_date,
				':product_taxable' => $product->is_taxable,
				':product_shipping' => $product->shipping,
				':product_status' => $product->status,
				':product_category' => $product->category,
				':product_sale_price' => $product->sale_price,
				':product_sale_start_date' => $product->sale_start_date,
				':product_sale_end_date' => $product->sale_end_date,
				':product_discount_volume' => $product->discount_volume,
				':product_discount' => $product->discount
			) );
			$primary_key = $pdo->lastInsertId();

			//The shopping cart needs information for each product, including its
			//id in the table ('productid'), its quantity ('quantity'), and the
			//total number of products ('nr_products')
			if (isset($_SESSION['productid']))
			{
				$index = array_search((int) $primary_key, $_SESSION['productid']);
			}
			else
			{
				$index = false;
			}

			if ($index !== false)
			{
				++$_SESSION['quantity'][$index];
			}
			else
			{
				$_SESSION['productid'][$one_index] = (int) $primary_key;
				$_SESSION['quantity'][$one_index] = 1;
				++$one_index;
			}

			if (!isset($_SESSION['nr_products']))
			{
				$_SESSION['nr_products'] = 1;
			}
			else
			{
				++$_SESSION['nr_products'];
			}

			$payment_total += $product->price;
			$product_title_array[] = $product->title;
		}

		/* Set Twig vars */
		$this->set( "payment_total", sprintf( '%01.2f', $payment_total ) );
		$this->set( "session_id", session_id() );
		$this->set( "products_table", $products_table );
		$this->set( "sub_voice_info", implode( " | ", $product_title_array ) );
		$this->set( "transaction_url", SF_system_controller::getURLForRedirectWithSession( SF_static_global::getSSLBase() . "/transaction.php" ) );
		/* Overriding set done in ::main() because $this->user has changed now for cart.html */
		//$this->set( "user", $this->user->toArray( self::$array_type ) );	
		}
		
	private static function _getUniqueCodeForTable()
	{
		static $unique;
		if( is_null($unique) ) {
			$unique = uniqid();
		}
		return $unique;
	}
		
	public function buildProductFromRegistrations( $result )
		{
			//$RegDescr = implode(" | ", $result);
			//$productTitle = $result["product"];
			//$price = $result["product_price"];
			
			$product = new stdClass();
			$product->table_name = 'temp_transactions_products_'.self::_getUniqueCodeForTable();
			$product->site_id = SF_static_global::getSiteId();
			$product->title = $result["product"];
			$product->description1 = implode(" | ", $result);
			$product->description2 = '';
			$product->number = '1';
			$product->price = sprintf('%01.2f', $result["product_price"]);
			$product->image = '';
			$product->thumbnail = '';
			$product->start_date = date('Y-m-d');
			$product->end_date = '';
			$product->is_taxable = 0;
			$product->shipping = 0;
			$product->status = 'active';
			$product->category = 0;
			$product->sale_price = '';
			$product->sale_start_date = '';
			$product->sale_end_date = '';
			$product->discount_volume = 0;
			$product->discount = 0;
			return $product;
		}
		

	
}

?>