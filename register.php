<?php
require_once __DIR__ . '/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $flavor = sanitize($_POST['flavor'] ?? 'Chocolate');
    $address = sanitize($_POST['address'] ?? '');
    $sponsor_id = strtoupper(trim(sanitize($_POST['sponsor_id'] ?? '')));

    if (empty($sponsor_id)) {
        $sponsor_id = 'ORB00000';
    }

    if (empty($name) || !$email || empty($phone) || empty($password) || empty($address)) {
        $error = 'Please fill in all required fields with valid details.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        $pdo = getDB();

        // Check if email already exists
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmtCheck->execute([$email]);
        if ($stmtCheck->fetch()) {
            $error = 'An account with this email address is already registered.';
        } else {
            // Verify sponsor exists
            $stmtSponsor = $pdo->prepare("SELECT user_id FROM users WHERE user_id = ?");
            $stmtSponsor->execute([$sponsor_id]);
            $sponsorUser = $stmtSponsor->fetch();

            if (!$sponsorUser) {
                $error = 'Invalid Direct Sponsor ID provided. Please check or leave as ORB00000.';
            } else {
                // START TRANSACTION FOR BFS PLACEMENT & COMMISSIONS
                try {
                    $pdo->beginTransaction();

                    // Perform Strict BFS to find placement parent
                    // Queue starts at root ORB00000
                    // Breadth-First Search traverses level by level, left-to-right (by id ASC)
                    $placementParent = null;

                    // Fetch all non-full parent candidates sorted by level ASC, id ASC
                    // Max children per parent = 10, max level for parent = 3 (so child gets level <= 4)
                    $stmtBfs = $pdo->query("SELECT user_id, level, children_count FROM users WHERE level < 4 AND children_count < 10 ORDER BY level ASC, id ASC FOR UPDATE");
                    $candidateParents = $stmtBfs->fetchAll();

                    if (empty($candidateParents)) {
                        $pdo->rollBack();
                        $error = 'Maximum global platform capacity (11,111 nodes) reached across 5 matrix levels.';
                    } else {
                        // The top candidate in BFS order is the first available parent
                        $placementParent = $candidateParents[0];
                        $parentId = $placementParent['user_id'];
                        $newLevel = $placementParent['level'] + 1;

                        // Generate unique user ID (e.g. ORB00001)
                        $newUserId = generateNextUserId();
                        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                        // 1. Insert new distributor
                        $stmtInsertUser = $pdo->prepare("INSERT INTO users (user_id, name, email, phone, password, sponsor_id, parent_id, level, children_count, wallet_balance, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0.00, 0)");
                        $stmtInsertUser->execute([
                            $newUserId,
                            $name,
                            $email,
                            $phone,
                            $passwordHash,
                            $sponsor_id,
                            $parentId,
                            $newLevel
                        ]);

                        // 2. Increment children count of placement parent
                        $stmtUpdateParent = $pdo->prepare("UPDATE users SET children_count = children_count + 1 WHERE user_id = ?");
                        $stmtUpdateParent->execute([$parentId]);

                        // 3. Create protein kit order (₹6,000)
                        $orderNum = 'ORD-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                        $stmtOrder = $pdo->prepare("INSERT INTO orders (order_number, user_id, flavor, package_amount, shipping_address, fulfillment_status) VALUES (?, ?, ?, 6000.00, ?, 'Processing')");
                        $stmtOrder->execute([$orderNum, $newUserId, $flavor, $address]);

                        // 4. Direct Referral Commission: ₹1,000 to sponsor
                        $stmtDirectCredit = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + 1000.00 WHERE user_id = ?");
                        $stmtDirectCredit->execute([$sponsor_id]);

                        $stmtDirectTx = $pdo->prepare("INSERT INTO wallet_transactions (user_id, from_user_id, type, amount, description) VALUES (?, ?, 'DIRECT_REFERRAL', 1000.00, ?)");
                        $stmtDirectTx->execute([$sponsor_id, $newUserId, "Direct referral bonus for introducing $newUserId ($name)"]);

                        // 5. Matrix Level Commissions: ₹200 up the parent chain (up to 5 levels)
                        $currentParentId = $parentId;
                        $depth = 0;
                        while ($currentParentId !== null && $depth < 5) {
                            // Credit parent wallet
                            $stmtMatrixCredit = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + 200.00 WHERE user_id = ?");
                            $stmtMatrixCredit->execute([$currentParentId]);

                            $stmtMatrixTx = $pdo->prepare("INSERT INTO wallet_transactions (user_id, from_user_id, type, amount, description) VALUES (?, ?, 'MATRIX_COMMISSION', 200.00, ?)");
                            $stmtMatrixTx->execute([$currentParentId, $newUserId, "Level commission from placement of $newUserId at level $newLevel"]);

                            // Move to next parent up
                            $stmtGetNextParent = $pdo->prepare("SELECT parent_id FROM users WHERE user_id = ?");
                            $stmtGetNextParent->execute([$currentParentId]);
                            $parentRow = $stmtGetNextParent->fetch();
                            $currentParentId = $parentRow ? $parentRow['parent_id'] : null;
                            $depth++;
                        }

                        // COMMIT TRANSACTION
                        $pdo->commit();

                        $_SESSION['user_id'] = $newUserId;
                        $_SESSION['user_name'] = $name;
                        $_SESSION['is_admin'] = 0;

                        header("Location: dashboard.php?registered=1");
                        exit;
                    }
                } catch (Exception $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $error = 'Registration failed due to a database error: ' . $e->getMessage();
                }
            }
        }
    }
}

// Get sponsor ID from query parameter if present (e.g. register.php?ref=ORB00001)
$refSponsor = sanitize($_GET['ref'] ?? 'ORB00000');

include __DIR__ . '/header.php';
?>

<section class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="glass-card p-8 sm:p-12 rounded-3xl border border-slate-800 space-y-8">

        <div class="text-center space-y-2">
            <span class="px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-bold uppercase tracking-widest">
                Instant Business Activation
            </span>
            <h1 class="text-3xl sm:text-4xl font-black text-white">Distributor Kit Registration</h1>
            <p class="text-sm text-slate-400 max-w-lg mx-auto">
                Includes 1kg Protein Powder Tub (₹6,000) & Instant Position in the 10-Matrix Spillover Tree.
            </p>
        </div>

        <?php if ($error): ?>
            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-semibold flex items-center">
                <i class="fa-solid fa-triangle-exclamation text-xl mr-3"></i>
                <div><?= $error ?></div>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-6">

            <div class="bg-slate-900/60 p-6 rounded-2xl border border-slate-800/80 space-y-4">
                <h3 class="text-sm font-bold text-cyan-400 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-user-gear mr-2"></i> 1. Sponsor & Account Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Direct Sponsor ID *</label>
                        <input type="text" name="sponsor_id" value="<?= htmlspecialchars($refSponsor) ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-cyan-400 font-bold focus:outline-none focus:border-cyan-500 transition text-sm uppercase">
                        <span class="text-[11px] text-slate-500 mt-1 block">Default: ORB00000 (Root Admin)</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Full Name *</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition text-sm" placeholder="e.g. Vikram Singh">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Email Address *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition text-sm" placeholder="e.g. vikram@example.com">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Phone Number *</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition text-sm" placeholder="+91 98765 43210">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Set Account Password *</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition text-sm" placeholder="Minimum 6 characters">
                </div>
            </div>

            <!-- Protein Kit Selection & Shipping -->
            <div class="bg-slate-900/60 p-6 rounded-2xl border border-slate-800/80 space-y-4">
                <h3 class="text-sm font-bold text-cyan-400 uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-box-open mr-2"></i> 2. Protein Powder Kit & Shipping Address
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Select Flavor Choice</label>
                        <select name="flavor" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition text-sm font-medium">
                            <option value="Rich Chocolate">Rich Chocolate (1kg Tub)</option>
                            <option value="Swiss Vanilla">Swiss Vanilla (1kg Tub)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kit Package Amount</label>
                        <div class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-emerald-400 font-extrabold text-sm flex justify-between items-center">
                            <span>₹6,000.00 INR</span>
                            <span class="text-xs text-slate-500 font-normal">All Taxes & Shipping Included</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Delivery Shipping Address *</label>
                    <textarea name="address" rows="3" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition text-sm" placeholder="Street, Flat/House No, Landmark, City, State, Pincode"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Summary & Terms -->
            <div class="p-4 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-xs text-slate-300 space-y-2">
                <div class="font-bold text-cyan-400 flex items-center">
                    <i class="fa-solid fa-shield-halved mr-1.5"></i> Automatic BFS Placement Policy
                </div>
                <p>
                    By clicking Register, your ₹6,000 order will be generated and processed. Our PHP 10-matrix BFS queue will instantly position you under the next available parent in the company tree. Direct sponsor receives ₹1,000 and ₹200 level bonus is distributed up to 5 tiers automatically.
                </p>
            </div>

            <button type="submit" class="w-full py-4 rounded-xl text-slate-950 font-black text-base gradient-accent hover:brightness-110 transition shadow-xl shadow-cyan-500/25">
                <i class="fa-solid fa-check-circle mr-2"></i> Complete Registration & Activate Kit (₹6,000)
            </button>
        </form>

        <div class="text-center text-xs text-slate-400">
            Already registered? <a href="login.php" class="text-cyan-400 font-bold hover:underline">Sign in to Member Portal</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
