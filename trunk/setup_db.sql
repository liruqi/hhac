CREATE DATABASE IF NOT EXISTS hhac;
USE hhac;
CREATE TABLE IF NOT EXISTS users(
    id INTEGER NOT NULL AUTO_INCREMENT,
    name VARCHAR(16) NOT NULL,
    password VARCHAR(16) NOT NULL,
    email VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS movies(
    id INTEGER NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    tags VARCHAR(255) NOT NULL,
    owner INTEGER NOT NULL,
    ctime TIMESTAMP NOT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS comments(
    id INTEGER NOT NULL AUTO_INCREMENT,
    owner INTEGER NOT NULL,
    content VARCHAR(255) NOT NULL,
    ctime TIMESTAMP NOT NULL,
    PRIMARY KEY (id)
);

CREATE USER hhac@'localhost' IDENTIFIED BY 'iamharmless';
GRANT ALL PRIVILEGES ON hhac.* TO hhac;
FLUSH PRIVILEGES;