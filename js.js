$(document).ready(function() {

    $('a.ip-link').click(function (e) {
        e.preventDefault();
        $(e).after("<iframe src='" + $(e).attr('href') + "' style='display:block;'></iframe>");
    });
});