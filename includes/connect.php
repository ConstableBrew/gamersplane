<?
	$dbHostname = getenv('MYSQL_HOST');
	$dbUsername = getenv('MYSQL_USERNAME');
	$dbPassword = getenv('MYSQL_PASSWORD');
	$dbName     = getenv('MYSQL_DATABASE');

	$mysql = new PDO("mysql:host=$dbHostname;dbname=$dbName", $dbUsername, $dbPassword);
	$mysql->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	$mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$mysql->query('SET time_zone="GMT"');

	$mongo = new MongoDB\Client(null, [], ['typeMap' => ['array' => 'array', 'document' => 'array', 'root' => 'array']]);
	$mongo = $mongo->gamersplane;

	class DB
	{
		public static $connections = [];

		public static function addConnection($label, $connection)
		{
			self::$connections[$label] = $connection;
		}

		public static function conn($label)
		{
			return self::$connections[$label];
		}
	}

	DB::addConnection('mysql', $mysql);
	DB::addConnection('mongo', $mongo);
?>
