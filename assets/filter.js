jQuery(document).ready(function($) {
    $('#filter-city').on('change', function() {
        var city = $(this).val(); 
        var data = {
            action: 'filter_travel_packages',
            city: city,
        };
        $.ajax({
            url: ajax_params.ajax_url, 
            data: data,
            type: 'POST',
            success: function(response) {
                $('.travel-packages-list').html(response); 
            },
            error: function(xhr, status, error) {
                console.log(erorr);
            }
        });
    });
});
