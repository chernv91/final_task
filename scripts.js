$(document).ready(function () {

    $.ajax({
        url     : 'http://charlie.rarus-crimea.ru/api/configurators/',
        crossDomain: true,
        dataType: 'json',
        header: {
            'Access-Control-Allow-Orgin': '*',
        },
        success : function (data) {
            console.log(data);
            let loyaltyProgram = '';
            let cardNumberType = '';
            for (key in data.loyalty_program) {
                if (data.loyalty_program[key] === true) {
                    loyaltyProgram = key;
                    break;
                }
            }

            for (key in data.card_number_type) {
                if (data.card_number_type[key] === true) {
                    cardNumberType = key;
                    break;
                }
            }

            $('#settings #max_percent').val(data.bonus_payment_percent);
            $('#settings #loyalty_program').text(loyaltyProgram);
            $('#settings #card_number_type').text(cardNumberType);
        },
    });

    $('#edit_loyalty_program').click(function () {
        $.ajax({
            url     : 'http://charlie.rarus-crimea.ru/api/configurators/',
            dataType: 'json',
            success : function (data) {
                let html = '';
                for (key in data.loyalty_program) {
                    if (data.loyalty_program[key] === true) {
                        html += '<input type="radio" name="loyalty_program" value="' + key + '" checked>' + key;
                    } else {
                        html += '<input type="radio" name="loyalty_program" value="' + key + '">' + key;
                    }
                }
                html += '<br><input type="button" id="save_loyalty_program" value="Сохранить настройки">';
                $('#loyalty_program').parent().after(html);
            }
        });
    });

    $('#edit_card_number_type').click(function () {
        $.ajax({
            url     : 'http://charlie.rarus-crimea.ru/api/configurators/',
            dataType: 'json',
            success : function (data) {
                let html = '';
                for (key in data.card_number_type) {
                    if (data.card_number_type[key] === true) {
                        html += '<input type="radio" name="card_number_type" value="' + key + '" checked>' + key;
                    } else {
                        html += '<input type="radio" name="card_number_type" value="' + key + '">' + key;
                    }
                }
                html += '<br><input type="button" id="save_card_number_type" value="Сохранить настройки">';
                $('#card_number_type').parent().after(html);
            }
        });
    });

    $('#max_possible_sum').parent().hide();

    $("#purchase_sum").change(function () {
        let purchaseSum = $('#sale #purchase_sum').val();
        $.ajax({
            url    : 'http://charlie.rarus-crimea.ru/api/calculators/max_possible_bonuses_sum/' + purchaseSum,
            //dataType: 'json',
            success: function (data) {
                $('#max_possible_sum').text(data);
            },
        });
        $('#max_possible_sum').parent().show();
    });

    $('#auth').click(function () {
        let apiKey = $('#api_key').val();
        $.ajax({
            url     : 'http://charlie.rarus-crimea.ru/api/users/',
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
            url     : 'http://charlie.rarus-crimea.ru/api/clients/' + cardNumber,
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
            error   : function (data) {
                alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
            }
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
            url    : 'http://charlie.rarus-crimea.ru/api/clients/',
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
                        url   : 'http://charlie.rarus-crimea.ru/api/card_operations/',
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
            $('#hidden_params #operation').val('subtract_bonuses');
        }
    });

    $('#create_purchase').click(function () {
        let id = $('#hidden_params #client_id').val();
        let purchaseSum = $('#sale #purchase_sum').val();
        let bonusBalance = $('#find #bonus_balance').val();
        let totalSum = $('#hidden_params #total_sum').val();
        let operation = $('#hidden_params #subtract_bonuses').val();
        totalSum = +totalSum + +purchaseSum;

        if ('add_bonuses' === operation) {
            $.ajax({
                url     : 'http://charlie.rarus-crimea.ru/api/calculators/bonuses/' + id + '/' + purchaseSum,
                dataType: 'json',
                success : function (data) {
                    if (data) {
                        let newBonuses = data;
                        bonusBalance = +bonusBalance + +newBonuses;

                        $.ajax({
                            method : 'PUT',
                            url    : 'http://charlie.rarus-crimea.ru/api/clients/',
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
                                    url   : 'http://charlie.rarus-crimea.ru/api/card_operations/',
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
                                    url   : 'http://charlie.rarus-crimea.ru/api/card_operations/',
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
                url    : 'http://charlie.rarus-crimea.ru/api/clients/',
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
                        method : 'POST',
                        url    : 'http://charlie.rarus-crimea.ru/api/card_operations/',
                        //dataType: 'json',
                        data   : {
                            name     : 'Списание бонусов',
                            client_id: id,
                            new_value: bonusesForSubtract,
                            //user_ip_key: '',
                        },
                        success: function (data) {
                            alert(data);
                        },
                    });
                    $.ajax({
                        method: 'POST',
                        url   : 'http://charlie.rarus-crimea.ru/api/card_operations/',
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

    $('#block_card').click(function () {
        let cardNumber = $('#find #card_number').val();
        let id = $('#hidden_params #client_id').val();
        $.ajax({
            method : 'PUT',
            url    : 'http://charlie.rarus-crimea.ru/api/clients/',
            //dataType: 'json',
            data   : {
                card_number: cardNumber,
                card_status: 'Заблокирована',
                operation  : 'block_card'
            },
            success: function (data) {
                $.ajax({
                    method: 'POST',
                    url   : 'http://charlie.rarus-crimea.ru/api/card_operations/',
                    //dataType: 'json',
                    data  : {
                        name     : 'Изменение статуса карты',
                        client_id: id,
                        new_value: 'Заблокирована',
                        //user_ip_key: '',
                    },
                    /*success : function (data) {
                        alert(data);
                    },*/
                });
            }
        });
    });

    $('#unblock_card').click(function () {
        let id = $('#hidden_params #client_id').val();
        let cardNumber = $('#find #card_number').val();
        $.ajax({
            method : 'PUT',
            url    : 'http://charlie.rarus-crimea.ru/api/clients/',
            //dataType: 'json',
            data   : {
                card_number: cardNumber,
                card_status: 'Активна',
                operation  : 'unblock_card'
            },
            success: function (data) {
                $.ajax({
                    method: 'POST',
                    url   : 'http://charlie.rarus-crimea.ru/api/card_operations/',
                    //dataType: 'json',
                    data  : {
                        name     : 'Изменение статуса карты',
                        client_id: id,
                        new_value: 'Активна',
                        //user_ip_key: '',
                    },
                    /*success : function (data) {
                        alert(data);
                    },*/
                });
            }
        });
    });

    $('#change_percent').click(function () {
        let id = $('#hidden_params #client_id').val();
        let cardNumber = $('#find #card_number').val();
        let oldPercent = $('#find #discount').val();
        let newPercent = $('#new_percent').val();
        $.ajax({
            method : 'PUT',
            url    : 'http://charlie.rarus-crimea.ru/api/clients/',
            //dataType: 'json',
            data   : {
                card_number: cardNumber,
                discount   : newPercent,
                operation  : 'change_percent'
            },
            success: function (data) {
                alert('ok');
                $.ajax({
                    method : 'POST',
                    url    : 'http://charlie.rarus-crimea.ru/api/card_operations/',
                    //dataType: 'json',
                    data   : {
                        name     : 'Изменение процента по карте',
                        client_id: id,
                        old_value: oldPercent,
                        new_value: newPercent,
                        //user_ip_key: '',
                    },
                    success: function (data) {
                        alert(data);
                    },
                });
            }
        });
    });

    $('#subtracted_bonuses_sum').click(function () {
        $.ajax({
            url     : 'http://charlie.rarus-crimea.ru/api/card_operations/subtracted_bonuses_sum',
            dataType: 'json',
            success : function (data) {
                $('#subtracted_bonuses_res').val(data);
            }
        });
    });

    $('#card_bonuses_sum').click(function () {
        $.ajax({
            url     : 'http://charlie.rarus-crimea.ru/api/card_operations/card_bonuses_sum',
            dataType: 'json',
            success : function (data) {
                $('#card_bonuses_res').val(data);
            }
        });
    });

    // за период добавить даты
    $('#card_history').click(function () {
        let cardNumber = $('#reports #card_number').val();
        $.ajax({
            url     : 'http://charlie.rarus-crimea.ru/api/card_operations/card_history/' + cardNumber,
            dataType: 'json',
            success : function (data) {
                let html = '<table border="1" cellspacing="0" cellpadding="5">' +
                    '<th>Наименование операции</th><th>Дата</th><th>Предыдущее значение</th><th>Актуальное значение</th>';
                for (key in data) {
                    html += '<tr align="center">';
                    for (key2 in data[key]) {
                        html += '<td>' + data[key][key2] + '</td>';
                    }
                    html += '</tr>';
                }
                html += '</table>';
                //alert(html);
                $('#card_history').after(html);
            }
        });
    });

    $('#cards_count').click(function () {
        $.ajax({
            url    : 'http://charlie.rarus-crimea.ru/api/clients/cards_count',
            //dataType: 'json',
            success: function (data) {
                $('#cards_count_res').val(data);
            }
        });
    });
})
;