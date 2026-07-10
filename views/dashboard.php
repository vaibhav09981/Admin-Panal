<h1>Dashboard</h1>
<p class="muted">Overview of your data. The same numbers are returned by <code>GET /api/stats</code>.</p>

<div class="stats">
    <div class="card stat">
        <span class="stat-label">Total Users</span>
        <span class="stat-value" id="stat-users"><?= $stats['total_users'] ?></span>
    </div>
    <div class="card stat">
        <span class="stat-label">Total Items</span>
        <span class="stat-value" id="stat-items"><?= $stats['total_items'] ?></span>
    </div>
    <div class="card stat">
        <span class="stat-label">Total Stock</span>
        <span class="stat-value" id="stat-stock"><?= $stats['total_stock'] ?></span>
    </div>
    <div class="card stat">
        <span class="stat-label">Inventory Value</span>
        <span class="stat-value" id="stat-value">₹<?= number_format($stats['inventory_value'], 2) ?></span>
    </div>
</div>
    <div class="card stat">
        <span class="stat-label">Total Items</span>
        <span class="stat-value"><?= $stats['total_items'] ?></span>
    </div>
    <div class="card stat">
        <span class="stat-label">Total Stock</span>
        <span class="stat-value"><?= $stats['total_stock'] ?></span>
    </div>
    <div class="card stat">
        <span class="stat-label">Inventory Value</span>
        <span class="stat-value">Rs. <?= number_format($stats['inventory_value'], 2) ?></span>
    </div>
</div>

<div class="card">
    <h3>Available API endpoints</h3>
    <ul class="endpoints">
        <li>POST /api/login</li>
        <li>POST /api/logout</li>
        <li>GET /api/me</li>
        <li>GET /api/stats</li>
        <li>GET · POST /api/users</li>
        <li>GET · PUT · DELETE /api/users/{id}</li>
        <li>GET · POST /api/items</li>
        <li>GET · PUT · DELETE /api/items/{id}</li>
    </ul>
</div>
