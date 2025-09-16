<?php
/**
 * API Database - Gestionnaire SQLite intégré
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    echo json_encode(['success' => false, 'error' => 'Action manquante']);
    exit;
}

$action = $input['action'];
$dbPath = __DIR__ . '/../core/db/app.db';

// Créer le dossier db s'il n'existe pas
$dbDir = dirname($dbPath);
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    switch ($action) {
        case 'getTables':
            $result = getTables($pdo);
            break;
            
        case 'executeQuery':
            $result = executeQuery($pdo, $input['query'] ?? '');
            break;
            
        case 'createTable':
            $result = createTable($pdo, $input['name'] ?? '', $input['columns'] ?? []);
            break;
            
        case 'exportDatabase':
            $result = exportDatabase($pdo);
            break;
            
        case 'importSQL':
            $result = importSQL($pdo, $input['sql'] ?? '');
            break;
            
        default:
            $result = ['success' => false, 'error' => "Action inconnue : $action"];
    }
} catch (Exception $e) {
    $result = ['success' => false, 'error' => 'Erreur base de données : ' . $e->getMessage()];
}

echo json_encode($result);

// Fonctions utilitaires

function getTables($pdo) {
    try {
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
        $tables = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tableName = $row['name'];
            
            // Compter les lignes
            $countStmt = $pdo->query("SELECT COUNT(*) as count FROM `$tableName`");
            $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            $tables[] = [
                'name' => $tableName,
                'count' => $count
            ];
        }
        
        // Informations sur la base
        $info = [
            'file' => 'app.db',
            'size' => file_exists(__DIR__ . '/../core/db/app.db') ? filesize(__DIR__ . '/../core/db/app.db') : 0,
            'tables_count' => count($tables)
        ];
        
        return [
            'success' => true,
            'data' => [
                'tables' => $tables,
                'info' => $info
            ]
        ];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function executeQuery($pdo, $query) {
    if (empty($query)) {
        return ['success' => false, 'error' => 'Requête vide'];
    }
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        
        // Si c'est une requête SELECT
        if (stripos(trim($query), 'SELECT') === 0) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $columns = [];
            
            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
            }
            
            return [
                'success' => true,
                'data' => [
                    'rows' => $rows,
                    'columns' => $columns,
                    'count' => count($rows)
                ]
            ];
        } else {
            // Pour INSERT, UPDATE, DELETE
            $rowCount = $stmt->rowCount();
            return [
                'success' => true,
                'data' => [
                    'affected_rows' => $rowCount,
                    'message' => "Requête exécutée avec succès. $rowCount ligne(s) affectée(s)."
                ]
            ];
        }
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function createTable($pdo, $name, $columns) {
    if (empty($name) || empty($columns)) {
        return ['success' => false, 'error' => 'Nom de table ou colonnes manquants'];
    }
    
    try {
        $columnDefs = [];
        foreach ($columns as $column) {
            $columnDefs[] = "`{$column['name']}` {$column['type']}";
        }
        
        $sql = "CREATE TABLE `$name` (" . implode(', ', $columnDefs) . ")";
        $pdo->exec($sql);
        
        return ['success' => true, 'message' => "Table '$name' créée avec succès"];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function exportDatabase($pdo) {
    try {
        $sql = '';
        
        // Exporter la structure et les données
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            // Structure de la table
            $createStmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='$table'")->fetch(PDO::FETCH_ASSOC);
            $sql .= $createStmt['sql'] . ";\n\n";
            
            // Données de la table
            $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $values = array_map(function($value) {
                    return is_null($value) ? 'NULL' : "'" . str_replace("'", "''", $value) . "'";
                }, array_values($row));
                
                $sql .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
            }
            $sql .= "\n";
        }
        
        return [
            'success' => true,
            'data' => [
                'sql' => $sql,
                'filename' => 'database_export_' . date('Y-m-d_H-i-s') . '.sql'
            ]
        ];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function importSQL($pdo, $sql) {
    if (empty($sql)) {
        return ['success' => false, 'error' => 'SQL vide'];
    }
    
    try {
        // Diviser en requêtes individuelles
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        $executed = 0;
        
        foreach ($queries as $query) {
            if (!empty($query)) {
                $pdo->exec($query);
                $executed++;
            }
        }
        
        return [
            'success' => true,
            'message' => "$executed requête(s) exécutée(s) avec succès"
        ];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
?>