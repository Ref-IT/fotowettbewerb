$(function() {
 if($('.datetimepicker').prop('type') == 'text') {
  $('.datetimepicker').datetimepicker({dateFormat: 'yy-mm-dd',timeFormat: 'HH:mm:ss',separator: 'T',showWeek: true,firstDay: 1});
 }
});
