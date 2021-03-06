DROP DATABASE IF EXISTS bonus_service;

CREATE DATABASE IF NOT EXISTS bonus_service DEFAULT CHARACTER SET utf8;
USE bonus_service;

DROP TABLE IF EXISTS user;

CREATE TABLE IF NOT EXISTS user
(
    api_key    CHAR(32)                      NOT NULL UNIQUE,
    first_name VARCHAR(30)                   NOT NULL,
    last_name  VARCHAR(30)                   NOT NULL,
    role       ENUM ('Кассир', 'Менеджер') NOT NULL,
    PRIMARY KEY (api_key)
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
    card_status   ENUM ('Активна', 'Заблокирована') NULL DEFAULT 'Активна',
    bonus_balance DECIMAL(5, 2) UNSIGNED            NULL DEFAULT 0,
    discount      DECIMAL(3, 1) UNSIGNED            NULL,
    total_sum     DECIMAL(7, 2) UNSIGNED            NULL DEFAULT 0,
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
    datetime     TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    client_id    SMALLINT UNSIGNED NOT NULL,
    old_value    VARCHAR(30)       NULL     DEFAULT '',
    new_value    VARCHAR(30)       NULL     DEFAULT '',
    user_api_key CHAR(32)          NOT NULL,
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

DROP TABLE IF EXISTS holiday;

CREATE TABLE IF NOT EXISTS holiday
(
    id   SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(45)       NOT NULL,
    date DATE              NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO user(api_key, first_name, last_name, role) VALUE ('7828a24b71c7d916ba97b267730ab57a', 'Валерия', 'Чернякова', 1);
INSERT INTO user(api_key, first_name, last_name, role) VALUE ('9828a24b71c7d916ba97b267730ab57a', 'Сергей', 'Поляков', 2);

INSERT INTO holiday (name, date)
VALUES ('Новый год', '2019-01-01');
INSERT INTO holiday (name, date)
VALUES ('Рождество', '2019-01-07');
INSERT INTO holiday (name, date)
VALUES ('День защитника отечества', '2019-02-23');
INSERT INTO holiday (name, date)
VALUES ('Международный женский день', '2019-03-08');
INSERT INTO holiday (name, date)
VALUES ('День солидарности трудящихся', '2019-05-01');
INSERT INTO holiday (name, date)
VALUES ('День победы', '2019-05-09');
INSERT INTO holiday (name, date)
VALUES ('День независимости России', '2019-06-12');
INSERT INTO holiday (name, date)
VALUES ('День народного единства', '2019-11-04');