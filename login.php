<?php
require_once __DIR__ . '/db.php';

$error = '';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id_or_email = trim($_POST['user_id_or_email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($user_id_or_email) || empty($password)) {
        $error = 'Please enter both your User ID / Email and password.';
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? OR email = ?");
        $stmt->execute([$user_id_or_email, $user_id_or_email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];

            if ($user['is_admin']) {
                header("Location: admin.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $error = 'Invalid credentials. Please verify your User ID / Email and password.';
        }
    }
}

include __DIR__ . '/header.php';
?>

<section class="py-16 max-w-md mx-auto px-4 sm:px-6">
    <div class="glass-card p-8 rounded-3xl border border-slate-800 space-y-6">

        <div class="text-center space-y-2">
            <div class="w-12 h-12 mx-auto rounded-2xl gradient-accent text-slate-950 font-black flex items-center justify-center text-xl shadow-lg shadow-cyan-500/20">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h1 class="text-2xl font-black text-white">Member Portal Sign In</h1>
            <p class="text-xs text-slate-400">Access your 10-matrix downlines, wallet commissions, and product kit dispatches.</p>
        </div>

        <?php if ($error): ?>
            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-semibold flex items-center">
                <i class="fa-solid fa-triangle-exclamation text-lg mr-2.5"></i>
                <div><?= $error ?></div>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">User ID / Email Address</label>
                <input type="text" name="user_id_or_email" value="<?= htmlspecialchars($_POST['user_id_or_email'] ?? '') ?>" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition text-sm" placeholder="e.g. ORB00000 or admin@orbitaprotein.com">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition text-sm" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl text-slate-950 font-extrabold gradient-accent hover:brightness-110 transition shadow-lg shadow-cyan-500/20 text-sm">
                <i class="fa-solid fa-right-to-bracket mr-2"></i> Access Dashboard
            </button>
        </form>

        <!-- Admin Quick Hint -->
        <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 text-[11px] text-slate-400 space-y-1">
            <span class="text-cyan-400 font-bold block"><i class="fa-solid fa-key mr-1"></i> Default Root Admin Credentials:</span>
            <p>ID: <code class="text-white">ORB00000</code> | Pass: <code class="text-white">Admin@123</code></p>
        </div>

        <div class="text-center text-xs text-slate-400 pt-2 border-t border-slate-800">
            Don't have a distributor account? <a href="register.php" class="text-cyan-400 font-bold hover:underline">Register Kit (₹6,000)</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
