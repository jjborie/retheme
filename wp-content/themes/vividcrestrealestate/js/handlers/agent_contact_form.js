(function($) { $(function() { 
    'use strict';
    
    // Show necessary forms
    $('.agent__contact-form a[data-action="request-information"]').on('click', function(ev) {
        ev.preventDefault();
        
        $('#agent__contact').toggle();
        
    });
    
    $('.agent__contact-form a[data-action="request-showing"]').on('click', function(ev) {
        ev.preventDefault();
        
       $('#agent__contact-appointment').toggle();
    });
    
    
    
    
    // Set datepickers
    $('.agent__contact-form input.date').datepicker({
        dateFormat: 'yy-mm-dd'
    });
    
    
    
    
    // Send form
    $('#agent__contact-form, #agent__contact-appointment').on('submit', function(ev) { 
        ev.preventDefault();
        
        // Perepare 
        var form = $(this);
        var data = $(this).serialize() + '&action=vividcrest_send_form&contact[property_link]=' + document.location.href;
        var url = '/wp-admin/admin-ajax.php';
        
        // Make request
        $.post(url, data, function(response) {
            if (response == 1) {
                form.find('input[type="submit"]')
                    .after('<p>Form sent</p>')
                    .remove();
            } else {
                console.log(response);
            }        
        });   
    });
}) })(jQuery)
