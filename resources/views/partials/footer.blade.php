<footer class="bg-primary text-white pt-5 pb-4">
    <div class="container">
        <div class="row gy-5">
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold mb-3">
                    <img alt="Logo"
                         src="{{ asset('images/logo_light.png') }}"
                         style="max-width: 200px;">
                </h5>
                <address class="small mb-3">
                    Gedung Sinarmas MSIG Tower Lt. 33<br>
                    Jl. Jenderal Sudirman Kav. 21, Karet Kuningan,<br>
                    Setiabudi, Jakarta Selatan, Jakarta 12920
                </address>
                <div class="mt-3">
                    <p class="mb-1 fw-semibold">Hubungi Kami</p>
                    <p class="mb-1">
                        <i class="bi bi-envelope me-2"></i><a class="text-white text-decoration-none"
                           href="mailto:info@electrons.id">info@electrons.id</a>
                    </p>
                    <p class="mb-1">
                        <i class="bi bi-telephone me-2"></i><a class="text-white text-decoration-none"
                           href="tel:02130930000">(021) 3093 0000</a>
                    </p>
                    <p class="mb-1">
                        <i class="bi bi-whatsapp me-2"></i><a class="text-white text-decoration-none"
                           href="https://wa.me/6281574410000">0815 7441 0000</a>
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-3 text-white">Kategori</h6>
                <ul class="list-unstyled small">
                    @foreach ($categories as $category)
                        <li class="{{ $loop->last ? '' : 'mb-2' }}">
                            <a class="text-decoration-none text-white-50"
                               href="{{ route('posts.category', $category->slug) }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-3 text-white">Link</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <a class="text-decoration-none text-white-50"
                           href="#">Tentang Kami</a>
                    </li>
                    <li class="mb-2">
                        <a class="text-decoration-none text-white-50"
                           href="#">Blog</a>
                    </li>
                    <li class="mb-2">
                        <a class="text-decoration-none text-white-50"
                           href="#">Karir</a>
                    </li>
                    <li class="mb-2">
                        <a class="text-decoration-none text-white-50"
                           href="#">Syarat & Ketentuan</a>
                    </li>
                    <li class="mb-2">
                        <a class="text-decoration-none text-white-50"
                           href="#">Kebijakan Privasi</a>
                    </li>
                    <li class="mb-2">
                        <a class="text-decoration-none text-white-50"
                           href="#">Bantuan</a>
                    </li>
                    <li class="mb-2">
                        <a class="text-decoration-none text-white-50"
                           href="#">Kontak</a>
                    </li>
                    <li>
                        <a class="text-decoration-none text-white-50"
                           href="#">FAQ</a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-12">
                <h6 class="fw-bold mb-3 text-white">Ikuti Kami</h6>
                <div class="d-flex gap-3">
                    <a class="text-white fs-4 social-icon"
                       href="#"
                       title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a class="text-white fs-4 social-icon"
                       href="#"
                       title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a class="text-white fs-4 social-icon"
                       href="#"
                       title="X"><i class="bi bi-twitter-x"></i></a>
                    <a class="text-white fs-4 social-icon"
                       href="#"
                       title="YouTube"><i class="bi bi-youtube"></i></a>
                    <a class="text-white fs-4 social-icon"
                       href="#"
                       title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a class="text-white fs-4 social-icon"
                       href="#"
                       title="TikTok"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
        </div>

        <hr class="border-light opacity-25 my-4">

        <div class="text-center small opacity-75">
            © 2025 All Rights Reserved <span class="fw-bold">{{ config('app.name') }}</span>
        </div>
    </div>
</footer>
