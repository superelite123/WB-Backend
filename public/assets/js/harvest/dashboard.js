$(function() {
    $('.stockimg').on('click', function() {
        $('.imagepreview').attr('src', $(this).attr('src'))
        $('#imagemodal').modal('show')
    });		

    $("#btnPrint").click(() => {
       window.print()
    })
});