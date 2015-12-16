$(function() {
    $('#payment').submit(function () {
        $.post(window.location, {
            gateway: $('input[name="gateway"]:checked').val()
        }, function(result) {
            var pay = window.open('about:blank', 'popUpWindow','height=750, width=1000, left=300, top=100, resizable=yes, scrollbars=yes, toolbar=yes, menubar=no, location=no, directories=no, status=yes');
            if (pay) {
                pay.document.write(result);
            } else {
                document.write(result);
            }
        }, 'html');

        return false;
    });
});