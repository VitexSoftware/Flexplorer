function saveColumnData(saverClass, key, field, evidence, value) {
    var input = $("[name='" + field + "']");

    if ((value == undefined) || (value == NaN)) {
        value = input.val();
    }

    $.post('datasaver.php', {
        SaverClass: saverClass,
        Field: field,
        Value: value,
        Key: key,
        Evidence: evidence,
        success: function () {
            input.css({borderColor: "#0f0", borderStyle: "solid"}).animate({borderWidth: '5px'}, 'slow', 'linear');
            input.animate({borderColor: 'gray', borderWidth: '1px'});
        }
    }
    ).fail(function () {
        input.css({borderColor: "#f00", borderStyle: "solid"}).animate({borderWidth: '5px'}, 'slow', 'linear');
        input.animate({borderColor: 'gray', borderWidth: '1px'});
    });
}
;
