open-equipment-scheduler
========================
@author: Ernie Cleveland <eclevela@umd.edu>

Based upon phpScheduleIt Version 1.1.2 by Nick Korbel et. al., http://phplabit.sourceforge.net

Open Equipment Scheduler is a php web application that manages the scheduling and 
billing of equipment for use in laboratory settings.  The tool allows management 
of users, labs, equipment, billing accounts, and reservations.  

Requirements
PHP 5+
PEAR::DB (included with most versions of PHP)
- PEAR::DB supported database. Compatible databases can be found here. 
- End-users need IE5+ or Netscape 6.x+ (Netscape 4.7 or earlier is NOT supported)

Installation
@TODO: SQL install scripts need to be updated
1) Verify that the PEAR DB package, a PEAR supported database (view currently supported databases) and PHP version 4.2.0 or greater are installed and properly configured on the destination server. If not, download and install at least these versions.

2) Copy all of the files provided into a desired web directory. Remember this directory because we will need it in the next two steps.

3) Edit your config/config.php file to be sure it is correct for your web server set up. Most importantly, make sure that the 'weburi', 'dbType', 'dbUser', 'dbPass' and 'dbName' settings are correct.

4) Follow the proper set of instructions below for your type of setup. If you are installing on a local machine, the automatic installation is advised.

5) After the installation is complete, you must register a user with the email address set for 'adminEmail' in config.php. This will be your administrative user.
