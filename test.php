<html>
<head></head>
<body>
<form action="get.php">
    <input type="submit">GET
</form>
<form action="post.php">
    <input type="submit">POST
</form>
<form action="put.php" method="POST">
    Имя: <input type="text" name="first_name"><br>
    Отчество: <input type="text" name="middle_name"><br>
    Фамилия: <input type="text" name="last_name"><br>
    Телефон: <input type="text" name="phone"><br>
    Дата рождения: <input type="date" name="birthday"><br>
    Номер карты: <input type="number" name="card_number"><br>
    <input type="submit" name="add_client" value="Добавить">
</form>
<form action="delete.php">
    <input type="submit">DELETE
</form>
</body>
</html>