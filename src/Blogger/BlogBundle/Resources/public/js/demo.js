function initAjaxForm()
{
    $('input[type="submit"]').click(function () {
    $('#form').submit(function(e){
        e.preventDefault();
        $(this).ajaxSubmit(
        {
            url: "{{ path('add_contact') }}",
            type: "POST",
            success: function (result)
            {
               $('#form_body').html(result);
            }
        });
    });
});
}
