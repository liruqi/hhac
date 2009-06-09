CREATE DATABASE IF NOT EXISTS vod;
USE vod;
CREATE TABLE IF NOT EXISTS flves (
    id INT NOT NULL,
    name VARCHAR(256) NOT NULL,
    path VARCHAR(256) NOT NULL,
    owner INT NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO flves VALUES(100001, 'How to get a girl', '/data/flv/howtogetagirl.flv', 1001);
INSERT INTO flves VALUES(100002, 'War Song', '/data/flv/war_song.flv', 1001);
