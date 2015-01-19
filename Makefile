
install:
	composer install --no-interaction --dev

test:
	phpunit .

server:
	php -S localhost:8008 -t example example/index.php

