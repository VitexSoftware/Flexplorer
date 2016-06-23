
/**
 * System.Spoje.Net - Zarolování statusbaru
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2015 Spoje.Net
 */


$(document).ready(function () {
    setTimeout(function () {
        $("#StatusMessages").slideUp("slow");
        $("#smdrag").fadeTo("slow", 0.25);
    }, 6000);

    $('#smdrag').on('mousedown', function (e) {
        $("#smdrag").fadeTo("slow", 1);
        $("#StatusMessages").slideDown("slow");

        var $dragable = $('#StatusMessages'),
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

    $("#StatusMessages").click(function () {
        $("#StatusMessages").slideUp("slow");
        $("#StatusMessages").attr('data-state', 'up');
        $("#smdrag").fadeTo("slow", 0.25);
    });

});


