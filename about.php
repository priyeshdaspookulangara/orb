<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/header.php';
?>

<!-- Hero Header -->
<section class="py-16 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
        <span class="px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-bold uppercase tracking-widest">
            Uncompromising Quality & Transparency
        </span>
        <h1 class="text-4xl sm:text-5xl font-black text-white">THE ORBITA NUTRITION STANDARD</h1>
        <p class="text-slate-400 max-w-2xl mx-auto text-base">
            Engineered for elite athletes, fitness enthusiasts, and visionary entrepreneurs. We combine cold-filtered 100% Whey Protein Isolate with a revolutionary direct-selling income engine.
        </p>
    </div>
</section>

<!-- Brand Values Grid -->
<section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="glass-card p-8 rounded-2xl border border-slate-800 space-y-4">
            <div class="w-12 h-12 rounded-xl gradient-accent text-slate-950 font-extrabold flex items-center justify-center text-xl shadow-lg shadow-cyan-500/20">
                <i class="fa-solid fa-flask"></i>
            </div>
            <h3 class="text-2xl font-bold text-white">100% Ultra-Filtered Isolate</h3>
            <p class="text-sm text-slate-400 leading-relaxed">
                Derived exclusively from grass-fed dairy, cross-flow micro-filtered to eliminate lactose, fat, and carbohydrates while retaining vital bioactive peptides.
            </p>
            <ul class="text-xs text-cyan-400 space-y-1.5 font-medium pt-2">
                <li><i class="fa-solid fa-check mr-2"></i>27 Grams Pure Protein per Serve</li>
                <li><i class="fa-solid fa-check mr-2"></i>Zero Added Sugar & Zero Fillers</li>
                <li><i class="fa-solid fa-check mr-2"></i>Enriched with Digestive Enzymes</li>
            </ul>
        </div>

        <div class="glass-card p-8 rounded-2xl border border-slate-800 space-y-4">
            <div class="w-12 h-12 rounded-xl gradient-accent text-slate-950 font-extrabold flex items-center justify-center text-xl shadow-lg shadow-cyan-500/20">
                <i class="fa-solid fa-certificate"></i>
            </div>
            <h3 class="text-2xl font-bold text-white">Certified Manufacturing</h3>
            <p class="text-sm text-slate-400 leading-relaxed">
                Produced in state-of-the-art GMP, ISO 22000, and FSSAI certified facilities with batch-by-batch third-party laboratory verification.
            </p>
            <ul class="text-xs text-cyan-400 space-y-1.5 font-medium pt-2">
                <li><i class="fa-solid fa-check mr-2"></i>FSSAI License #10020022001199</li>
                <li><i class="fa-solid fa-check mr-2"></i>Heavy Metals & Amino Spiking Tested</li>
                <li><i class="fa-solid fa-check mr-2"></i>QR Code Batch Authenticity</li>
            </ul>
        </div>

        <div class="glass-card p-8 rounded-2xl border border-slate-800 space-y-4">
            <div class="w-12 h-12 rounded-xl gradient-accent text-slate-950 font-extrabold flex items-center justify-center text-xl shadow-lg shadow-cyan-500/20">
                <i class="fa-solid fa-diagram-project"></i>
            </div>
            <h3 class="text-2xl font-bold text-white">Automated Matrix Algorithm</h3>
            <p class="text-sm text-slate-400 leading-relaxed">
                Built on transparent, deterministic PHP code. Every distributor registration triggers immediate dispatch and automated commission ledger updates.
            </p>
            <ul class="text-xs text-cyan-400 space-y-1.5 font-medium pt-2">
                <li><i class="fa-solid fa-check mr-2"></i>Strict Company-Wide BFS Queue</li>
                <li><i class="fa-solid fa-check mr-2"></i>Instant Direct Sponsor Credit (₹1,000)</li>
                <li><i class="fa-solid fa-check mr-2"></i>5-Tier Upline Cascade (₹200)</li>
            </ul>
        </div>
    </div>
</section>

<!-- Manufacturing & Logistics Showcase -->
<section class="py-16 bg-slate-950/40 border-y border-slate-800/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <h2 class="text-3xl font-black text-white">From Lab to Doorstep</h2>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Our direct-to-consumer model removes retail middlemen markups. Every ₹6,000 kit delivers high-grade nutritional value combined with real financial opportunity.
                </p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="glass-card p-4 rounded-xl border border-slate-800">
                        <div class="text-2xl font-black text-cyan-400">24 Hours</div>
                        <div class="text-xs text-slate-400">Dispatch SLA</div>
                    </div>
                    <div class="glass-card p-4 rounded-xl border border-slate-800">
                        <div class="text-2xl font-black text-emerald-400">100%</div>
                        <div class="text-xs text-slate-400">Genuine Sealed Tubs</div>
                    </div>
                </div>
            </div>

            <div class="glass-card p-8 rounded-3xl border border-slate-800 space-y-6">
                <h3 class="text-xl font-bold text-white border-b border-slate-800 pb-3">Distributor Kit Details</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <i class="fa-solid fa-box text-cyan-400 mt-1"></i>
                        <div>
                            <span class="font-bold text-white text-sm">1kg Orbita Protein Powder Tub</span>
                            <p class="text-xs text-slate-400">Choice of Rich Chocolate or Swiss Vanilla with scoop included.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <i class="fa-solid fa-truck-fast text-cyan-400 mt-1"></i>
                        <div>
                            <span class="font-bold text-white text-sm">Insured Express Shipping</span>
                            <p class="text-xs text-slate-400">Tracking code generated in distributor dashboard upon dispatch.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <i class="fa-solid fa-key text-cyan-400 mt-1"></i>
                        <div>
                            <span class="font-bold text-white text-sm">Lifetime Business License</span>
                            <p class="text-xs text-slate-400">Permanent position in the global 10-matrix tree.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
