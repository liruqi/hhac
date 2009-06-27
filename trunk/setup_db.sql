-- setup_db.sql
-- Setup a mysql database for HHAC.

-- Create Schemas.
CREATE DATABASE IF NOT EXISTS hhac;
USE hhac;
CREATE TABLE IF NOT EXISTS users(
    id INTEGER NOT NULL AUTO_INCREMENT,
    name VARCHAR(16) NOT NULL,
    password VARCHAR(16) NOT NULL,
    email VARCHAR(255) NOT NULL,
    ctime TIMESTAMP NOT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS videos(
    id INTEGER NOT NULL AUTO_INCREMENT,
	path VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    tags VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    owner INTEGER NOT NULL,
    ctime TIMESTAMP NOT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS comments(
    id INTEGER NOT NULL AUTO_INCREMENT,
    owner INTEGER NOT NULL,
    movie INTEGER NOT NULL,
    content VARCHAR(255) NOT NULL,
    ctime TIMESTAMP NOT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS sessions(
    id INTEGER NOT NULL AUTO_INCREMENT,
    sessid VARCHAR(255) NOT NULL,
    user_id INTEGER NOT NULL,
    address VARCHAR(255) NOT NULL,
    ctime TIMESTAMP NOT NULL,
    PRIMARY KEY (id)
);
-- Create Mysql User.
-- Error occurs when the very user exists.
CREATE USER hhac@'localhost' IDENTIFIED BY 'iamharmless';
GRANT ALL PRIVILEGES ON hhac.* TO hhac;
FLUSH PRIVILEGES;
