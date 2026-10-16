<?php
require_once __DIR__ . '/db.php';
requireLogin();

$user = getCurrentUser();
$pdo = getDB();

// Fetch Order Fulfillment Details
$stmtOrder = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmtOrder->execute([$user['user_id']]);
$order = $stmtOrder->fetch();

// Fetch Wallet Transactions
$stmtTx = $pdo->prepare("SELECT * FROM wallet_transactions WHERE user_id = ? ORDER BY id DESC");
$stmtTx->execute([$user['user_id']]);
$transactions = $stmtTx->fetchAll();

// Fetch Direct Referrals
$stmtDirect = $pdo->prepare("SELECT * FROM users WHERE sponsor_id = ? ORDER BY id DESC");
$stmtDirect->execute([$user['user_id']]);
$directReferrals = $stmtDirect->fetchAll();

// Fetch Direct Matrix Children (Placed directly under this user as parent)
$stmtChildren = $pdo->prepare("SELECT * FROM users WHERE parent_id = ? ORDER BY id ASC");
$stmtChildren->execute([$user['user_id']]);
$children = $stmtChildren->fetchAll();

// Helper to recursively fetch downline tree structure up to 3 levels deep for visual display
function getDownlineTree($parentId, $maxDepth = 3, $currentDepth = 1) {
    if ($currentDepth > $maxDepth) return [];
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT user_id, name, level, children_count, created_at FROM users WHERE parent_id = ? ORDER BY id ASC");
    $stmt->execute([$parentId]);
    $nodes = $stmt->fetchAll();

    foreach ($nodes as &$node) {
        $node['children'] = getDownlineTree($node['user_id'], $maxDepth, $currentDepth + 1);
    }
    return $nodes;
}

$matrixTree = getDownlineTree($user['user_id']);

$registeredAlert = isset($_GET['registered']) && $_GET['registered'] == 1;

include __DIR__ . '/header.php';
?>

<section class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <?php if ($registeredAlert): ?>
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-semibold flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-circle-check text-2xl"></i>
                <div>
                    <div class="font-bold">Welcome to Orbita Nutrition Direct!</div>
                    <div class="text-xs text-emerald-300">Your ₹6,000 kit order is active and your position is locked in the 10-matrix tree.</div>
                </div>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>
    <?php endif; ?>

    <!-- Member Welcome & Profile Quick Bar -->
    <div class="glass-card p-6 rounded-3xl border border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-2xl gradient-accent flex items-center justify-center text-slate-950 font-black text-2xl shadow-lg shadow-cyan-500/20">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl font-black text-white"><?= htmlspecialchars($user['name']) ?></h1>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-bold">
                        ID: <?= htmlspecialchars($user['user_id']) ?>
                    </span>
                    <?php if ($user['is_admin']): ?>
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold">
                            Root Admin
                        </span>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    <i class="fa-solid fa-envelope mr-1 text-slate-500"></i><?= htmlspecialchars($user['email']) ?> |
                    <i class="fa-solid fa-phone mr-1 text-slate-500"></i><?= htmlspecialchars($user['phone']) ?>
                </p>
            </div>
        </div>

        <!-- Direct Referral Link Copy -->
        <div class="w-full md:w-auto bg-slate-900/90 p-3 rounded-2xl border border-slate-800 flex items-center justify-between space-x-3">
            <div class="text-xs overflow-hidden">
                <span class="text-[10px] text-slate-500 uppercase font-bold block">Your Direct Referral Link</span>
                <code class="text-cyan-400 font-mono text-xs truncate block" id="ref-link">
                    http://<?= $_SERVER['HTTP_HOST'] ?>/register.php?ref=<?= urlencode($user['user_id']) ?>
                </code>
            </div>
            <button onclick="navigator.clipboard.writeText(document.getElementById('ref-link').innerText.trim()); alert('Referral link copied to clipboard!');" class="px-3 py-2 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-400 font-bold text-xs border border-cyan-500/40 transition flex-shrink-0">
                <i class="fa-solid fa-copy mr-1"></i> Copy
            </button>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-2">
            <div class="flex justify-between items-center text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Wallet Balance</span>
                <i class="fa-solid fa-wallet text-cyan-400 text-base"></i>
            </div>
            <div class="text-3xl font-black text-emerald-400"><?= formatINR($user['wallet_balance']) ?></div>
            <p class="text-[11px] text-slate-500">Instant direct & matrix earnings</p>
        </div>

        <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-2">
            <div class="flex justify-between items-center text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Matrix Placement Level</span>
                <i class="fa-solid fa-layer-group text-cyan-400 text-base"></i>
            </div>
            <div class="text-3xl font-black text-white">Level <?= $user['level'] ?></div>
            <p class="text-[11px] text-slate-500">Parent ID: <span class="text-cyan-400 font-bold"><?= $user['parent_id'] ?? 'Root' ?></span></p>
        </div>

        <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-2">
            <div class="flex justify-between items-center text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Direct Children</span>
                <i class="fa-solid fa-children text-cyan-400 text-base"></i>
            </div>
            <div class="text-3xl font-black text-cyan-400"><?= $user['children_count'] ?> <span class="text-xs text-slate-500 font-normal">/ 10</span></div>
            <p class="text-[11px] text-slate-500">Direct matrix capacity used</p>
        </div>

        <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-2">
            <div class="flex justify-between items-center text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Direct Referrals</span>
                <i class="fa-solid fa-users text-cyan-400 text-base"></i>
            </div>
            <div class="text-3xl font-black text-white"><?= count($directReferrals) ?></div>
            <p class="text-[11px] text-slate-500">Sponsor ID: <span class="text-cyan-400 font-bold"><?= $user['sponsor_id'] ?? 'None' ?></span></p>
        </div>

    </div>

    <!-- Protein Package Fulfillment Card -->
    <div class="glass-card p-6 sm:p-8 rounded-3xl border border-slate-800 space-y-6">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 border-b border-slate-800 pb-4">
            <div>
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fa-solid fa-box-open text-cyan-400 mr-2.5"></i> Protein Powder Kit Fulfillment Status
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">₹6,000 Starter Kit Dispatch & Tracking Information</p>
            </div>
            <?php if ($order): ?>
                <?php
                $statusColor = 'bg-amber-500/10 text-amber-400 border-amber-500/30';
                if ($order['fulfillment_status'] === 'Dispatched') $statusColor = 'bg-blue-500/10 text-blue-400 border-blue-500/30';
                if ($order['fulfillment_status'] === 'Delivered') $statusColor = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30';
                ?>
                <span class="px-4 py-1.5 rounded-full border text-xs font-extrabold uppercase tracking-wider <?= $statusColor ?>">
                    <i class="fa-solid fa-truck-ramp-box mr-1.5"></i> Status: <?= htmlspecialchars($order['fulfillment_status']) ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if ($order): ?>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-sm">
                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <span class="text-xs text-slate-500 uppercase font-bold block mb-1">Order Number</span>
                    <span class="text-white font-mono font-bold"><?= htmlspecialchars($order['order_number']) ?></span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <span class="text-xs text-slate-500 uppercase font-bold block mb-1">Flavor Choice</span>
                    <span class="text-cyan-400 font-bold"><?= htmlspecialchars($order['flavor']) ?> (1kg)</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <span class="text-xs text-slate-500 uppercase font-bold block mb-1">Tracking Number</span>
                    <span class="text-white font-mono"><?= $order['tracking_number'] ? htmlspecialchars($order['tracking_number']) : 'Pending Warehouse Dispatch' ?></span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <span class="text-xs text-slate-500 uppercase font-bold block mb-1">Order Date</span>
                    <span class="text-slate-300"><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 text-xs text-slate-300">
                <span class="font-bold text-slate-400 block mb-1">Shipping Address:</span>
                <p><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
            </div>
        <?php else: ?>
            <div class="p-6 text-center text-slate-400 text-sm">
                No kit order found for this account.
            </div>
        <?php endif; ?>
    </div>

    <!-- 10-Matrix Visual Tree Structure -->
    <div class="glass-card p-6 sm:p-8 rounded-3xl border border-slate-800 space-y-6">
        <div class="border-b border-slate-800 pb-4 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fa-solid fa-sitemap text-cyan-400 mr-2.5"></i> Interactive 10-Matrix Tree View
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Visual representation of your direct downline network (Max 10 per node)</p>
            </div>
            <span class="text-xs text-cyan-400 font-bold bg-cyan-500/10 px-3 py-1 rounded-full border border-cyan-500/20">
                Auto BFS Spillover
            </span>
        </div>

        <!-- Matrix Tree Container -->
        <div class="bg-slate-950/80 p-6 rounded-2xl border border-slate-800/90 overflow-x-auto space-y-4">

            <!-- Root User Node -->
            <div class="flex justify-center">
                <div class="p-4 rounded-2xl bg-gradient-to-r from-cyan-500/20 to-blue-500/20 border border-cyan-500/40 text-center min-w-[200px] shadow-lg">
                    <div class="text-xs text-cyan-400 font-bold uppercase tracking-wider mb-1">YOUR POSITION</div>
                    <div class="text-lg font-black text-white"><?= htmlspecialchars($user['user_id']) ?></div>
                    <div class="text-xs text-slate-300"><?= htmlspecialchars($user['name']) ?></div>
                    <div class="mt-2 text-[10px] text-cyan-300 bg-cyan-950/60 py-0.5 px-2 rounded-full inline-block">
                        Level <?= $user['level'] ?> • <?= $user['children_count'] ?>/10 Children
                    </div>
                </div>
            </div>

            <div class="text-center text-slate-600 font-bold text-lg"><i class="fa-solid fa-arrow-down"></i></div>

            <!-- Direct Children Tier -->
            <?php if (empty($matrixTree)): ?>
                <div class="text-center py-6 text-slate-500 text-xs">
                    No downline members placed under your matrix node yet. New registrations via BFS spillover will appear here!
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <?php foreach ($matrixTree as $c): ?>
                        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-center space-y-2 hover:border-cyan-500/40 transition">
                            <div class="text-xs font-extrabold text-cyan-400 font-mono"><?= htmlspecialchars($c['user_id']) ?></div>
                            <div class="text-xs font-bold text-white truncate" title="<?= htmlspecialchars($c['name']) ?>"><?= htmlspecialchars($c['name']) ?></div>
                            <div class="text-[10px] text-slate-400">Level <?= $c['level'] ?> • Children: <?= $c['children_count'] ?></div>

                            <!-- Subchildren indicator -->
                            <?php if (!empty($c['children'])): ?>
                                <div class="pt-2 border-t border-slate-800/80 text-[10px] text-cyan-300 space-y-1">
                                    <div class="font-bold text-slate-500 uppercase">Sub-Downlines:</div>
                                    <?php foreach ($c['children'] as $sc): ?>
                                        <div class="bg-slate-950 px-2 py-1 rounded text-slate-300 flex justify-between">
                                            <span><?= $sc['user_id'] ?></span>
                                            <span class="text-slate-500"><?= $sc['children_count'] ?> child</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Ledger & Referral List Tabs -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Wallet Transaction Ledger -->
        <div class="lg:col-span-7 glass-card p-6 sm:p-8 rounded-3xl border border-slate-800 space-y-4">
            <h3 class="text-xl font-bold text-white flex items-center border-b border-slate-800 pb-3">
                <i class="fa-solid fa-receipt text-cyan-400 mr-2.5"></i> Wallet Commission Ledger
            </h3>

            <?php if (empty($transactions)): ?>
                <div class="p-8 text-center text-slate-500 text-xs">
                    No commission transactions recorded yet.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                                <th class="py-3 px-2">Type</th>
                                <th class="py-3 px-2">From User</th>
                                <th class="py-3 px-2">Description</th>
                                <th class="py-3 px-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            <?php foreach ($transactions as $tx): ?>
                                <tr class="hover:bg-slate-800/30">
                                    <td class="py-3 px-2">
                                        <?php if ($tx['type'] === 'DIRECT_REFERRAL'): ?>
                                            <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-bold border border-emerald-500/20">Direct ₹1,000</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-400 font-bold border border-cyan-500/20">Matrix ₹200</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-2 font-mono text-cyan-400 font-bold"><?= htmlspecialchars($tx['from_user_id']) ?></td>
                                    <td class="py-3 px-2 text-slate-400 max-w-xs truncate"><?= htmlspecialchars($tx['description']) ?></td>
                                    <td class="py-3 px-2 text-right font-bold text-emerald-400">+<?= formatINR($tx['amount']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Direct Referrals List -->
        <div class="lg:col-span-5 glass-card p-6 sm:p-8 rounded-3xl border border-slate-800 space-y-4">
            <h3 class="text-xl font-bold text-white flex items-center border-b border-slate-800 pb-3">
                <i class="fa-solid fa-users text-cyan-400 mr-2.5"></i> Direct Sponsored Members
            </h3>

            <?php if (empty($directReferrals)): ?>
                <div class="p-8 text-center text-slate-500 text-xs">
                    You haven't directly sponsored any members yet. Share your link to earn ₹1,000 per referral!
                </div>
            <?php else: ?>
                <div class="space-y-3 max-h-[350px] overflow-y-auto pr-2">
                    <?php foreach ($directReferrals as $dr): ?>
                        <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800 flex justify-between items-center text-xs">
                            <div>
                                <div class="font-bold text-white"><?= htmlspecialchars($dr['name']) ?></div>
                                <div class="text-[11px] text-cyan-400 font-mono"><?= htmlspecialchars($dr['user_id']) ?> • Level <?= $dr['level'] ?></div>
                            </div>
                            <div class="text-right text-[11px] text-slate-500">
                                <div>Joined</div>
                                <div class="text-slate-300 font-medium"><?= date('M d, Y', strtotime($dr['created_at'])) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

</section>

<?php include __DIR__ . '/footer.php'; ?>
