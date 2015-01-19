
![Ditto.php](./docs/images/dittophp.png)

## About

*Ditto.php* is a php package to mimic a site by a given URL (mimics everything, all pages, images, css, js, etc.).

Used as a composer package, you can include into a file such as index.php and route all requests to it, or you can use it in a route in your framework.

It can be used as a web proxy, and can inject javascript to the page.
E.g. We've used it to proxy a site in an iframe, and inject javascript to select a value on the site and pass it back up the parent site.

It also injects some javascirpt at the top of the page for you to hijack all ajax requests so these are also procxies for you ;)

## Install
```bash
composer require stilliard/ditto.php dev-master
```

## Usage

See: [Example](./example/index.php)

(You can run this by cd'ing into this repo and running: `make server`)

### License

This project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

