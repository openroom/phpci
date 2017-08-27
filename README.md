Example PHP project
===================

The purpose of this repository is to show how to use GitLab to do
Continuous Integration with a PHP project. It serves as a companion project for
<https://docs.gitlab.com/ce/ci/examples/php.html>.

In order to run this project just fork it on GitLab.com.
Every push will then trigger a new build on GitLab.

Source
------
This project was taken from: https://github.com/travis-ci-examples/php.

to run unit tests locally, 

* give your postgres user password of postgres
* create hello_world_test database 
* make postgres owner of the database 
* go to your project folder 
* run something like vendor/bin/phpunit --configuration phpunit_pgsqllocal.xml --coverage-text 

enjoy!
