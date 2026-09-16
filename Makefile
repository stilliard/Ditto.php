
install:
	composer install --no-interaction

test:
	./vendor/bin/phpunit tests

PORT?=8008
server:
	php -S localhost:$(PORT) example/index.php

server-url-param:
	php -S localhost:$(PORT) example/via_url_param.php

