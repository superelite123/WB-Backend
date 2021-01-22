$('#print_barcode').click(() => {
    $('.coa_area').addClass('pdf_area')
    $('#bd-example-modal-sm').modal('hide')
    window.print()
})
$('.control_panel').click(() => {
    $('#bd-example-modal-sm').modal('show')
})
$('.btnPrint').click(function() {
    $(this).removeClass('btn-info').addClass('btn-success')
    $(this).html('<i class="fa fa-check">&nbsp;</i>Printed')
})
$(($) => {
    $('#bd-example-modal-sm').modal('show')

})