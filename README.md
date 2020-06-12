# simple-admin
Simple Admin for the Phalcon PHP Framework

![github-small](https://sitchi.dev/sa4.png)
![github-small](https://sitchi.dev/sa3.png)

## Get Started

### Requirements

* PHP >= 7.3
* [Apache][1] Web Server with [mod_rewrite][2] enabled or [Nginx][3] Web Server
* Phalcon >= 4.0.5 [Phalcon Framework release][4] extension enabled
* [MariaDB][5] >= 10.3

### Installation

##### Install via composer create-project

```bash
composer create-project sitchi/simple-admin
```

##### Install via git clone

```bash
git clone https://github.com/sitchi/simple-admin

composer install
```

After the installation

1. Edit `app/config/config.php` file with your DB connection information
2. Run DB migrations `vendor/bin/phalcon-migrations run`
3. Write permissions of the cache, logs directory `sudo chmod -R 0777 cache/ logs/`

## License

The Simple Admin is under the MIT License, you can view the license [here](https://github.com/sitchi/simple-admin/blob/master/LICENSE).

[1]: http://httpd.apache.org/
[2]: http://httpd.apache.org/docs/current/mod/mod_rewrite.html
[3]: http://nginx.org/
[4]: https://github.com/phalcon/cphalcon/releases
[5]: https://mariadb.org/