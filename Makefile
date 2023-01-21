.DEFAULT_GOAL := help
.PHONY: help

include .env
export

up:
	cd Docker && docker compose up -d

install:
	git config core.fileMode false
	sudo chmod -R 777 .
	make up
	composer update
	make db-config

db-config:
	mysql -h 127.0.0.1 -P $(DB_PORT) -w -u root -p < db_ecommerce.sql