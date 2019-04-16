<html>
<head>
    <style>
        form {
            float: left;
            margin: 10px;
        }
    </style>
    <script
            src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous">
    </script>
    <script src="scripts.js"></script>
</head>
<body>
<h3>Форма поиска клиента</h3>
<form action="" method="GET" id="find">
    <p>Введите номер карты клиента:</p>
    <input type="text" id="card_number" size="15">
    <input type="button" id="get_client" value="ОК">
    <p>% скидки:</p>
    <input type="text" id="discount" size="15"><br>
    <p>Количество бонусов:</p>
    <input type="text" id="bonus_balance" size="15"><br>
</form>
<h3>Форма добавления клиента</h3>
<form action="" method="POST" id="add">
    <p>Имя(*):</p>
    <input type="text" id="first_name"><br>
    <p>Отчество:</p>
    <input type="text" id="middle_name"><br>
    <p>Фамилия(*):</p>
    <input type="text" id="last_name"><br>
    <p>Дата рождения(*):</p>
    <input type="text" id="birthday" placeholder="YYYY-MM-DD"><br>
    <p>Телефон(*):</p>
    <input type="text" id="phone"><br>
    <p>Номер карты:</p>
    <input type="text" id="card_number"><br>
    <p>Размер скидки:</p>
    <input type="text" id="discount"><br>
    <input type="button" id="add_client" value="Добавить"><br>
</form>
<h3>Форма продажи товара</h3>
<form action="" method="POST" id="sale">
    <p>Сумма покупки:</p>
    <input type="text" id="sum">
    <p>Количество бонусов к списанию:</p>
    <input type="text" id="bonus_amount">
    <input type="button" id="subtract_bonuses" value="Списать бонусы"><br>
    <input type="button" id="create_purchase" value="Оформить покупку"><br>
</form>
</body>
</html>