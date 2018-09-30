$(document).ready(function() {

    $('a.ip-link').click(function (e) {
        e.preventDefault();
        $(e).after("<iframe src='" + $(e).attr('href') + "' style='display:block;'></iframe>");
    });
});

$("a.ip-link[data-busyurl]").click(function(e){
    e.preventDefault();
    e.stopPropagation();

    $("<iframe src='" + $(this).data("busyurl") + "'/>").insertAfter(this);

    return false;
});