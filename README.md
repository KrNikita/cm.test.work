# cm.test.work
Requirements:
- Mysql (need use Legacy Authentication method bacause PHP PDO not compatible with new Authetnication methon in Mysql above 5.x)
- PHP (Tested on 7.1)

Database setting can be changed in conf.php file

To test run PHP builtin server:
  PHP -S 0.0.0.0:3000 -t app
Then run composer:
  composer install
Then open url http://localhost:3000


