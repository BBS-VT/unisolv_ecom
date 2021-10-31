/**
 * Theme: Dastone - Responsive Bootstrap 4 admin Dashboard
 * Author: Mannatthemes
 * Clipboard Js
 */


var clipboard = new ClipboardJS('.btn');

clipboard.on('success', function(e) {
    console.log(e);
});

clipboard.on('error', function(e) {
    console.log(e);
});
