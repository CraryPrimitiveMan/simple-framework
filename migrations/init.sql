/*创建新用户*/
CREATE USER jun@localhost IDENTIFIED BY 'jun';

/*用户授权 授权jun用户拥有sf数据库的所有权限*/
GRANT ALL PRIVILEGES ON sf.* TO jun@'%' IDENTIFIED BY 'jun';

/*刷新授权*/
FLUSH PRIVILEGES;

/*创建数据库*/
CREATE DATABASE IF NOT EXISTS `sf`;

/*选择数据库*/
USE `sf`;

/*创建表*/
CREATE TABLE IF NOT EXISTS `user` (
    id INT(20) NOT NULL AUTO_INCREMENT,
    name VARCHAR(50),
    age INT(11),
    PRIMARY KEY(id)
);

/*插入测试数据*/
INSERT INTO `user` (name, age) VALUES('harry', 20), ('tony', 23), ('tom', 24);