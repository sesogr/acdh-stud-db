<?php
/* Verbindung mit Datenbank*/

$dbhost = getenv( 'DB_HOST' );
$dbuser = getenv( 'DB_USER' );
$dbpassword = getenv( 'DB_PASSWORD' );
$dbname = getenv( 'DB_NAME' );
$dbname2 = getenv( 'DB_NAME_2' );
$dbschema = getenv( 'DB_SCHEMA' ); 
$dbschemawbmod = getenv( 'DB_SCHEMA_WURZBACH_MODEL' );
$dbport = getenv( 'DB_PORT' ); 

/* Variables from demos/edit/config.php */

$httpuseredit = getenv( 'HTTP_USER_EDIT' ); 
$httppassedit = getenv( 'HTTP_PASS_EDIT' );
$httprealmedit = getenv( 'HTTP_REALM_EDIT' );

/* Variables from humorist/edit/config.php */

$httpuserhumorist = getenv( 'HTTP_USER_HUMORIST' ); 
$httppasshumorist = getenv( 'HTTP_PASS_HUMORIST' );


// $link=mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
// $link2=mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname2);

/* Variables for schwechat/norm_ */
$dsn = "mysql:host=$dbhost;port=3306;dbname=$dbname;charset=UTF8";

/* Variables for studenten/search, show and form */
$dsndb2 = "mysql:host=$dbhost;port=3306;dbname=$dbname2;charset=UTF8";

?>