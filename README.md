# Hours of Operation ![HoO Logo](https://gitlab.lib.unc.edu/cappdev/hours-of-operation/raw/master/assets/images/hoo-20.png)
A Wordpress plugin to help you manage the Hours of Operation for you business or institution.
    
## Hacking

### Requirements
  * [PHP](http://php.net) >= 5.3.0
  * [Wordpress](http://wordpress.org) (of course) 

This plugin uses [Composer](http://getcomposer.org) for dependency management.
You will first need to install the dependencies with `make dependencies` or
`php composer.phar update`

### Installation
```bash
git clone git@gitlab.lib.unc.edu:cappdev/hours-of-operation.git
cd hours-of-operation
make dependencies
ln -s /<full-path-to-this-directory> /<full-path-to-your-wordpress-plugins-directory>
```


### Dependencies
HoO uses [Doctrine 2 ORM](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest)
and [Recurr](https://github.com/simshaun/recurr). I highly recommend getting familiar with them.

# License
Hours of Operation is licensed under the [GNU General Public License, version 2](https://www.gnu.org/licenses/gpl-2.0.html).

