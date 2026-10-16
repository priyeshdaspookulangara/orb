<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';
$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORBITA NUTRITION | 10-Matrix Direct Selling Platform</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: {
                            800: '#0f172a',
                            900: '#0b0f19',
                            950: '#050811',
                        },
                        cyan: {
                            400: '#22d3ee',
                            500: '#06b6d4',
                            600: '#0891b2',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome for sleek icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #0b0f19;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card-hover:hover {
            transform: translateY(-4px);
            border-color: rgba(6, 182, 212, 0.4);
            box-shadow: 0 10px 30px -10px rgba(6, 182, 212, 0.25);
        }
        .neon-glow {
            text-shadow: 0 0 12px rgba(6, 182, 212, 0.6);
        }
        .neon-border {
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.3);
        }
        .gradient-text {
            background: linear-gradient(135deg, #ffffff 0%, #38bdf8 50%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gradient-accent {
            background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-cyan-500 selection:text-black">

<!-- Navigation -->
<nav class="sticky top-0 z-50 glass-card border-b border-slate-800/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo -->
            <a href="index.php" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl gradient-accent flex items-center justify-center text-slate-950 font-black text-xl shadow-lg shadow-cyan-500/20 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div>
                    <span class="text-2xl font-extrabold tracking-wider text-white">ORBITA<span class="text-cyan-400">.</span></span>
                    <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-widest -mt-1">NUTRITION DIRECT</span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <div class="hidden md:flex items-center space-x-1">
                <a href="index.php" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentPage === 'index.php' ? 'text-cyan-400 bg-cyan-500/10 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' ?>">
                    <i class="fa-solid fa-house mr-1.5 text-xs"></i>Home
                </a>
                <a href="about.php" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentPage === 'about.php' ? 'text-cyan-400 bg-cyan-500/10 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' ?>">
                    <i class="fa-solid fa-shield-halved mr-1.5 text-xs"></i>About
                </a>
                <a href="business-plan.php" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentPage === 'business-plan.php' ? 'text-cyan-400 bg-cyan-500/10 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' ?>">
                    <i class="fa-solid fa-sitemap mr-1.5 text-xs"></i>Business Plan
                </a>
                <a href="contact.php" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentPage === 'contact.php' ? 'text-cyan-400 bg-cyan-500/10 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' ?>">
                    <i class="fa-solid fa-headset mr-1.5 text-xs"></i>Contact
                </a>
            </div>

            <!-- Action Buttons -->
            <div class="hidden md:flex items-center space-x-3">
                <?php if ($currentUser): ?>
                    <a href="dashboard.php" class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                        <i class="fa-solid fa-gauge text-cyan-400 mr-2"></i>Dashboard
                    </a>
                    <?php if ($currentUser['is_admin']): ?>
                        <a href="admin.php" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-950 bg-cyan-400 hover:bg-cyan-300 transition shadow-lg shadow-cyan-500/20">
                            <i class="fa-solid fa-user-gear mr-1.5"></i>Admin
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="px-3 py-2 rounded-xl text-sm font-medium text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-200 hover:text-white hover:bg-slate-800 transition">
                        Sign In
                    </a>
                    <a href="register.php" class="px-5 py-2.5 rounded-xl text-sm font-extrabold text-slate-950 gradient-accent hover:brightness-110 transition shadow-lg shadow-cyan-500/25">
                        <i class="fa-solid fa-rocket mr-1.5"></i>Join Now (₹6,000)
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Toggle Button -->
            <div class="md:hidden flex items-center">
                <button type="button" id="mobile-menu-btn" class="text-slate-300 hover:text-white p-2 focus:outline-none">
                    <i class="fa-solid fa-bars text-xl" id="menu-icon"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobile-menu" class="hidden md:hidden glass-card border-t border-slate-800 px-4 pt-2 pb-6 space-y-2">
        <a href="index.php" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">Home</a>
        <a href="about.php" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">About Us</a>
        <a href="business-plan.php" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">Business Plan</a>
        <a href="contact.php" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">Contact Support</a>
        <div class="pt-4 border-t border-slate-800 space-y-2">
            <?php if ($currentUser): ?>
                <a href="dashboard.php" class="block w-full text-center px-4 py-2.5 rounded-xl font-semibold text-white bg-slate-800">Dashboard</a>
                <?php if ($currentUser['is_admin']): ?>
                    <a href="admin.php" class="block w-full text-center px-4 py-2.5 rounded-xl font-semibold text-slate-950 bg-cyan-400">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php" class="block w-full text-center px-4 py-2.5 rounded-xl font-semibold text-red-400 bg-red-950/40">Log Out</a>
            <?php else: ?>
                <a href="login.php" class="block w-full text-center px-4 py-2 rounded-xl font-medium text-slate-200 border border-slate-700">Member Sign In</a>
                <a href="register.php" class="block w-full text-center px-4 py-2.5 rounded-xl font-extrabold text-slate-950 gradient-accent shadow-lg">Join Network (₹6,000)</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>
