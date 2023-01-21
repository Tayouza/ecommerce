.DEFAULT_GOAL := help
.PHONY: help

include .env
export

up:
	docker compose up -d

install:
	make up
	composer update
	git config core.fileMode false
	sudo chamod -R 777 .
