#docker
d-stop:
	cd docker && docker stop $$(docker ps -q -a)

d-up:
	cd docker && docker-compose up -d

d-start: d-stop d-up d-php

.PHONY: d-php
d-php:
	cd docker && docker-compose exec base_php bash
##############################
