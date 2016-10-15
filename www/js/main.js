var submitFormDelayTimeoutId = null;

function submitFormDelayed(formEl) {
    if (submitFormDelayTimeoutId) {
        // cancel previous timeout
        window.clearTimeout(submitFormDelayTimeoutId);
    }
     
    submitFormDelayTimeoutId = window.setTimeout(submitForm, 500, formEl);
}

function submitForm(formEl) {
    $(formEl).closest("form").submit();
}

function subscribeHandlers(where) {
    $(where).find("input").iCheck({
        checkboxClass: 'icheckbox_square-blue'
    });
    $(where).find('.datepicker').datepicker({
        orientation: 'left bottom'
    });
    $(where).find("input.search-tasks-input").keyup(function() { submitFormDelayed(this); });
}

$(document).ready(function () {
    $.nette.init();
    subscribeHandlers(document);
    $.nette.ext('snippets').after(subscribeHandlers);
});
