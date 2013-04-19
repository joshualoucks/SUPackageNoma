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

	protected $use_bootstrap = TRUE;
	
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
	public function main()
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
		
		/*
$story_component = new SF_component_story();
		
		if( $slug_array ) {
			$story_component->setAntagonist( $slug_array[0] );
		}
		
		$this->set( "message", $this->message );
		if( $this->showStory ) {
			$this->set( "story", $story_component );
		}
*/
	}
	
}

?>