Deploy
======

A simple command line deploy application for git based projects.

Authors and contributors
========================

[Gustavo Straube](http://gustavostraube.wordpress.com) (Creator, Developer)

Requirements
============

* git
* A MySQL database

Installation
============

* Clone the project from [GitHub](https://github.com/straube/deploy).
* Run `composer install`. Go to [Composer's website](http://getcomposer.org) to more information about it.
* Rename `config.json.dist` to `config.json`.
* Open up `config.json` and configure your installation and at least one deploy config, following the example given in the file.
* Make sure that `deploy` script is executable running: `chmod a+x deploy`.

Usage
=====

After installation, you can run `./deploy` to see all commands available.
