# Hours of Operation (HOO) ![HoO Logo](https://gitlab.lib.unc.edu/cappdev/hours-of-operation/raw/master/assets/images/hoo-20.png)
A Wordpress plugin to help you manage and display the hours of operation across a variety of locations and special events. Designed for libraries, Hoo is adaptable enough to work for any organization.

Major features in HOO include: 

- Customizable location profiles with fields for detailed descriptions, contact information, and geolocation coordinates.  

- Customizable event categories for color-coding and prioritizing different types of events, such as regular hours, seasonal hours, and holidays. 

- An attractive default display that includes a Google map view with position markers and a list of the current hours for each location. 

- A detailed view for each location that includes location information and an interactive monthly calendar. 

- Shortcodes for displaying Today and Weekly hours for each location anywhere in your Wordpress site, and another for showing the current status of all the locations at a glance. 

- An API shortcode that creates a consumable JSON view of all location hours so that you can create your own view of the data. 

- Simple permissions that allow only Admin users to create and edit locations and categories while Editor users are only able to create and edit hours events. 

- Uses standard ical and recurrence rules, allowing you to create repeating events, 24-hour events, and open hours that extend past midnight without cluttering the calendar view. 

HOO was created by the Core Applications Development team at the University of North Carolina at Chapel Hill Library. 

# How to Use HOO
To install HOO in an existing Wordpress instance, simply download the plugin file and unzip it in your plugins directory. Activate it and look for the Hours of Operation menu in your administrative sidebar to get started. 

## Setting up Locations and Events
You will need to create at least one Location and provide latitude and longitude information for it in order for the Google map display to work correctly. 

## Displaying Hours
When you are ready to display your hours, create a Wordpress page and add a HOO shortcode to it. For the main display with the Google map, use [hoo widget="full"]. See the Shortcodes submenu for other shortcode display options.

## Uninstalling
If you need to completely delete HOO, deactivate the plugin and select the Delete option in the plugins menu to remove all data and database tables from your installation.  

# Development

## Requirements
  * [PHP](http://php.net) >= 5.3.0
  * [Wordpress](http://wordpress.org) (of course) 

This plugin uses [Composer](http://getcomposer.org) for dependency management.
You will first need to install the dependencies with `make dependencies` or
`php composer.phar update`

## Installation
```bash
git clone git@gitlab.lib.unc.edu:cappdev/hours-of-operation.git
cd hours-of-operation
make dependencies
ln -s /<full-path-to-this-directory> /<full-path-to-your-wordpress-plugins-directory>
```
## Dependencies
HoO uses [Doctrine 2 ORM](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest)
and [Recurr](https://github.com/simshaun/recurr). I highly recommend getting familiar with them.

# License
Hours of Operation is licensed under the [GNU General Public License, version 2](https://www.gnu.org/licenses/gpl-2.0.html).


