function performAJAXrequest(ajaxRequest, url, context, success, complete, error)
{
	var settings =	{
						async:true,
						cache:false,
						contentType:"application/x-www-form-urlencoded",
						context: context,
						data:	{
									"AJAX_Request":JSON.stringify(ajaxRequest)
								},
						crossDomain:true,
						dataType:"jsonp",
						processData:true,
						timeout:15000,
						type:"GET",
						url:url
					}

	if ((typeof success) !== 'undefined')
	{
		settings.success = success;			// Parameters: data, textStatus, jqXHR
	}
	if ((typeof complete) !== 'undefined')
	{
		settings.complete = complete;		// Parameters: jqXHR, textStatus
	}
	if ((typeof error) !== 'undefined')
	{
		settings.error = error;		// Parameters: jqXHR, textStatus, errorThrown
	}
	jQuery.ajax(settings);
}

function performAJAXrequest2(ajaxRequest, url, context)
{
	var settings =	{
						async:true,
						cache:false,
						contentType:"application/x-www-form-urlencoded",
						context: context,
						data:	{
									"AJAX_Request":JSON.stringify(ajaxRequest)
								},
						crossDomain:true,
						dataType:"jsonp",
						processData:true,
						timeout:15000,
						type:"GET",
						url:url
					}
	return jQuery.ajax(settings);
}

function isWellFormedGUID(guid)
{
	var reGUID = /^\{[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}\}$/;
	
	return reGUID.test(guid);
}
