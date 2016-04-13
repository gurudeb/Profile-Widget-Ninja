/*jslint browser:true*/
/*global jQuery*/
jQuery(document).ready(function ($) {
    "use strict";
    $('.pwn-background-color').wpColorPicker();
    $(document).ajaxComplete(function () {
        $('.pwn-background-color').wpColorPicker();
    });
});