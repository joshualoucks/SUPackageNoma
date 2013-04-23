<?php

final class SF_controller_register_manage
	extends SF_system_controller_twig_template
	implements SF_interface_controller_router
{
	
	const ALLOW_BARE = TRUE;
	
	public function do_()
	{
		$this->doList();
	}
	
	public function doAdd()
	{
		$this->view = "manage/register/edit.html";
		$this->set( "is_add", TRUE );
	}
	
	public function doEdit( $slugs )
	{
		if( ! is_array( $slugs ) || ! array_key_exists( 0, $slugs ) )
			throw new RuntimeException( "missing slug id." );
		
		$register = DB_noma_registerPeer::retrieveByPK( $slugs[0] );
		if( ! $register )
			throw new RuntimeException( "No register for id {$slugs[0]}." );
			
		$this->view = "manage/register/edit.html";
		$this->set( "register", $register->toArray() );
		$this->set( "is_add", FALSE );
	}

	public function doList()
	{
		$this->view = "manage/register/list.html";
	}
	
	public function authenticate()
	{
		return DB_user::getRequiredSessionUser()->isLevelOrAbove( "Admin" );
	}
	public function authenticate() {
		return parent::authenticate() && DB_user::isAdmin();
	}

	
}

?>