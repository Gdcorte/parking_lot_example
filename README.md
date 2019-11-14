This project was conceived as a PHP training and has been implemented using only PHP and MySQL.
Therefore, no front-end was designed.

To run the application:

1-use a PHP virtual server
	php -S localhost:8000
	
2-set the correct database entries in the file database.php
    const DB_HOSTNAME = "hostname";
    const DB_USERNAME = "username";
    const DB_PASSWORD = "pass";
    const DB_DATABASE = 'db_name';
	
3- Use the functionalities provided by the classes on index.php