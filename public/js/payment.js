$(function() ***REMOVED***
    $('#payment').submit(function () ***REMOVED***
        $.post(window.location, ***REMOVED***
            gateway: $('input[name="gateway"]:checked').val()
        ***REMOVED***, function(result) ***REMOVED***
            var pay = window.open('about:blank', 'popUpWindow','height=750, width=1000, left=300, top=100, resizable=yes, scrollbars=yes, toolbar=yes, menubar=no, location=no, directories=no, status=yes'***REMOVED***
            if (pay) ***REMOVED***
                pay.document.write(result***REMOVED***
            ***REMOVED*** else ***REMOVED***
                document.write(result***REMOVED***
            ***REMOVED***
        ***REMOVED***, 'html'***REMOVED***

        return false;
    ***REMOVED******REMOVED***
***REMOVED******REMOVED***