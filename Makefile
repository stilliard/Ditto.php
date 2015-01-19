
install:
	composer install --no-interaction --dev

test:
	phpunit tests

server:
	php -S localhost:8008 -t example example/index.php

