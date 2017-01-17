jQuery(document).ready( function($) {
    $("#force-update").click( function(e) {
        e.preventDefault;
        $('#result').html("Ajax called");
        $.post(
            ajaxurl,
            {
                'action': 'kk_force_update' // function to call
            },
            function(response){
                $('#result').html(response);
            }
        );
        return false;
    });
});