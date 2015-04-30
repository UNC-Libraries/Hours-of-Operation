all: update-repo dependencies

update-repo:
	git pull origin master

dependencies:
	php composer.phar update --no-dev

.PHONY: all update-repo dependencies
