function getRoom() {
    var dataInfo;
    $.ajax({
        dataType: 'json',
        type: 'get',
        url: '/form/room',
        async: false,
        success: function (data) {
            dataInfo = data.data;
        }
    });
    return dataInfo;
}


