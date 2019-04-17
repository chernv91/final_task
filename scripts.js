$(document).ready(function () {

    $('#max_possible_sum').parent().hide();

    $("#purchase_sum").change(function () {
        let purchaseSum = $('#sale #purchase_sum').val();
        $.ajax({
            url     : 'api/calculators/max_possible_bonuses_sum/' + purchaseSum,
            dataType: 'json',
            success : function (data) {
                $('#max_possible_sum').text(data);
            },
        });
        $('#max_possible_sum').parent().show();
    });

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
                    if ('id' === key) {
                        $("#hidden_params #client_id").val(data[key]);
                    } else if ('total_sum' === key) {
                        $("#hidden_params #total_sum").val(data[key]);
                    } else {
                        let loyaltyProgramField = key;
                        $("#find #" + loyaltyProgramField).val(data[key]);
                    }

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
            method : 'POST',
            url    : 'api/clients/',
            //dataType: 'json',
            data   : {
                first_name : firstName,
                middle_name: middleName,
                last_name  : lastName,
                birthday   : birthday,
                phone      : phone,
                card_number: cardNumber,
                discount   : discount
            },
            success: function (data) {
                if (data && cardNumber) {
                    let id = data;
                    $.ajax({
                        method: 'POST',
                        url   : 'api/cardoperations/',
                        //dataType: 'json',
                        data  : {
                            name     : 'Выпуск карты',
                            client_id: id,
                            //user_ip_key: '',
                        },
                        /*success : function (data) {
                            alert(data);
                        },*/
                    });
                }
            },
        });

    });

    $('#subtract_bonuses').click(function () {
        let maxPossibleSum = $('#max_possible_sum').text();
        let bonusesForSubtract = $('#bonuses_for_subtract').val();

        if (+bonusesForSubtract > +maxPossibleSum) {
            alert('Нельзя списать количество бонусов больше максимально допустимого!');
        } else {
            var purchase_sum = $('#purchase_sum').val();
            $('#purchase_sum').val(purchase_sum - bonusesForSubtract);
            $('#hidden_params #subtract_bonuses').val('true');
        }
    });

    $('#create_purchase').click(function () {
        let id = $('#hidden_params #client_id').val();
        let purchaseSum = $('#sale #purchase_sum').val();
        let bonusBalance = $('#find #bonus_balance').val();
        let totalSum = $('#hidden_params #total_sum').val();
        let operation = $('#hidden_params #subtract_bonuses').val();
        totalSum = +totalSum + +purchaseSum;

        if ('false' === operation) {
            $.ajax({
                url     : 'api/calculators/bonuses/' + id + '/' + purchaseSum,
                dataType: 'json',
                success : function (data) {
                    if (data) {
                        let newBonuses = data;
                        bonusBalance = +bonusBalance + +newBonuses;

                        $.ajax({
                            method : 'PUT',
                            url    : 'api/clients/',
                            //dataType: 'json',
                            data   : {
                                id           : id,
                                bonus_balance: bonusBalance,
                                total_sum    : totalSum,
                                operation    : 'add_bonuses'
                            },
                            success: function (data) {
                                alert(data);
                                $.ajax({
                                    method: 'POST',
                                    url   : 'api/cardoperations/',
                                    //dataType: 'json',
                                    data  : {
                                        name     : 'Начисление бонусов',
                                        client_id: id,
                                        new_value: newBonuses,
                                        //user_ip_key: '',
                                    },
                                    /*success : function (data) {
                                        alert(data);
                                    },*/
                                });
                                $.ajax({
                                    method: 'POST',
                                    url   : 'api/cardoperations/',
                                    //dataType: 'json',
                                    data  : {
                                        name     : 'Регистрация оборота по карте',
                                        client_id: id,
                                        new_value: totalSum,
                                        //user_ip_key: '',
                                    },
                                    /*success : function (data) {
                                        alert(data);
                                    },*/
                                });

                            },
                        });
                    }
                },
            });
        } else {
            let bonusesForSubtract = $('#bonuses_for_subtract').val();
            bonusBalance = +bonusBalance - +bonusesForSubtract;

            $.ajax({
                method : 'PUT',
                url    : 'api/clients/',
                //dataType: 'json',
                data   : {
                    id           : id,
                    bonus_balance: bonusBalance,
                    total_sum    : totalSum,
                    operation    : 'subtract_bonuses'
                },
                success: function (data) {
                    alert(data);
                    $.ajax({
                        method: 'POST',
                        url   : 'api/cardoperations/',
                        //dataType: 'json',
                        data  : {
                            name     : 'Списание бонусов',
                            client_id: id,
                            new_value: bonusesForSubtract,
                            //user_ip_key: '',
                        },
                        /*success : function (data) {
                            alert(data);
                        },*/
                    });
                    $.ajax({
                        method: 'POST',
                        url   : 'api/cardoperations/',
                        //dataType: 'json',
                        data  : {
                            name     : 'Регистрация оборота по карте',
                            client_id: id,
                            new_value: totalSum,
                            //user_ip_key: '',
                        },
                        /*success : function (data) {
                            alert(data);
                        },*/
                    });

                },
            });

        }

    });
})
;