<?php
class Database
{
	private static $host = "localhost";
	private static $db_name = "crud";
	private static $username = "root";
	private static $password = "";
	private static $conn;

	public static function getConnection()
	{
		self::$conn = null;

		try {
			self::$conn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$db_name, self::$username, self::$password);
		} catch (PDOException $exception) {
			echo "Connectio error: " . $exception->getMessage();
		}
		return self::$conn;
	}
	public static function disconnect()
	{
		self::$conn = null;
	}
}
