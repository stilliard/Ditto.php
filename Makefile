
install:
	composer install --no-interaction

test:
	./vendor/bin/phpunit tests

server:
	php -S localhost:8008 example/index.php

server-url-param:
	php -S localhost:8008 example/via_url_param.php

