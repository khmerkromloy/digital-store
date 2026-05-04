<footer>
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <a class="navbar-brand d-flex align-items-center gap-2 text-white mb-3" href="{{ route('home') }}">
                    <i class="bi bi-bag-check-fill fs-3"></i>
                    <span class="fw-bold fs-4">DigitalShop</span>
                </a>
                <p class="small mb-3" style="color:#94a3b8;">
                    Genuine license keys, premium accounts, and digital products at unbeatable prices —
                    delivered instantly to your inbox.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x fs-5"></i></a>
                    <a href="#" aria-label="Telegram"><i class="bi bi-telegram fs-5"></i></a>
                    <a href="#" aria-label="Discord"><i class="bi bi-discord fs-5"></i></a>
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h6>Shop</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><a href="{{ route('products.index') }}">All Products</a></li>
                    <li><a href="{{ route('categories.index') }}">Categories</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'license-keys']) }}">License Keys</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'spotify-accounts']) }}">Spotify</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h6>Company</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}">Terms of Service</a></li>
                </ul>
            </div>

            <div class="col-lg-4">
                <h6>Stay in the loop</h6>
                <p class="small" style="color:#94a3b8;">
                    Subscribe to get notified about new products, restocks, and exclusive deals.
                </p>
                <form class="d-flex gap-2 mt-2" onsubmit="return false">
                    <input type="email" class="form-control form-control-sm" placeholder="you@example.com" disabled>
                    <button class="btn btn-primary btn-sm" disabled>Subscribe</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <span>&copy; {{ date('Y') }} DigitalShop. All rights reserved.</span>
            <span class="d-flex gap-3">
                <a href="{{ route('privacy') }}">Privacy</a>
                <a href="{{ route('terms') }}">Terms</a>
                <a href="{{ route('contact') }}">Contact</a>
            </span>
        </div>
    </div>
</footer>
