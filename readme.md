#Lifesize Control App

This is an application designed to replace [Lifesize Control][].

[Lifesize Control]: http://www.lifesize.com/en/products/video-conferencing-infrastructure/management-software

##Getting Started

- Execute the system/structure.sql file against your sql database
- copy system/config.default.php to config.php and edit settings


##Updating

After you've gotten your application setup and added some devices, you can run the updater with ```php updaters/updater.php```.  You can run as many as you would like although 1 is probably fine until you have enough devices to cause it to delay.  Also, you will want to run ```php updaters/interval.php``` on a half hour interval.  I suggest cron or equivalent.  interval.php *MUST* be run before updater.php will do anything.