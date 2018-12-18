# CropSight

Daniel Reynolds<sup>1</sup>, Joshua Ball<sup>1</sup> and Ji Zhou<sup>1,2,*</sup>

<sup>1</sup>Earlham Institute, Norwich Research Park, Norwich UK  
<sup>2</sup>Nanjing Agricultural University, Nanjing, China

<sup>*</sup>Correspondence: ji.zhou@earlham.ac.uk


CropSight is a server system which runs on a network enabled web server.
To install CS, a functioning PHP and SQL server is required. Whilst CS is designed
to be easy to use and intuitive in design, allowing users with no technical background
to utilize the tools, installation requires an IT professional. The only pre-requisites
for installation are a PHP server supporting PHP5+ i.e. Apache and an SQL server
i.e. MySQL.

## Database Initialization

1.	A user must be created on the SQL server with the username ‘cropsight’ and a
    secure password which must be recorded. 
2.	A database named ‘cropsight’ must be created on the SQL server and read/write
    access given to the cropsight user
3.	In the supplied source code is the SQL file /database/database.sql, this file must be
    run on the cropsight database using either an SQL admin tool such as phpmyadmin or
    the SQL command: source /pathtosource/sql/database.sql. This will initialize all
	database tables.
4.	The default username to access the system is 'admin'. Leaving the password blank will prompt
	the user to set a password on first login.

## PHP Initialization

1.	Edit the file in the supplied source code /interface/database.php and /api/database.php
    to add the password created in Database Initialization Step 1 to the empty field $sql_password.
2.	Edit the file in the supplied source code /api/developer_key.php to add a unique developer
    key and value to the empty fields $developer_key and $developer_key_value. These form part
	of the api URL to verify devices.
3.	Copy all files in the php folder (not including the folder itself) to the base php
    folder of the webserver. For example, on a standard Linux Apache server this folder
	would be /var/www/html.
4.	Connect to the webserver using it’s IP or web address, successful installation will
    show the CropSight login screen. This can be logged in using the user account created
	in Database Initialization Step 4.
