CREATE DATABASE IF NOT EXISTS api_blog;

USE api_blog; 

CREATE TABLE Users (
id int(255) not null auto_increment, 
name varchar(50) not null,
lastName varchar(200) not null, 
userName varchar(50) not null, 
email varchar(255),
password varchar (255) not null, 
description text, 
createAt datetime,
updateAt datetime,
rememberToken varchar(255),
role varchar(20),
image varchar(255),
Primary key(id)
)ENGINE=InnoDB;

CREATE TABLE Categories (
id int(255) not null auto_increment, 
name varchar(100) not null,
createAt datetime,
updateAt datetime,
Primary key(id)
)ENGINE=InnoDB;

CREATE TABLE Posts (
id int(255) not null auto_increment, 
title varchar(50) not null,
content text, 
image varchar(255),
createAt datetime,
updateAt datetime,
fk_idcategories int(255) not null,
fk_idusers int(255) not null,
Primary key(id),
foreign key (fk_idcategories) references Categories(id),
foreign key (fk_idusers) references Users(id)
)ENGINE=InnoDB;