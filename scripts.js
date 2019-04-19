$(document).ready(function () {

    $('#for_operator, #for_manager').hide();

    $('#auth').click(function () {
        let apiKey = $('#api_key').val();
        $.ajax({
            url     : 'http://charlie.rarus-crimea.ru/api/users/',
            dataType: 'json',
            headers : {
                'Authorization': 'Basic ' + btoa(':' + apiKey),
            },
            success : function (data) {
                let message = data.message;
                alert(message);
                let role = data.role;
                $('#user_api_key').val(apiKey);
                $('#authorization').hide();
                if ('Оператор' === role) {
                    $('#for_operator').show();
                } else {
                    $('#for_operator, #for_manager').show();
                }
                $.ajax({
                    url        : 'http://charlie.rarus-crimea.ru/api/configurators/',
                    dataType   : 'json',
                    success    : function (data) {
                        alert('Настройки приложения получены');
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

                        if ('Скидка' === loyaltyProgram) {
                            $('#find #bonus_balance').hide();
                            $('#find #bonus_balance').prev().hide();
                            $('#max_percent').hide();
                            $('#max_percent').prev().hide();
                            $('#subtract').hide();
                        } else {
                            $('#find #discount').hide();
                            $('#find #discount').prev().hide();
                            $('#discount_operations').hide();
                            $('#use_discount').hide();
                        }

                        if ('Номер телефона' === cardNumberType) {
                            $('#change_card_status').hide();
                        }

                        $('#settings #max_percent').val(data.bonus_payment_percent);
                        $('#settings #loyalty_program').text(loyaltyProgram);
                        $('#settings #card_number_type').text(cardNumberType);
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
                                },
                                error   : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                }
                            });
                        });

                        $('body').on('click', '#save_loyalty_program', function () {
                            let checked = $('#loyalty').find('[checked]').val();
                            $.ajax({
                                method  : 'PUT',
                                url     : 'http://charlie.rarus-crimea.ru/api/configurators/',
                                dataType: 'json',
                                data    : {
                                    checked: checked,
                                    setting: 'loyalty_program',
                                },
                                success : function (data) {
                                    alert(data.code + ' - ' + data.message);
                                },
                                error   : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                }
                            });
                        });

                        $('body').on('click', '[name=card_number_type]', function () {
                            $('#card').find('[checked]').removeAttr('checked');
                            $(this).attr('checked', 'true');
                        });

                        $('body').on('click', '#save_card_number_type', function () {
                            let checked = $('#card').find('[checked]').val();
                            $.ajax({
                                method  : 'PUT',
                                url     : 'http://charlie.rarus-crimea.ru/api/configurators/',
                                dataType: 'json',
                                data    : {
                                    checked: checked,
                                    setting: 'card_number_type',
                                },
                                success : function (data) {
                                    alert(data.code + ' - ' + data.message);
                                },
                                error   : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                }
                            });
                        });

                        $('body').on('click', '[name=loyalty_program]', function () {
                            $('#loyalty').find('[checked]').removeAttr('checked');
                            $(this).attr('checked', 'true');
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
                                },
                                error   : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                }
                            });
                        });

                        $('#max_possible_sum').parent().hide();
                        $("#purchase_sum").change(function () {
                            if ('Скидка' !== loyaltyProgram) {
                                let purchaseSum = $('#sale #purchase_sum').val();
                                $.ajax({
                                    url    : 'http://charlie.rarus-crimea.ru/api/calculators/max_possible_bonuses_sum/' + purchaseSum,
                                    dataType: 'json',
                                    success: function (data) {
                                        $('#max_possible_sum').text(data.maxSum);
                                    },
                                    error  : function (data) {
                                        alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                    }
                                });
                                $('#max_possible_sum').parent().show();
                            }
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
                                method  : 'POST',
                                url     : 'http://charlie.rarus-crimea.ru/api/clients/',
                                dataType: 'json',
                                data    : {
                                    first_name : firstName,
                                    middle_name: middleName,
                                    last_name  : lastName,
                                    birthday   : birthday,
                                    phone      : phone,
                                    card_number: cardNumber,
                                    discount   : discount
                                },
                                success : function (data) {
                                    alert(data.code + ' - ' + data.message);
                                    if (data && cardNumber) {
                                        let id = data.id;
                                        $.ajax({
                                            method: 'POST',
                                            url   : 'http://charlie.rarus-crimea.ru/api/card_operations/',
                                            //dataType: 'json',
                                            data  : {
                                                name        : 'Выпуск карты',
                                                client_id   : id,
                                                user_api_key: apiKey,
                                            },
                                            /*success : function (data) {
                                                alert(data);
                                            },*/
                                        });
                                    }
                                },
                                error   : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                }
                            });

                        });

                        $('#use_discount').click(function () {
                            let id = $('#hidden_params #client_id').val();
                            let discount = $('#find #discount').val();
                            let purchaseSum = $('#purchase_sum').val();
                            let discountSum = purchaseSum * discount / 100;
                            let totalSum = $('#hidden_params #total_sum').val();
                            alert('Сумма скидки: ' + discountSum);
                            purchaseSum -= discountSum;
                            totalSum = +totalSum + +purchaseSum;
                            $('#purchase_sum').val(purchaseSum);
                            $('#create_purchase').click(function () {
                                $.ajax({
                                    method : 'PUT',
                                    url    : 'http://charlie.rarus-crimea.ru/api/clients/',
                                    //dataType: 'json',
                                    data   : {
                                        id       : id,
                                        total_sum: totalSum
                                    },
                                    success: function (data) {
                                        $.ajax({
                                            method : 'POST',
                                            url    : 'http://charlie.rarus-crimea.ru/api/card_operations/',
                                            //dataType: 'json',
                                            data   : {
                                                name        : 'Скидка по карте',
                                                client_id   : id,
                                                new_value   : discountSum,
                                                user_api_key: apiKey,
                                            },
                                            success: function (data) {
                                                $.ajax({
                                                    method : 'POST',
                                                    url    : 'http://charlie.rarus-crimea.ru/api/card_operations/',
                                                    //dataType: 'json',
                                                    data   : {
                                                        name        : 'Регистрация оборота по карте',
                                                        client_id   : id,
                                                        new_value   : totalSum,
                                                        user_api_key: apiKey,
                                                    },
                                                    success: function (data) {
                                                        alert(data);
                                                    },
                                                    error  : function (data) {
                                                        alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                                    }
                                                });
                                            },
                                        });
                                    }
                                });
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
                            let operation = $('#hidden_params #operation').val();
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
                                                            name        : 'Начисление бонусов',
                                                            client_id   : id,
                                                            new_value   : newBonuses,
                                                            user_api_key: apiKey,
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
                                                            name        : 'Регистрация оборота по карте',
                                                            client_id   : id,
                                                            new_value   : totalSum,
                                                            user_api_key: apiKey,
                                                        },
                                                        /*success : function (data) {
                                                            alert(data);
                                                        },*/
                                                        error : function (data) {
                                                            alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                                        }
                                                    });

                                                },
                                                error  : function (data) {
                                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                                }
                                            });
                                        }
                                    },
                                    error   : function (data) {
                                        alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                    }
                                });
                            } else if ('subtract_bonuses' === operation) {
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
                                                name        : 'Списание бонусов',
                                                client_id   : id,
                                                new_value   : bonusesForSubtract,
                                                user_api_key: apiKey,
                                            },
                                            success: function (data) {
                                                alert(data);
                                            },
                                            error  : function (data) {
                                                alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                            }
                                        });
                                        $.ajax({
                                            method: 'POST',
                                            url   : 'http://charlie.rarus-crimea.ru/api/card_operations/',
                                            //dataType: 'json',
                                            data  : {
                                                name        : 'Регистрация оборота по карте',
                                                client_id   : id,
                                                new_value   : totalSum,
                                                user_api_key: apiKey,
                                            },
                                            /*success : function (data) {
                                                alert(data);
                                            },*/
                                            error : function (data) {
                                                alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                            }
                                        });

                                    },
                                    error  : function (data) {
                                        alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                    }
                                });
                            } else {

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
                                            name        : 'Изменение статуса карты',
                                            client_id   : id,
                                            old_value   : 'Активна',
                                            new_value   : 'Заблокирована',
                                            user_api_key: apiKey,
                                        },
                                        /*success : function (data) {
                                            alert(data);
                                        },*/
                                    });
                                },
                                error  : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
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
                                            name        : 'Изменение статуса карты',
                                            client_id   : id,
                                            old_value   : 'Заблокирована',
                                            new_value   : 'Активна',
                                            user_api_key: apiKey,
                                        },
                                        /*success : function (data) {
                                            alert(data);
                                        },*/
                                    });
                                },
                                error  : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
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
                                            name        : 'Изменение процента по карте',
                                            client_id   : id,
                                            old_value   : oldPercent,
                                            new_value   : newPercent,
                                            user_api_key: apiKey,
                                        },
                                        success: function (data) {
                                            alert(data);
                                        },
                                    });
                                },
                                error  : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                }
                            });
                        });

                        $('#subtracted_bonuses_sum').click(function () {
                            $.ajax({
                                url     : 'http://charlie.rarus-crimea.ru/api/card_operations/subtracted_bonuses_sum',
                                dataType: 'json',
                                success : function (data) {
                                    $('#subtracted_bonuses_res').val(data);
                                },
                                error   : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                }
                            });
                        });

                        $('#card_bonuses_sum').click(function () {
                            $.ajax({
                                url     : 'http://charlie.rarus-crimea.ru/api/card_operations/card_bonuses_sum',
                                dataType: 'json',
                                success : function (data) {
                                    $('#card_bonuses_res').val(data);
                                },
                                error   : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
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
                                },
                                error   : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                }
                            });
                        });

                        $('#cards_count').click(function () {
                            $.ajax({
                                url    : 'http://charlie.rarus-crimea.ru/api/clients/cards_count',
                                //dataType: 'json',
                                success: function (data) {
                                    $('#cards_count_res').val(data);
                                },
                                error  : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                }
                            });
                        });

                        $('#discount_sum').click(function () {
                            $.ajax({
                                url    : 'http://charlie.rarus-crimea.ru/api/card_operations/discount_sum',
                                //dataType: 'json',
                                success: function (data) {
                                    $('#discount_sum_res').val(data);
                                },
                                error  : function (data) {
                                    alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                                }
                            });
                        });
                    },
                    error      : function (data) {
                        alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
                    }
                });
            },
            error   : function (data) {
                alert(data.responseJSON.code + ' - ' + data.responseJSON.message);
            }
        });
    });


});