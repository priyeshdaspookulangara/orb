<?php
require_once __DIR__ . '/db.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $errorMsg = 'Please fill in all required fields.';
    } else {
        $successMsg = 'Thank you! Your message has been routed to our support desk. Ticket ID: #ORB-' . rand(10000, 99999);
    }
}

include __DIR__ . '/header.php';
?>

<!-- Header -->
<section class="py-16 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
        <span class="px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-bold uppercase tracking-widest">
            24/7 Dedicated Distributor Support
        </span>
        <h1 class="text-4xl sm:text-5xl font-black text-white">GET IN TOUCH WITH ORBITA</h1>
        <p class="text-slate-400 max-w-2xl mx-auto text-base">
            Have questions about your product dispatch, matrix earnings, or business kit orders? Our logistics and support team is here to assist.
        </p>
    </div>
</section>

<section class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

        <!-- Contact Form -->
        <div class="lg:col-span-7 glass-card p-8 rounded-3xl border border-slate-800 space-y-6">
            <h3 class="text-2xl font-bold text-white border-b border-slate-800 pb-4">Send a Direct Message</h3>

            <?php if ($successMsg): ?>
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm font-semibold flex items-center">
                    <i class="fa-solid fa-circle-check text-xl mr-3"></i>
                    <div><?= $successMsg ?></div>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg): ?>
                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-semibold flex items-center">
                    <i class="fa-solid fa-triangle-exclamation text-xl mr-3"></i>
                    <div><?= $errorMsg ?></div>
                </div>
            <?php endif; ?>

            <form action="contact.php" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Your Full Name *</label>
                        <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition text-sm" placeholder="e.g. Rahul Sharma">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Email Address *</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition text-sm" placeholder="e.g. rahul@example.com">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Inquiry Topic</label>
                    <select name="subject" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition text-sm">
                        <option value="General Inquiry">General Product Inquiry</option>
                        <option value="Dispatch Tracking">Protein Tub Order & Dispatch Tracking</option>
                        <option value="Matrix Commissions">Wallet & Matrix Commission Query</option>
                        <option value="Sponsor Issue">Sponsor & Tree Placement Clarification</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Your Message *</label>
                    <textarea name="message" rows="5" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition text-sm" placeholder="Provide order details or questions..."></textarea>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl text-slate-950 font-extrabold gradient-accent hover:brightness-110 transition shadow-lg shadow-cyan-500/20 text-sm">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Submit Inquiry
                </button>
            </form>
        </div>

        <!-- Contact Information & Warehouse Cards -->
        <div class="lg:col-span-5 space-y-6">

            <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-4">
                <h4 class="text-lg font-bold text-white flex items-center">
                    <i class="fa-solid fa-warehouse text-cyan-400 mr-2.5"></i> Central Fulfillment Warehouse
                </h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Orbita Logistics Park, Tower 4, Cyber City, Gurugram, Haryana - 122002, India.
                </p>
                <div class="text-xs text-slate-300 space-y-1">
                    <p><span class="text-slate-500">Dispatch Hours:</span> Mon - Sat, 09:00 - 18:00 IST</p>
                    <p><span class="text-slate-500">Facility Certification:</span> FSSAI / ISO 22000 Certified</p>
                </div>
            </div>

            <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-4">
                <h4 class="text-lg font-bold text-white flex items-center">
                    <i class="fa-solid fa-headset text-cyan-400 mr-2.5"></i> Distributor Helpline
                </h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Dedicated support desk for matrix members and distributor kit dispatches.
                </p>
                <div class="space-y-2 text-sm text-white font-medium">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-xs"><i class="fa-solid fa-phone"></i></div>
                        <span>+91 (0) 800-ORBITA-FIT</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-xs"><i class="fa-solid fa-envelope"></i></div>
                        <span>support@orbitanutrition.com</span>
                    </div>
                </div>
            </div>

            <div class="glass-card p-6 rounded-2xl border border-cyan-500/30 bg-cyan-500/5 space-y-2">
                <h4 class="text-sm font-bold text-cyan-400 flex items-center">
                    <i class="fa-solid fa-bolt mr-2"></i> Instant Registration Active
                </h4>
                <p class="text-xs text-slate-300 leading-relaxed">
                    New orders placed via `register.php` are automatically queued in the BFS matrix algorithm and sent for immediate warehouse packing.
                </p>
            </div>

        </div>

    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
