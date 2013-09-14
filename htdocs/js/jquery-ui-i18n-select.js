function getJqueryUserLanguage(obj) {
 var l = self.navigator.language||self.navigator.userLanguage;
 if (l.toLowerCase() == 'en-us') return '';
 var l = l.toLowerCase().split('-');
 if (l.length == 1) {
   if (obj.regional[l[0]] != undefined) return l[0];
   else return '';
 }
 if (l.length > 1) {
   if (obj.regional[l[0] + '-' + l[1].toUpperCase()] != undefined) return l[0] + '-' + l[1].toUpperCase();
   else if (obj.regional[l[0]] != undefined) return l[0];
   else return '';
 }
 return '';
}

$(function() {
 $.datepicker.setDefaults( $.datepicker.regional[ getJqueryUserLanguage($.datepicker) ] );
 $.timepicker.setDefaults( $.timepicker.regional[ getJqueryUserLanguage($.timepicker) ] );
});

