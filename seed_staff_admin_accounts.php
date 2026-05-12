<?php
$pdo = new PDO('mysql:host=localhost;dbname=glamhub_online_store;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$accounts = [
    ['GlamHub Admin', 'admin@glamhub.com', 'admin', 'ADM001', 'Management', 'Admin@123'],
    ['GlamHub Staff 1', 'staff1@glamhub.com', 'staff', 'STF001', 'Sales', 'Staff@123'],
    ['GlamHub Staff 2', 'staff2@glamhub.com', 'staff', 'STF002', 'Customer Support', 'Staff@123'],
    ['GlamHub Staff 3', 'staff3@glamhub.com', 'staff', 'STF003', 'Inventory', 'Staff@123'],
    ['GlamHub Staff 4', 'staff4@glamhub.com', 'staff', 'STF004', 'Beauty Advisor', 'Staff@123'],
    ['GlamHub Staff 5', 'staff5@glamhub.com', 'staff', 'STF005', 'Order Fulfilment', 'Staff@123'],
];

$stmt = $pdo->prepare(
    'INSERT INTO users (username, full_name, email, role, staff_admin_id, department, password)
     VALUES (?, ?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        username = VALUES(username),
        full_name = VALUES(full_name),
        role = VALUES(role),
        department = VALUES(department),
        password = VALUES(password)'
);

foreach ($accounts as $account) {
    [$name, $email, $role, $officialId, $department, $password] = $account;
    $stmt->execute([$officialId, $name, $email, $role, $officialId, $department, password_hash($password, PASSWORD_DEFAULT)]);
}

echo "Official staff/admin accounts created.\n";
