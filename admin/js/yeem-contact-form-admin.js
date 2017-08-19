(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

    jQuery(document).ready(function () {

        //Append a field
        $("#fieldSelection a").click(function(event) {
            event.preventDefault();
            $(appendField($(this).data('type'))).appendTo('#fieldSection').hide().slideDown('fast');
            $('#fieldSection').sortable();
        });

        //Remove a field or option
        $("#yeemcf").on("click", ".remove", function() {
            if (confirm('Are you sure you want to remove this field?')) {
                var $this = $(this);
                $this.parent().slideUp( "slow", function() {
                    $this.parent().remove()
                });
            }
        });

        //Turn field into required
        $("#yeemcf").on("click", ".makeRequired", function() {
            addRequired($(this));
        });

        //Turn option into selected
        $("#yeemcf").on("click", ".makeSelected", function() {
            addSelectedOption($(this));
        });

        //Add a new option to a field
        $("#yeemcf").on("click", ".addOption", function() {
            $(addOption()).appendTo($(this).prev()).hide().slideDown('fast');
            $('.options ul').sortable();
        });

        //Saving form
        $("#yeemcf").submit(function(event) {
            event.preventDefault();

            //Loop through fields and save field data to array
            var fields = [];

            $('.field').each(function() {

                var $this = $(this);

                //field type
                var fieldType = $this.data('type');

                //field label
                var fieldLabel = $this.find('.fieldLabel').val();

                //field required
                var fieldReq = $this.hasClass('required') ? 1 : 0;

                //check if this field has options
                if($this.find('.options li').length >= 1) {

                    var options = [];

                    $this.find('.options li').each(function() {

                        var $thisOption = $(this);

                        //option label
                        var optionLabel = $thisOption.find('.optionLabel').val();

                        //option selected
                        var optionSelected = $thisOption.hasClass('selected') ? 1 : 0;

                        options.push({
                            label: optionLabel,
                            sel: optionSelected
                        });

                    });
                }

                fields.push({
                    type: fieldType,
                    label: fieldLabel,
                    req: fieldReq,
                    options: options
                });

            });

            //var data = JSON.stringify([{"name":"formId","value":formId},{"name":"fieldSection","value":fields}]);
            //var data = JSON.stringify(fields);
            var data = {
                            action: 'yeem_save_form',
                            formfields: JSON.stringify(fields)
                        };

            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: data,
                //dataType: 'json',
                success: function (msg) {
                    console.log(msg);
                    $('.alert').removeClass('hide');
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                },
                error: function() { console.log("error"); }
            });
        });

        //load saved form
        loadFormDB();


    });

    //Append field to form layout
    function appendField(fieldType) {

        var bRequired, bOptions = false;
        var strRequiredDiv ='', strOptionsDiv = '';

        switch (fieldType) {
            case 'text':
            case 'email':
            case 'date':
            case 'textarea':
                bRequired = true;
                bOptions = false;
                break;
            case 'select':
            case 'radio':
                bRequired = true;
                bOptions = true;
                break;
            case 'checkbox':
                bRequired = true;
                bOptions = true;
                break;
        }

        if (bRequired) {
            strRequiredDiv = '' +
                '<div class="reqCheckbox">Required? ' +
                '<input class="makeRequired" type="checkbox">' +
                '</div>'
        }

        if (bOptions) {
            strOptionsDiv = '' +
                '<div class="options">' +
                '<ul></ul>' +
                '<button type="button" class="addOption">Add Option</button>' +
                '</div>'
        }

        return '' +
            '<div class="field ui-sortable-handle" data-type="' + fieldType + '">' +
	            '<button type="button" class="remove glyphicon glyphicon-trash"></button>' +
	            '<div class="fieldTypeText"><em>' + fieldType.toUpperCase() + ' Field </em></div>' +
	            '<div class="displayText">Text to Display: ' +
	            '<input type="text" class="fieldLabel" size="50">' +
            '</div>' +
            strRequiredDiv +
            strOptionsDiv;
    }

    //Make field required
    function addRequired($this) {
        if (!$this.parents('.field').hasClass('required')) {
            $this.parents('.field').addClass('required');
            $this.attr('checked','checked');
        } else {
            $this.parents('.field').removeClass('required');
            $this.removeAttr('checked');
        }
    }

    //Make option selected
    function addSelectedOption($this) {
        if (! $this.parents('li').hasClass('selected')) {

            //Checkboxes with multiple options that can be marked as selected
            if ($this.parents('.field').data('type') != 'checkbox') {
                $this.parents('.options').find('li').removeClass('selected');
                $this.parents('.options').find('.makeSelected').not($this).removeAttr('checked');
            }

            $this.parents('li').addClass('selected');
            $this.attr('checked','checked');

        } else {

            //Remove selected class
            $this.parents('li').removeClass('selected');
            $this.removeAttr('checked');

        }
    }

    //Add an option for select, radio, and checkbox objects
    function addOption() {
        return '' +
            '<li>' +
						'<button type="button" class="remove glyphicon glyphicon-trash"></button>' +
            '<div class="optionTypeText">' +
							'Option: <input type="text" class="optionLabel"> ' +
            	'Selected by default? <input class="makeSelected" type="checkbox">' +
						'<div>' +
            '</li>'
    }

    //Load the correct form fields from the database into the form builder layout
    function loadFormDB() {

        var formfields;
        var data = {    action: 'yeem_get_formelements' };

        $.ajax({
            method: "POST",
            url: ajaxurl,
            data: data,
            //dataType: 'json',
            success: function(msg){

                formfields = JSON.parse(msg);

                //Loop through each field object
                $.each( formfields, function( key, val ) {
                    //Add the field
                    $(appendField(val['type'])).appendTo('#fieldSection').hide().slideDown('fast');
                    var $currFieldObj = $('#fieldSection .field').last();

                    //Add the label
                    $currFieldObj.find('.fieldLabel').val(val['label']);

                    //Mark field as required
                    if (val['req']) {
                        addRequired($currFieldObj.find('.makeRequired'));
                    }

                    //Render options if any
                    if (val['options']) {
                        $.each( val['options'], function( key, val ) {

                            $currFieldObj.find('.options ul').append(addOption());
                            $currFieldObj.find('.optionLabel').last().val(val['label']);
                            if (val['sel']) {
                                addSelectedOption($currFieldObj.find('.makeSelected').last());
                            }
                        });
                    }

                });
                $('#fieldSection').sortable({
                    tolerance: 'touch',
                    drop: function () {
                        alert('delete!');
                    }
                });
                $('.options ul').sortable();

            },
            error: function() { console.log("error"); }
        });
    }

    //Load the form fields from a temp text file (deprecated)
    function loadForm() {
        $.getJSON(yeemScriptObj.pluginsUrl + '/tmp/form1.txt', function(data) {
            if (data) {
                //Loop through each field object
                $.each( data, function( key, val ) {
                    //Add the field
                    $(appendField(val['type'])).appendTo('#fieldSection').hide().slideDown('fast');
                    var $currFieldObj = $('#fieldSection .field').last();

                    //Add the label
                    $currFieldObj.find('.fieldLabel').val(val['label']);

                    //Mark field as required
                    if (val['req']) {
                        addRequired($currFieldObj.find('.makeRequired'));
                    }

                    //Render options if any
                    if (val['options']) {
                        $.each( val['options'], function( key, val ) {

                            $currFieldObj.find('.options ul').append(addOption());
                            $currFieldObj.find('.optionLabel').last().val(val['label']);
                            if (val['sel']) {
                                addSelectedOption($currFieldObj.find('.makeSelected').last());
                            }
                        });
                    }

                });

                $('#fieldSection').sortable({
                    tolerance: 'touch',
                    drop: function () {
                        alert('delete!');
                    }
                });
                $('.options ul').sortable();
            }
        });
    }

})( jQuery );
