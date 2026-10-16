<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/header.php';

// Fetch quick statistics
$pdo = getDB();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(package_amount), 0) FROM orders")->fetchColumn();
$totalCommissions = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM wallet_transactions")->fetchColumn();
?>

<!-- Hero Section -->
<section class="relative pt-12 pb-20 md:pt-20 md:pb-32 overflow-hidden">
    <!-- Neon Background Glows -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-cyan-500/15 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-10 right-10 w-80 h-80 bg-blue-600/10 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

            <!-- Left Hero Content -->
            <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-bold tracking-widest uppercase">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    <span>10-Matrix BFS Forced Spillover Engine</span>
                </div>

                <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tight leading-none">
                    PREMIUM WHEY. <br>
                    <span class="gradient-text">EXPONENTIAL REWARDS.</span>
                </h1>

                <p class="text-lg text-slate-300 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    Empower your fitness and financial goals with Orbita Pure Whey Isolate. Backed by an automated company-wide 10-matrix BFS spillover network, earn direct ₹1,000 bonuses and multi-level passive matrix commissions.
                </p>

                <!-- Key Value Badges -->
                <div class="grid grid-cols-3 gap-4 pt-2 max-w-lg mx-auto lg:mx-0">
                    <div class="glass-card p-3 rounded-xl border border-slate-800 text-center">
                        <div class="text-xl font-bold text-cyan-400">₹6,000</div>
                        <div class="text-[11px] text-slate-400 font-medium">Starter Kit</div>
                    </div>
                    <div class="glass-card p-3 rounded-xl border border-slate-800 text-center">
                        <div class="text-xl font-bold text-emerald-400">₹1,000</div>
                        <div class="text-[11px] text-slate-400 font-medium">Direct Referral</div>
                    </div>
                    <div class="glass-card p-3 rounded-xl border border-slate-800 text-center">
                        <div class="text-xl font-bold text-cyan-300">₹200 / Head</div>
                        <div class="text-[11px] text-slate-400 font-medium">5-Level Spillover</div>
                    </div>
                </div>

                <!-- CTA Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                    <a href="register.php" class="w-full sm:w-auto px-8 py-4 rounded-xl text-base font-extrabold text-slate-950 gradient-accent hover:brightness-110 transition shadow-xl shadow-cyan-500/25 flex items-center justify-center">
                        <i class="fa-solid fa-rocket mr-2"></i> Register Distributor Kit (₹6,000)
                    </a>
                    <a href="business-plan.php" class="w-full sm:w-auto px-6 py-4 rounded-xl text-base font-semibold text-slate-200 glass-card hover:bg-slate-800 transition flex items-center justify-center border border-slate-700">
                        <i class="fa-solid fa-circle-info mr-2 text-cyan-400"></i> Explore Matrix Plan
                    </a>
                </div>
            </div>

            <!-- Right Hero Product Showcase -->
            <div class="lg:col-span-5 relative flex justify-center">
                <div class="glass-card glass-card-hover p-8 rounded-3xl border border-slate-700/60 max-w-md w-full relative">
                    <div class="absolute -top-4 -right-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 font-black text-xs px-4 py-1.5 rounded-full uppercase tracking-wider shadow-lg">
                        100% Whey Isolate
                    </div>

                    <!-- Protein Tub Mockup Graphics -->
                    <div class="w-full h-64 bg-slate-900/80 rounded-2xl flex items-center justify-center border border-slate-800 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/10 to-transparent"></div>
                        <div class="text-center p-6 space-y-3 z-10">
                            <div class="w-24 h-24 mx-auto rounded-full gradient-accent flex items-center justify-center shadow-2xl shadow-cyan-500/40 text-slate-950 text-4xl font-black">
                                <i class="fa-solid fa-jar"></i>
                            </div>
                            <h3 class="text-2xl font-black text-white tracking-wide">ORBITA PURE WHEY</h3>
                            <p class="text-xs text-cyan-400 font-semibold tracking-widest uppercase">27g Protein • 6.2g BCAA • 0g Sugar</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400">Includes in ₹6,000 Kit:</span>
                            <span class="text-emerald-400 font-bold">1kg Sealed Tub</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400">Flavor Choice:</span>
                            <span class="text-white font-medium">Rich Chocolate / Swiss Vanilla</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400">Network Position:</span>
                            <span class="text-cyan-400 font-bold">Instant 10-Matrix Placement</span>
                        </div>
                    </div>

                    <a href="register.php" class="block w-full text-center mt-6 py-3 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-400 font-bold border border-cyan-500/40 transition">
                        Order & Activate Business <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Live Network Stats Banner -->
<section class="py-10 border-y border-slate-800 bg-slate-950/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-card p-6 rounded-2xl flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-xl font-bold">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Active Distributors</p>
                    <p class="text-2xl font-extrabold text-white mt-1"><?= number_format($totalUsers) ?> <span class="text-xs text-slate-500 font-normal">/ 11,111</span></p>
                </div>
            </div>

            <div class="glass-card p-6 rounded-2xl flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl font-bold">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Platform Revenue Generated</p>
                    <p class="text-2xl font-extrabold text-white mt-1"><?= formatINR($totalRevenue) ?></p>
                </div>
            </div>

            <div class="glass-card p-6 rounded-2xl flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-purple-400 text-xl font-bold">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Total Commissions Paid</p>
                    <p class="text-2xl font-extrabold text-cyan-400 mt-1"><?= formatINR($totalCommissions) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How 10-Matrix Spillover Works Section -->
<section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
        <h2 class="text-xs font-bold text-cyan-400 uppercase tracking-widest">Architected For Fair Distribution</h2>
        <h3 class="text-3xl sm:text-4xl font-black text-white">How Strict BFS Auto-Spillover Works</h3>
        <p class="text-slate-400 text-base">
            Unlike standard binary MLM structures, our 10-matrix Breadth-First Search algorithm fills downlines left-to-right across every tier, ensuring every distributor benefits from team expansion.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Step 1 -->
        <div class="glass-card glass-card-hover p-8 rounded-2xl border border-slate-800 space-y-4 relative">
            <div class="w-10 h-10 rounded-lg bg-cyan-500/20 text-cyan-400 font-black flex items-center justify-center text-lg">1</div>
            <h4 class="text-xl font-bold text-white">Purchase Kit & Join</h4>
            <p class="text-sm text-slate-400 leading-relaxed">
                Register for ₹6,000 to receive 1kg of premium Orbita Protein Powder tub dispatched directly to your address.
            </p>
        </div>

        <!-- Step 2 -->
        <div class="glass-card glass-card-hover p-8 rounded-2xl border border-slate-800 space-y-4 relative">
            <div class="w-10 h-10 rounded-lg bg-cyan-500/20 text-cyan-400 font-black flex items-center justify-center text-lg">2</div>
            <h4 class="text-xl font-bold text-white">Automatic BFS Placement</h4>
            <p class="text-sm text-slate-400 leading-relaxed">
                The global PHP engine traverses the tree top-to-bottom, placing you under the first available parent node with fewer than 10 children.
            </p>
        </div>

        <!-- Step 3 -->
        <div class="glass-card glass-card-hover p-8 rounded-2xl border border-slate-800 space-y-4 relative">
            <div class="w-10 h-10 rounded-lg bg-cyan-500/20 text-cyan-400 font-black flex items-center justify-center text-lg">3</div>
            <h4 class="text-xl font-bold text-white">Instant Commission Distribution</h4>
            <p class="text-sm text-slate-400 leading-relaxed">
                Your direct sponsor earns ₹1,000 immediately, and ₹200 per head cascades up 5 levels to your upline parents automatically.
            </p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
