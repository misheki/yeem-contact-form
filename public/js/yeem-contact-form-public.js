(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    $(function () {

				$(generateForm());

				$("#mycontactform").submit(function(event) {
    					event.preventDefault();
						}).validate({
							submitHandler: function(form) {
								var ff = [];
								var email_to;
								var bSkipNewPush = false;

								// Get all the forms elements and their values in one step
								var values = $('#mycontactform').serializeArray();
								var source_page = jQuery('input:hidden[name=source_page]').val();
								var captcha_sum = jQuery('input[name=sum]').val();

								if(captcha_sum === '')
								{
										alert("Please prove that you are not a robot.");
										return;
								}

								$.each( values, function( i, field ) { //E.g. text-12345

										var fType = field.name.substring(0, field.name.indexOf('-'));
										var fLabel = field.name.substring(field.name.indexOf('-') + 1, field.name.length).replace(/\[|\]|_/g, ' ');
										var fValue = field.value;
										bSkipNewPush = false;

										//Populate the recipient email
									  if(fType === 'email'){ email_to = fValue; }

									 	//For select dropdown, if same value as placeholder, set to empty
									 	if(fType === 'select'){
											 if(fLabel.replace(/\s/g,'') === fValue.replace(/\s/g,'')){ field.value = "";}
									 	}

									 	if(fType === 'checkbox'){
										 	if(ff.length > 0){
												if(ff[ff.length - 1].name === fLabel){	//Means a checkbox with more than 1 check
														bSkipNewPush = true;
														var mergeCheckValues = ff[ff.length - 1].value + ", " + fValue;
														ff[ff.length - 1].value =  mergeCheckValues;
												}
											}
									 	}

										//Once reach field for captcha, exit
										if(fLabel === 'sum' || fLabel === 'submitted')
											return;

									 	if(!bSkipNewPush){
										 	ff.push({
											 	name: fLabel,
											 	value: fValue
											});
									 	}
							});

							$.ajax({
									method: "POST",
									url: yeemScriptObj.ajaxUrl,
									data: {action: 'yeem_sendmail', fields:ff, email_to:email_to, source_page:source_page, captcha_sum: captcha_sum},
									//data: {action: 'yeem_sendmail', fields:ff, email_to:email_to, captcha_sum: captcha_sum},
									success: function (msg) {
											$('#ConfirmationMsg').removeClass('hide');
											$('#mycontactform').addClass('hide');
											msg = msg.substring(0, msg.length-1);
											$("#ConfirmationMsg").html(msg);
									},
									error: function() { console.log("error sending mail"); }
							});
						}
					});
    });

    function generateForm() {

        $("#formarea").empty();
				$("#captchaAns").val('');

        var formfields;
        var data = {    action: 'yeem_get_formelements' };

        $.ajax({
            method: "POST",
            url: yeemScriptObj.ajaxUrl,
            data: data,
            //dataType: 'json',
            success: function(msg){

                formfields = JSON.parse(msg);

                //go through each saved field object and render the form HTML
                $.each( formfields, function( k, v ) {

                    var fieldType = v['type'];
                    var fieldLabel = v['label'];
										var strLabel = fieldLabel.replace(/\s/g, '_');

                    //Add the field
                    $('#formarea').append(addFieldHTML(fieldType, strLabel));
                    var currentField = $('#formarea p').last();

                    //Add the label
                    currentField.find('input').attr("placeholder", fieldLabel);
                    currentField.find('textarea').attr("placeholder", fieldLabel);
                    currentField.find('select').append('<option value="" selected>' + fieldLabel + '</option>');
                    currentField.find('label').text(fieldLabel);

                    //Check if choices are present
                    if (v['options']) {

												var optionCount = 0;
												var uniqueID = strLabel;

                        $.each( v['options'], function( k, v ) {
														optionCount++;


                            if (fieldType == 'select') {
                                //var selected = v['sel'] ? ' selected' : '';
                                //var choiceHTML = '<option' + selected + '>' + v['label'] + '</option>';
                                var choiceHTML = '<option value="' + optionCount + '">' + v['label'] + '</option>';
                                currentField.find(".dropdownselect").append(choiceHTML);
                            }

                            else if (fieldType == 'radio') {
                                var selected = v['sel'] ? ' checked' : '';
                                var choiceHTML = '<input type="radio" name="radio-' + uniqueID + '"' + selected + ' value="' + v['label'] + '">' + v['label'] + '<br />';
                                currentField.find(".choices").append(choiceHTML);
                            }

                            else if (fieldType == 'checkbox') {
                                var selected = v['sel'] ? ' checked' : '';
                                var choiceHTML = '<input type="checkbox" name="checkbox-' + uniqueID + '[]"' + selected + ' value="' + v['label'] + '">' + v['label'] + '<br />';
                                currentField.find(".choices").append(choiceHTML);
                            }

                        });
                    }

                    //Check if field is required
                    if (v['req']) {

                        if (fieldType == 'text') { currentField.find("input").prop('required',true) }
                        else if (fieldType == 'email') { currentField.find("input").prop('required',true) }
                        else if (fieldType == 'date') { currentField.find("input").prop('required',true) }
                        else if (fieldType == 'textarea') { currentField.find("textarea").prop('required',true) }
                        else if (fieldType == 'select') { currentField.find("select").prop('required',true) }
                        else if (fieldType == 'radio') { currentField.find("input").prop('required',true) }
												else if (fieldType == 'checkbox') {	currentField.find("input").prop('required',true)}

												/*if (fieldType == 'text') { currentField.find("input").addClass('required') }
                        else if (fieldType == 'email') { currentField.find("input").addClass('reqGroup') }
                        else if (fieldType == 'date') { currentField.find("input").addClass('reqGroup') }
                        else if (fieldType == 'textarea') { currentField.find("textarea").addClass('reqGroup') }
                        else if (fieldType == 'select') { currentField.find("select").addClass('reqGroup') }
                        else if (fieldType == 'radio') { currentField.find("input").addClass('reqGroup') }
												else if (fieldType == 'checkbox') { currentField.find("input").addClass('reqGroup') }*/
                    }

                });

                //HTML templates for rendering frontend form fields
                function addFieldHTML(fieldType, strLabel) {

                    switch (fieldType) {

                        case 'text':
                            return '' +
                                '<p>' +
                                '<input type="text" name="text-' + strLabel + '" placeholder="" pattern="[a-zA-Z0-9 ]+">' +
                                '</p>';

                        case 'email':
                            return '' +
                                '<p>' +
                                '<input type="email" name="email-' + strLabel + '" placeholder="" >' +
                                '</p>';

                        case 'date':
                            return '' +
                                '<p>' +
                                '<input type="text" class="calendar" id="datepicker1-' + strLabel + '" placeholder="" name="date-' + strLabel + '" size="20">' +
                                '</p>';

                        case 'textarea':
                            return '' +
                                '<p>' +
                                '<textarea name="textarea-' + strLabel + '" placeholder=""></textarea>' +
                                '</p>';

                        case 'select':
                            return '' +
                                '<p class="clearfix">' +
                                '<select name="select-' + strLabel + '" class="dropdownselect" title="Please select one."></select>' +
                                '</p>';

                        case 'radio':
                            return '' +
                                '<p class="withoptions">' +
                                '<label></label>' +
                                //'<div class="choices choices-radio"></div>' +
                                 '<span class="choices"></span>' +
                                '</p>';

                        case 'checkbox':
                            return '' +
                                '<p class="withoptions">' +
                                '<label></label>' +
                                //'<p class="choices choices-checkbox"></p>' +
                                '<span class="choices"></span>' +
                                '</p>';

                        case 'agree':
                            return '' +
                                '<p id="yeemcf-agree-' + strLabel + '" class="yeemcf-field yeemcf-agree required-field">' +
                                '<input type="checkbox" required>' +
                                '<label></label>' +
                                '</p>'
                    }
                }
            },
            error: function() { console.log("error"); }
        });

        $("body").on("focus", ".calendar", function() {
            $(this).datepicker();
        });



    }

})( jQuery );
