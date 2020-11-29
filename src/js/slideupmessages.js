
/**
 * System.Spoje.Net - Zarolování statusbaru
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2015 Spoje.Net
 */


$(document).ready(function () {
    setTimeout(function () {
        $("#status-messages").slideUp("slow");
        $("#smdrag").fadeTo("slow", 0.25);
    }, 6000);

    $('#smdrag').on('mousedown', function (e) {
        $("#smdrag").fadeTo("slow", 1);
        $("#status-messages").slideDown("slow");

        var $dragable = $('#status-messages'),
                startHeight = $dragable.height() - 10,
                pY = e.pageY;

        $(document).on('mouseup', function (e) {
            $(document).off('mouseup').off('mousemove');
        });
        $(document).on('mousemove', function (me) {
            var my = (me.pageY - pY);

            $dragable.css({
                height: startHeight + my
            });
        });

    });

    $("#status-messages").click(function () {
        $("#status-messages").slideUp("slow");
        $("#status-messages").attr('data-state', 'up');
        $("#smdrag").fadeTo("slow", 0.25);
    });

});


