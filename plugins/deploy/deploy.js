jQuery(document).ready( function($) {

    $('#reset').click( function(event) {
        return confirm('This will overwrite your data file with settings from the database. Are you sure you want to do this?');
    });

    // select all

    $('.cb-select-all').change( function() {
        var checked = this.checked;
        $('.cb-select-all').parents('table').find('.cb-select').each( function(){
            $(this).prop('checked', checked);
        });
    });
    $('.cb-select').change( function() {
        var checked = this.checked,
            all;
        if (checked) {

            $(this).parents('table').find('.cb-select').each( function(e){
                if (all !== false) all = this.checked;
                console.log(all, e);
            });

            if (all)
                $(this).parents('table').find('.cb-select-all').prop('checked', true);
        
        } else {
            $(this).parents('table').find('.cb-select-all').prop('checked', checked);
        }
    });

    // tabs

    $('#deploy-nav-tab a').click( function() {
        var href = $(this).attr('href');

        $('.nav-tab-active').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        $('.deploy-tab').hide();
        $(href).show();

        return false;
    });

});