$(document).ready(function () {

    $('#auth').click(function () {
        let apiKey = $('#api_key').val();
        $.ajax({
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
        let cardNumber = $('#find #card_number').val();
        $.ajax({
            url     : 'api/clients/' + cardNumber,
            dataType: 'json',
            success : function (data) {
                for (key in data) {
                    let loyaltyProgramField = key;
                    $("#find #" + loyaltyProgramField).val(data[key]);
                }
            },
        });
    });

    $('#add_client').click(function () {
        let firstName = $('#first_name').val();
        let middleName = $('#middle_name').val();
        let lastName = $('#last_name').val();
        let birthday = $('#birthday').val();
        let phone = $('#phone').val();
        let cardNumber = $('#add #card_number').val();
        let discount = $('#add #discount').val();
        $.ajax({
            method  : 'POST',
            url     : 'api/clients/',
            //dataType: 'json',
            data: {
                first_name: firstName,
                middle_name: middleName,
                last_name: lastName,
                birthday: birthday,
                phone: phone,
                card_number: cardNumber,
                discount: discount
            },
            success : function (data) {
                alert(data);
            },
        });
    });
});