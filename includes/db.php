<?php
/**
 * Configuración de conexión a la base de datos
 * Sistema: HYPE Sportswear E-commerce
 * Configurado para Laragon (localhost)
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'hype_shop');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Variable global para la conexión PDO
$pdo = null;

/**
 * Obtiene la conexión PDO a la base de datos
 * @return PDO Objeto de conexión PDO
 * @throws PDOException Si hay error en la conexión
 */
function getDB() {
    global $pdo;
    
    // Si ya existe una conexión, retornarla (patrón Singleton)
    if ($pdo !== null) {
        return $pdo;
    }
    
    try {
        // DSN (Data Source Name)
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        // Opciones de PDO
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,     // Lanzar excepciones en caso de error
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,           // Modo de fetch por defecto: array asociativo
            PDO::ATTR_EMULATE_PREPARES   => false,                       // Desactivar emulación de prepared statements
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET   // Establecer charset
        ];
        
        // Crear la conexión PDO
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        return $pdo;
        
    } catch (PDOException $e) {
        // En producción, no mostrar detalles del error
        // error_log($e->getMessage());
        // die("Error de conexión a la base de datos");
        
        // En desarrollo, mostrar el error completo
        die("Error de conexión: " . $e->getMessage());
    }
}

/**
 * Ejecuta una consulta SELECT y retorna todos los resultados
 * @param string $sql Query SQL
 * @param array $params Parámetros para prepared statement
 * @return array Resultados de la consulta
 */
function dbQuery($sql, $params = []) {
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error en query: " . $e->getMessage());
        return [];
    }
}

/**
 * Ejecuta una consulta SELECT y retorna un solo registro
 * @param string $sql Query SQL
 * @param array $params Parámetros para prepared statement
 * @return array|false Resultado de la consulta o false si no hay resultados
 */
function dbQueryOne($sql, $params = []) {
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error en query: " . $e->getMessage());
        return false;
    }
}

/**
 * Ejecuta una consulta de INSERT, UPDATE o DELETE
 * @param string $sql Query SQL
 * @param array $params Parámetros para prepared statement
 * @return bool|int ID del último registro insertado (INSERT) o número de filas afectadas
 */
function dbExecute($sql, $params = []) {
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $result = $stmt->execute($params);
        
        // Si es un INSERT, retornar el ID insertado
        if (stripos(trim($sql), 'INSERT') === 0) {
            return $db->lastInsertId();
        }
        
        // Para UPDATE/DELETE, retornar número de filas afectadas
        return $stmt->rowCount();
        
    } catch (PDOException $e) {
        error_log("Error en execute: " . $e->getMessage());
        return false;
    }
}

/**
 * Inicia una transacción
 */
function dbBeginTransaction() {
    $db = getDB();
    return $db->beginTransaction();
}

/**
 * Confirma una transacción
 */
function dbCommit() {
    $db = getDB();
    return $db->commit();
}

/**
 * Revierte una transacción
 */
function dbRollback() {
    $db = getDB();
    return $db->rollBack();
}

// Inicializar la conexión al incluir el archivo
// Esto asegura que la conexión esté lista para usar
getDB();
