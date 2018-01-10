[Задание](Тестовое задание на вакансию PHP-программист.pdf) не до конца ясно, необходимо обсуждение.

Для запуска необходимо 
 - PHP >= 5.3
 - PostgreSQL server

### installing

`git clone https://github.com/METALmasterKS/b2broker-test.git api.b2broker.local`

`cd api.b2broker.local`

`wget https://getcomposer.org/installer && php installer && rm installer`

`php composer.phar install`

execute schema.sql on your PGSQL server

### using

for example our url is - http://api.b2broker.local

check it

`curl -d "serial=123456789012345&time=2018-01-11 00:33:10" -X POST http://api.b2broker.local`

set options

`curl -d "serial=123456789012345&time=2018-01-11 00:34:47&connect_freq=15&firmware=1.02a" -X POST http://api.b2broker.local`

set options and options sets

`curl -d "serial=123456789012345&time=2018-01-11 00:34:47&connect_freq=25&firmware=1.01a&sets[connect_freq]=25&sets[some_par]=some_string&sets[status]=lost" -X POST http://api.b2broker.local`

set only options sets

`curl -d "serial=123456789012345&time=2018-01-11 00:34:47&sets[connect_freq]=25&sets[some_par]=buzzy" -X POST http://api.b2broker.local`