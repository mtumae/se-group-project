<?php
// This file assumes $db is already defined by admin_dashboard.php

try {
    // --- 1. GET ALL THE STATS ---
    $userCount = $db->fetch("SELECT COUNT(*) as count FROM users")[0]['count'];
    $listingCount = $db->fetch("SELECT COUNT(*) as count FROM items")[0]['count'];
    $totalValue = $db->fetch("SELECT SUM(price) as sum FROM items")[0]['sum'];

    // --- 2. Table Stat ---
    $itemsByitem_category = $db->fetch(
        "SELECT item_category, COUNT(*) as count 
         FROM items 
         WHERE item_category IS NOT NULL AND item_category != ''
         GROUP BY item_category"
    );

    // --- 3. Chart 1: Signups Over Time ---
    $signups = $db->fetch(
        "SELECT DATE(created_at) AS signup_date, COUNT(id) AS new_users
         FROM users
         WHERE created_at >= CURDATE() - INTERVAL 7 DAY
         GROUP BY signup_date
         ORDER BY signup_date ASC"
    );

    $signup_labels = [];
    $signup_data = [];
    foreach ($signups as $day) {
        $signup_labels[] = $day['signup_date'];
        $signup_data[] = $day['new_users'];
    }

    // --- 4. Chart 2: User Role Distribution ---
    $roles = $db->fetch(
        "SELECT r.role_name, COUNT(u.id) AS count
         FROM users u
         JOIN roles r ON u.role_id = r.id
         GROUP BY r.role_name"
    );

    $role_labels = [];
    $role_data = [];
    foreach ($roles as $role) {
        $role_labels[] = $role['role_name'];
        $role_data[] = $role['count'];
    }

} catch (Exception $e) {
    die("Database error loading reports: " . $e->getMessage());
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid">
    
    <button class="btn btn-primary mb-3" onclick="window.print()">
        Print / Save as PDF
    </button>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h3 class="card-title"><?= $userCount ?></h3>
                    <p class="card-text">Total Registered Users</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h3 class="card-title"><?= $listingCount ?></h3>
                    <p class="card-text">Total Active items</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h3 class="card-title">Ksh<?= number_format($totalValue ?? 0, 2) ?></h3>
                    <p class="card-text">Total Item Prices</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Signups (Last 7 Days)</div>
                <div class="card-body">
                    <canvas id="signupsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">User Role Distribution</div>
                <div class="card-body">
                    <canvas id="rolesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h4>items by item_category</h4>
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>item_category</th>
                        <th>Total items</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($itemsByitem_category)): ?>
                        <tr><td colspan="2">No categories found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($itemsByitem_category as $item_category): ?>
                            <tr>
                                <td><?= htmlspecialchars($item_category['item_category']) ?></td>
                                <td><?= htmlspecialchars($item_category['count']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .navbar, .btn, h1 {
        display: none !important;
    }
    .container, .container-fluid {
        width: 100% !important; margin: 0 !important; padding: 0 !important;
    }
    .card {
        border: 1px solid #ddd !important; break-inside: avoid; 
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // --- Pass PHP data to JavaScript ---
    const signupLabels = <?php echo json_encode($signup_labels); ?>;
    const signupData = <?php echo json_encode($signup_data); ?>;
    
    const roleLabels = <?php echo json_encode($role_labels); ?>;
    const roleData = <?php echo json_encode($role_data); ?>;

    // --- Render Signups Line Chart ---
    const ctxSignups = document.getElementById('signupsChart').getContext('2d');
    new Chart(ctxSignups, {
        type: 'line',
        data: {
            labels: signupLabels,
            datasets: [{
                label: 'New Users',
                data: signupData,
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // --- Render Roles Pie Chart ---
    const ctxRoles = document.getElementById('rolesChart').getContext('2d');
    new Chart(ctxRoles, {
        type: 'pie',
        data: {
            labels: roleLabels,
            datasets: [{
                label: 'User Roles',
                data: roleData,
                backgroundColor: [
                    'rgb(54, 162, 235)', 
                    'rgb(255, 205, 86)'  
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true
        }
    });

});
</script>