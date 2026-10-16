<?php
require_once __DIR__ . '/db.php';
requireLogin();
requireAdmin();

$pdo = getDB();
$msg = '';
$error = '';

// Handle Order Dispatch Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_order') {
    $order_id = intval($_POST['order_id'] ?? 0);
    $status = sanitize($_POST['fulfillment_status'] ?? 'Processing');
    $tracking = sanitize($_POST['tracking_number'] ?? '');

    if ($order_id > 0) {
        $stmtUpdate = $pdo->prepare("UPDATE orders SET fulfillment_status = ?, tracking_number = ? WHERE id = ?");
        $stmtUpdate->execute([$status, $tracking, $order_id]);
        $msg = "Order #$order_id updated successfully!";
    }
}

// Fetch Global Statistics
$totalMembers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(package_amount), 0) FROM orders")->fetchColumn();
$totalCommissions = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM wallet_transactions")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE fulfillment_status = 'Processing'")->fetchColumn();

// Fetch All Distributors with Order Information
$query = "
    SELECT
        u.*,
        o.id AS order_id,
        o.order_number,
        o.flavor,
        o.package_amount,
        o.shipping_address,
        o.fulfillment_status,
        o.tracking_number
    FROM users u
    LEFT JOIN orders o ON u.user_id = o.user_id
    ORDER BY u.id ASC
";
$distributors = $pdo->query($query)->fetchAll();

include __DIR__ . '/header.php';
?>

<section class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Admin Title Header -->
    <div class="glass-card p-6 rounded-3xl border border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <span class="w-3 h-3 rounded-full bg-cyan-400 animate-pulse"></span>
                <h1 class="text-2xl font-black text-white">ORBITA PLATFORM ADMIN CONTROL PANEL</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Centralized monitoring for 10-Matrix BFS Tree, Protein Kit Dispatches, and Wallet Commissions.
            </p>
        </div>
        <a href="dashboard.php" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-200 bg-slate-800 hover:bg-slate-700 transition">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Back to Member Dashboard
        </a>
    </div>

    <?php if ($msg): ?>
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-semibold text-sm flex items-center">
            <i class="fa-solid fa-circle-check text-xl mr-3"></i>
            <div><?= $msg ?></div>
        </div>
    <?php endif; ?>

    <!-- Platform Stats Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-2">
            <div class="flex justify-between items-center text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Total Revenue</span>
                <i class="fa-solid fa-indian-rupee-sign text-emerald-400 text-lg"></i>
            </div>
            <div class="text-3xl font-black text-emerald-400"><?= formatINR($totalRevenue) ?></div>
            <p class="text-[11px] text-slate-500">Gross kit sales (₹6,000/kit)</p>
        </div>

        <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-2">
            <div class="flex justify-between items-center text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Total Network Members</span>
                <i class="fa-solid fa-users text-cyan-400 text-lg"></i>
            </div>
            <div class="text-3xl font-black text-white"><?= number_format($totalMembers) ?> <span class="text-xs text-slate-500 font-normal">/ 11,111</span></div>
            <p class="text-[11px] text-slate-500">Global matrix occupancy</p>
        </div>

        <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-2">
            <div class="flex justify-between items-center text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Total Commissions Paid</span>
                <i class="fa-solid fa-wallet text-purple-400 text-lg"></i>
            </div>
            <div class="text-3xl font-black text-purple-400"><?= formatINR($totalCommissions) ?></div>
            <p class="text-[11px] text-slate-500">Direct ₹1k + Matrix ₹200</p>
        </div>

        <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-2">
            <div class="flex justify-between items-center text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Pending Dispatches</span>
                <i class="fa-solid fa-truck-fast text-amber-400 text-lg"></i>
            </div>
            <div class="text-3xl font-black text-amber-400"><?= $pendingOrders ?> Kits</div>
            <p class="text-[11px] text-slate-500">Processing in central warehouse</p>
        </div>

    </div>

    <!-- Searchable Distributors Table -->
    <div class="glass-card p-6 sm:p-8 rounded-3xl border border-slate-800 space-y-6">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-4">
            <div>
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fa-solid fa-users-gear text-cyan-400 mr-2.5"></i> Global Distributor Registry
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Search and manage tree placements, earnings, and fulfillment</p>
            </div>

            <!-- Search Input -->
            <div class="w-full sm:w-72 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                <input type="text" id="table-search" onkeyup="filterTable()" placeholder="Search Name, User ID, Email..." class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white text-xs focus:outline-none focus:border-cyan-500 transition">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse" id="distributors-table">
                <thead>
                    <tr class="bg-slate-900/90 text-cyan-400 font-bold uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3 px-3">User ID</th>
                        <th class="py-3 px-3">Distributor Name</th>
                        <th class="py-3 px-3">Level / Parent</th>
                        <th class="py-3 px-3">Sponsor ID</th>
                        <th class="py-3 px-3 text-center">Children</th>
                        <th class="py-3 px-3 text-right">Wallet Balance</th>
                        <th class="py-3 px-3">Kit Order / Status</th>
                        <th class="py-3 px-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    <?php foreach ($distributors as $d): ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 px-3 font-mono font-bold text-cyan-400">
                                <?= htmlspecialchars($d['user_id']) ?>
                                <?php if ($d['is_admin']): ?>
                                    <span class="block text-[9px] text-amber-400 uppercase font-sans">Admin</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="font-bold text-white"><?= htmlspecialchars($d['name']) ?></div>
                                <div class="text-[10px] text-slate-500"><?= htmlspecialchars($d['email']) ?></div>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-white font-bold">L-<?= $d['level'] ?></span>
                                <span class="text-slate-400 ml-1">Parent: <?= $d['parent_id'] ?? 'Root' ?></span>
                            </td>
                            <td class="py-3.5 px-3 font-mono text-slate-300">
                                <?= $d['sponsor_id'] ?? '-' ?>
                            </td>
                            <td class="py-3.5 px-3 text-center font-bold text-cyan-300">
                                <?= $d['children_count'] ?> / 10
                            </td>
                            <td class="py-3.5 px-3 text-right font-bold text-emerald-400">
                                <?= formatINR($d['wallet_balance']) ?>
                            </td>
                            <td class="py-3.5 px-3">
                                <?php if ($d['order_number']): ?>
                                    <div class="font-mono text-slate-200"><?= htmlspecialchars($d['order_number']) ?></div>
                                    <div class="text-[10px] text-cyan-400"><?= htmlspecialchars($d['flavor']) ?></div>
                                    <?php
                                    $stClass = 'text-amber-400';
                                    if ($d['fulfillment_status'] === 'Dispatched') $stClass = 'text-blue-400';
                                    if ($d['fulfillment_status'] === 'Delivered') $stClass = 'text-emerald-400';
                                    ?>
                                    <span class="text-[10px] font-bold uppercase <?= $stClass ?>"><?= $d['fulfillment_status'] ?></span>
                                <?php else: ?>
                                    <span class="text-slate-500">Root Node</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <?php if ($d['order_id']): ?>
                                    <button onclick='openFulfillmentModal(<?= json_encode($d) ?>)' class="px-2.5 py-1 rounded bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 font-bold border border-cyan-500/30 transition text-[11px]">
                                        <i class="fa-solid fa-truck-fast mr-1"></i> Fulfill
                                    </button>
                                <?php else: ?>
                                    <span class="text-slate-600">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</section>

<!-- Fulfillment Modal -->
<div id="fulfillment-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="glass-card p-6 rounded-3xl border border-slate-700 max-w-md w-full space-y-4">
        <div class="flex justify-between items-center border-b border-slate-800 pb-3">
            <h4 class="text-lg font-bold text-white"><i class="fa-solid fa-box text-cyan-400 mr-2"></i> Update Kit Fulfillment</h4>
            <button onclick="closeFulfillmentModal()" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form action="admin.php" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="update_order">
            <input type="hidden" name="order_id" id="modal-order-id">

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Distributor & Order</label>
                <div class="text-sm font-bold text-white" id="modal-distributor-info"></div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Fulfillment Status</label>
                <select name="fulfillment_status" id="modal-status" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm focus:outline-none focus:border-cyan-500">
                    <option value="Processing">Processing</option>
                    <option value="Dispatched">Dispatched</option>
                    <option value="Delivered">Delivered</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Courier Tracking Number</label>
                <input type="text" name="tracking_number" id="modal-tracking" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm focus:outline-none focus:border-cyan-500" placeholder="e.g. AWB-98234123">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Shipping Address</label>
                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300" id="modal-address"></div>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl text-slate-950 font-black gradient-accent hover:brightness-110 transition text-sm">
                Save & Update Order
            </button>
        </form>
    </div>
</div>

<script>
    function filterTable() {
        const input = document.getElementById("table-search").value.toUpperCase();
        const table = document.getElementById("distributors-table");
        const tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) {
            let textContent = tr[i].textContent || tr[i].innerText;
            if (textContent.toUpperCase().indexOf(input) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }

    function openFulfillmentModal(distributor) {
        document.getElementById('modal-order-id').value = distributor.order_id;
        document.getElementById('modal-distributor-info').innerText = distributor.name + " (" + distributor.user_id + ") - " + distributor.order_number;
        document.getElementById('modal-status').value = distributor.fulfillment_status;
        document.getElementById('modal-tracking').value = distributor.tracking_number || '';
        document.getElementById('modal-address').innerText = distributor.shipping_address;
        document.getElementById('fulfillment-modal').classList.remove('hidden');
    }

    function closeFulfillmentModal() {
        document.getElementById('fulfillment-modal').classList.add('hidden');
    }
</script>

<?php include __DIR__ . '/footer.php'; ?>
