.PHONY: up down scan build ssh ssh-db
.DEFAULT_GOAL := help

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "     up              Starts application containers and services."
	@echo "     down            Stops application containers and services."
	@echo "     build           Builds the application containers."
	@echo "     scan            Scans the local files"
	@echo "     ssh             SSH into the PHP service."
	@echo "     ssh-db          SSH into the DB service."

up:
	$(info 🔥 Make: Starting up.)
	@docker-compose up -d

down:
	$(info 💥 Make: Shutting down.)
	@docker-compose down

build:
	$(info 🏗  Make: Building environment images.)
	@docker-compose rm -vsf
	@docker-compose down -v --remove-orphans
	@docker-compose build

scan:
	$(info 📦 Make: Scanning files.)
	@docker-compose run --rm php php scan.php

ssh:
	$(info 💻 Make: SSH into PHP container.)
	@docker-compose exec php bash

ssh-db:
	$(info 💻 Make: SSH into DB container.)
	@docker-compose exec db psql -U postgres -d booksdb