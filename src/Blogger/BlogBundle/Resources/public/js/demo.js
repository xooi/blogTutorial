function initAjaxForm()
{
    $('body').on('submit', '.ajaxForm', function (e) {
 
        e.preventDefault();
 
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                alert(data.message);
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (typeof jqXHR.responseJSON !== 'undefined') {
                if (jqXHR.responseJSON.hasOwnProperty('form')) {
                    $('#form_body').html(jqXHR.responseJSON.form);
                }
 
                $('.post_form').html(jqXHR.responseJSON.message);
 
            } else {
                alert(errorThrown);
            }
 
        });
    });
}
