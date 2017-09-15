
install:
	composer install --no-interaction

test:
	phpunit tests

server:
	php -S localhost:8008 -t example example/index.php

server-url-param:
	php -S localhost:8008 -t example example/via_url_param.php

