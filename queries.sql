DROP DATABASE IF EXISTS bonus_service;

CREATE DATABASE IF NOT EXISTS bonus_service DEFAULT CHARACTER SET utf8;
USE bonus_service;

DROP TABLE IF EXISTS user;

CREATE TABLE IF NOT EXISTS user
(
    api_key    CHAR(50)                      NOT NULL,
    first_name VARCHAR(30)                   NOT NULL,
    last_name  VARCHAR(30)                   NOT NULL,
    role       ENUM ('Оператор', 'Менеджер') NOT NULL,
    PRIMARY KEY (api_key)
);

DROP TABLE IF EXISTS loyalty_program;

CREATE TABLE IF NOT EXISTS loyalty_program
(
    id        TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name      VARCHAR(45)      NOT NULL,
    is_chosen BOOL             NOT NULL,
    PRIMARY KEY (id)
);

DROP TABLE IF EXISTS card_number;

CREATE TABLE IF NOT EXISTS card_number
(
    id        TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name      VARCHAR(45)      NOT NULL,
    is_chosen BOOL             NOT NULL,
    PRIMARY KEY (id)
);

DROP TABLE IF EXISTS client;

CREATE TABLE IF NOT EXISTS client
(
    id            SMALLINT UNSIGNED                 NOT NULL AUTO_INCREMENT,
    first_name    VARCHAR(45)                       NOT NULL,
    middle_name   VARCHAR(45)                       NULL,
    last_name     VARCHAR(45)                       NOT NULL,
    birthday      DATE                              NOT NULL,
    phone         BIGINT UNSIGNED                   NOT NULL,
    card_number   SMALLINT(5) UNSIGNED ZEROFILL     NULL UNIQUE,
    card_status   ENUM ('Активна', 'Заблокирована') NULL,
    bonus_balance DECIMAL(5, 2) UNSIGNED            NULL,
    discount      DECIMAL(3, 1) UNSIGNED            NULL,
    total_sum     DECIMAL(7, 2) UNSIGNED            NULL,
    PRIMARY KEY (id)
);

DROP TABLE IF EXISTS card_operation;

CREATE TABLE IF NOT EXISTS card_operation
(
    id           INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    name         ENUM (
        'Списание бонусов',
        'Начисление бонусов',
        'Изменение процента по карте',
        'Регистрация оборота по карте',
        'Изменение статуса карты',
        'Выпуск карты',
        'Скидка по карте')         NOT NULL,
    datetime     TIMESTAMP         NOT NULL,
    client_id    SMALLINT UNSIGNED NOT NULL,
    old_value    VARCHAR(30)       NULL,
    new_value    VARCHAR(30)       NULL,
    user_api_key CHAR(50)          NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_api_key)
        REFERENCES user (api_key)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (client_id)
        REFERENCES client (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);