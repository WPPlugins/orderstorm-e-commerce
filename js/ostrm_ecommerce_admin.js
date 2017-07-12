/*
	Copyright (C) 2010-2017 OrderStorm Ecommerce, Inc. (e-mail: wordpress-ecommerce@orderstorm.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
jQuery
(
	function ()
	{
		jQuery("form#APIkeySignupForm").first().submit
		(
			function (event)
			{
				var	orderStormAPIkeySignupFormData = jQuery(event.target).serializeArray(),
					messages = "",
					addMessage = function (messageToAdd)
					{
						if (messages.length > 0)
						{
							messages = messages + "\n";
						}
						messages = messages + messageToAdd;
					},
					gatherMessages = function (messagesArray)
					{
						var	messageSort,
							messageName,
							messageValue;

						if (typeOf(messagesArray) === "array" && messagesArray.length > 0)
						{
							messagesArray.forEach
							(
								function (value, index, array)
								{
									if (typeOf(value) === "array")
									{
										if(value.length === 3)
										{
											messageSort = value[0];
											messageName = value[1];
											messageValue = value[2];
											addMessage(messageSort + ") " + messageValue);
										}
									}
								}
							);

							return true;
						}
						else
						{
							return false;
						}
					},
					showMessages = function ()
					{
						if (messages.length > 0)
						{
							alert(messages);
						}
					};

				event.preventDefault();

				orderStormAPIkeySignupFormData.push({"name":"action", "value":"api_key_signup"});
				orderStormAPIkeySignupFormData.push({"name":"ostrmAdminNonce", "value":ajaxGlobals.ostrmAdminNonce});

				jQuery.ajax
				(
					{
						async:false,
						cache:false,
						contentType:"application/x-www-form-urlencoded",
						data:orderStormAPIkeySignupFormData,
						dataType:"json",
						error:	function (XMLHttpRequest, textStatus, errorThrown)
								{
									alert('Signup failed');
								},
						processData:true,
						success:	function (data, textStatus)
									{
										var	messageName, messageValue;

										if (data.hasOwnProperty('blnSignupSuccessful') && data.hasOwnProperty('blnValidNonce') && data.hasOwnProperty('messages'))
										{
											if (data.blnSignupSuccessful)
											{
												if (data.hasOwnProperty('api_key'))
												{
													if (isWellFormedGUID(data.api_key))
													{
														jQuery("#orderstorm_ecommerce_key_guid").val(data.api_key);
														jQuery("#OrderStormECommerceMainSettings").submit();
													}
													else
													{
														addMessage("Signup failed -> Invalid API key returned");
														gatherMessages(data.messages);
														showMessages();
													}
												}
												else
												{
													addMessage("Signup failed -> No API key was returned");
													gatherMessages(data.messages);
													showMessages();
												}
											}
											else
											{
												if (data.blnValidNonce !== true)
												{
													addMessage("Signup failed -> Invalid nonce");
												}
												if (!gatherMessages(data.messages))
												{
													addMessage("Signup failed -> Unknown cause");
												}
											}
										}
										else
										{
											addMessage("Invalid response");
										}

										showMessages();
									},
						timeout:15000,
						type:"POST",
						url:ajaxGlobals.ajaxURL
					}
				);

				return false;
			}
		);
	}
);
