$ = jQuery
$ ->
	#edit form
	$form = $( "form#su-register-form" )
	$form.suAjaxForm()
		
	$( "a#su-delete-register" ).suAction
		finished_closure: ->
			SF.cache.dirty "register_edit"			
			$modal = $( "#su_controller_modal" )
			if $modal.length then $modal.modal 'hide'