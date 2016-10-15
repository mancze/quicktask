function subscribeHandlers(where) {
    $(where).find('.datepicker').datepicker({
        orientation: 'left bottom'
    });
}

$(document).ready(function () {
    $.nette.init();
    subscribeHandlers(document);
    $.nette.ext('snippets').after(subscribeHandlers);
});
