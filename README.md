# Synskuel
##### Getting started :
include the file
````php
require 'synskuel.php';
````
Connect to the database
````php
Synskuel::connect(host, database, user, password [, options(array)]);
````
````php
$options = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM];
Synskuel::connect('127.0.0.1', 'database', 'root', 'superpass', $options);
````
##### Query something
````php
Synskuel::query(query [, params(array), success(callback), error(callback)];
````
SELECT example :
````php
// Callbacks are optionals
Synskuel::query("SELECT * FROM table WHERE id=?", [3], function($res){
    print_r($res->fetchAll());
}, function($err){
    print_r($err);
});
````
INSERT example :
````php
// Callbacks are optionals
Synskuel::query("INSERT INTO table (name, lastname) VALUES(?,?)", ['John', 'Doe']);
````
UPDATE/DETE example :
````php
// Callbacks are optionals
Synskuel:: query("UPDATE table SET name=? WHERE id=?", ['Jane', 1]):
Synskuel:: query("DELETE FROM table WHERE id=?", [1]):
````
COUNT example :
````php
$number = Synskuel::query("SELECT COUNT(*) FROM table");
echo $number;
// OR
Synskuel::query("SELECT COUNT(*) FROM table ", [], function($number){
    echo $number;
});
````
CHECK if a record exists :
````php
Synskuel::exists("SELECT COUNT(*) FROM table WHERE name=? ", ['Jane'], function(){
    echo " Jane is in the database !";
}, function() {
    echo "Jane is not in the database !";
});
````
##### Print all errors
````php
Synskuel::errors();
````
##### Log errors to a file
````php
// Default file : synskuel.log
Synskuel::logs();
// Custom file :
Synskuel::logs("yourfile.ext);
````
