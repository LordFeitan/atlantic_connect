<?php
class Conexion {
    public static function conectar() {
        $host = "50.31.177.150";
        $dbname = "tecnober_atlantic_db";
        $username = "tecnober_user_atlantic";
        $password = "Ys4[uc(3e[&%";

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }
}
