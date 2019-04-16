$(document).ready(function () {

    $('#auth').click(function () {
        let apiKey = $('#api_key').val();
        $.ajax({
            method  : 'GET',
            url     : 'api/users/',
            dataType: 'json',
            headers : {
                'Authorization': 'Basic ' + btoa(':' + apiKey),
            },
            success : function (data) {
                let message = data['message'];
                alert(message);
            },
        });
    });

    $('#get_client').click(function () {
        let cardNumber = $('#card_number').val();
        $.ajax({
            method  : 'GET',
            url     : 'api/clients/' + cardNumber,
            dataType: 'json',
            success : function (data) {
                for (key in data) {
                    let loyaltyProgramField = key;
                    $("#" + loyaltyProgramField).val(data[key]);
                }
            },
        });
    });
});