<?php
require_once __DIR__ . '/../server/core/Database.php';

try {
    $db = new Database();
    echo "✅ Database connection successful\n";
} catch(Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>