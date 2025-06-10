<?php
include "db.php";

try {
    echo "<h2>Setting up Database</h2>";
    
    // Read and execute create_tables.sql
    echo "<h3>Creating Tables:</h3>";
    $sql = file_get_contents('create_tables.sql');
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $db->exec($statement);
            echo "✅ Executed: " . substr($statement, 0, 50) . "...<br>";
        }
    }
    
    // Verify tables were created
    $tables = [
        'kullanicilar',
        'ogretmenler',
        'rezervasyonlar',
        'mesajlar',
        'duyurular',
        'gorusme_saatleri'
    ];
    
    echo "<h3>Verifying Tables:</h3>";
    foreach ($tables as $table) {
        try {
            $result = $db->query("DESCRIBE $table");
            echo "✅ Table '$table' exists<br>";
        } catch (PDOException $e) {
            echo "❌ Error with table '$table': " . $e->getMessage() . "<br>";
        }
    }
    
    // Insert sample data
    echo "<h3>Inserting Sample Data:</h3>";
    include "insert_sample_data.php";
    
    echo "<h3>Setup Complete!</h3>";
    echo "<p>You can now:</p>";
    echo "<ul>";
    echo "<li><a href='login.php'>Go to Login Page</a></li>";
    echo "<li><a href='admin_panel_login.php'>Go to Admin Login</a></li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
}
?> 