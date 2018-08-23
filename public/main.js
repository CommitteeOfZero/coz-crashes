$(function() {
  $('.report-modal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var guid = button.data('guid');
    var modal = $(this);
    modal.find('.modal-guid-input').val(guid);
    modal.find('.modal-guid-label').text(guid);
  });
});