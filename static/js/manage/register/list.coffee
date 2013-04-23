$ = jQuery
$ ->
	sortby = new SF.macro.sortby $("#su-register-sortby"), ({key: key, is_ascending: is_ascending}) ->
		SF.history.set 
			page: 1
			sort_by: key
			is_ascending: +is_ascending
				
	pagination = new SF.macro.pagination.basic $("#su-register-pagination"), ({page: page}) ->
		SF.history.set
			page: page
				
	search = new SF.macro.search $("#su-register-search"), ({value: value}) ->
		SF.history.set
			search: value
			
	refreshRegisters = ->
		current = SF.history.get()
		request = 
			search: current.search
			page: current.page
			sort_by: current.sort_by
			is_ascending: current.is_ascending
			
		SF.action.post "action/register/manage/search", request, (response) ->
			pagination.setState response
			$( "#register-results-num" ).text response.count
			SF.proto.build $("#register-list tbody tr.su_prototype"), response.results, (prototype, data) ->
				prototype.attr "data-id", data.Id
				prototype.find( ".su-fname" ).text data.Fname
				prototype.find( ".su-lname" ).text data.Lname
				prototype.find( ".su-email" ).text data.Email
				prototype.find( ".su-address1" ).text data.Address1
				prototype.find( ".su-address2" ).text data.Address2
				prototype.find( ".su-city" ).text data.City
				prototype.find( ".su-state" ).text data.State
				prototype.find( ".su-zip" ).text data.Zip
				prototype.find( ".su-country" ).text data.Country
				prototype.find( ".su-product" ).text data.Product
				prototype.find( ".su-product-price" ).text data.ProductPrice

				prototype.find( "a.su-edit" ).attr "href", "register/manage/edit/"+data.Id
				$a = prototype.find( "a#su-delete")
				$a.attr "href", "#"
				$a.attr "data-id", data.Id 
				$a.suAction
					finished_closure: ->
						refreshRegisters()
							
 				
				
	SF.history.init
		page: 1
		sort_by: "Fname"
		is_ascending: 1
		search: ""
		
	SF.history.register [ "page", "sort_by", "is_ascending", "search" ], refreshRegisters
	
	SF.history.register [ "search" ], (updated_params) ->
		search.setState
			value: updated_params.search

	SF.history.register [ "sort_by", "is_ascending" ], ->
		{sort_by: sort_by, is_ascending: is_ascending} = SF.history.get()
		sortby.setState
			key: sort_by
			is_ascending: Boolean Number is_ascending
			
			
	SF.cache.registerHandle "register_edit", refreshRegisters