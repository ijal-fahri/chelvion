<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pusat Bantuan - CELVION</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { 
            font-family: 'Poppins', sans-serif; 
        }
        
        body {
            background-color: #f8fafc;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .navbar-nav .nav-link {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            margin: 0 0.2rem;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background-color: #eef2ff;
            color: #4f46e5 !important;
        }
        
        .btn-purple {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: white;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-purple:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-outline-purple {
            color: #4f46e5;
            border: 1px solid #4f46e5;
            background-color: transparent;
            transition: all 0.2s ease;
        }

        .btn-outline-purple:hover {
            background-color: #4f46e5;
            color: #fff;
        }

        .card-custom {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .help-search {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 1rem;
            padding: 3rem 2rem;
        }

        .help-category-card {
            height: 100%;
            transition: all 0.3s ease;
            border-left: 4px solid #4f46e5;
        }

        .help-category-card:hover {
            border-left-color: #7c3aed;
            transform: translateX(4px);
        }

        .faq-item {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            border-color: #4f46e5;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
        }

        .faq-question {
            padding: 1.5rem;
            background-color: white;
            border: none;
            width: 100%;
            text-align: left;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            background-color: #f8fafc;
        }

        .faq-question[aria-expanded="true"] {
            background-color: #eef2ff;
            color: #4f46e5;
        }

        .faq-answer {
            padding: 0 1.5rem 1.5rem;
            background-color: white;
            color: #64748b;
        }

        .contact-card {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            border-radius: 1rem;
            padding: 2rem;
            height: 100%;
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .support-badge {
            background-color: #10b981;
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .quick-link {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            text-decoration: none;
            color: #374151;
            transition: all 0.3s ease;
            margin-bottom: 0.75rem;
        }

        .quick-link:hover {
            border-color: #4f46e5;
            background-color: #f8fafc;
            color: #4f46e5;
            transform: translateY(-2px);
        }

        .quick-link-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            background-color: #eef2ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
            margin-right: 1rem;
            transition: all 0.3s ease;
        }

        .quick-link:hover .quick-link-icon {
            background-color: #4f46e5;
            color: white;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .status-online {
            background-color: #10b981;
        }

        .search-input {
            border: none;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .section-title {
            border-bottom: 2px solid #eef2ff;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }

        .help-section {
            margin-bottom: 4rem;
        }

        .back-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #4f46e5;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background-color: #4338ca;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    @include('components.navbar-pelanggan', [
        'orderCount' => $orderCount ?? 0,
        'wishlistCount' => $wishlistCount ?? 0,
        'cartCount' => $cartCount ?? 0
    ])

    <main class="container my-5">
        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="display-5 fw-bold text-dark mb-3">Pusat Bantuan CELVION</h1>
                <p class="lead text-muted mb-4">Temukan solusi untuk masalah Anda dengan cepat dan mudah</p>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="help-section" id="faq">
            <h3 class="h4 fw-bold text-dark section-title">Pertanyaan Umum (FAQ)</h3>
            <div class="row">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <!-- FAQ Item 1 -->
                        <div class="faq-item">
                            <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
                                Bagaimana cara melakukan pemesanan produk?
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div id="faq1" class="collapse show" data-bs-parent="#faqAccordion">
                                <div class="faq-answer">
                                    <p>Untuk melakukan pemesanan:</p>
                                    <ol>
                                        <li>Pilih produk yang ingin dibeli</li>
                                        <li>Klik tombol "Beli Sekarang" atau "Tambah ke Keranjang"</li>
                                        <li>Jika menambah ke keranjang, buka keranjang dan klik "Checkout"</li>
                                        <li>Isi alamat pengiriman dan pilih metode pembayaran</li>
                                        <li>Konfirmasi pesanan dan lakukan pembayaran</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="faq-item">
                            <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Berapa lama waktu pengiriman pesanan?
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div id="faq2" class="collapse" data-bs-parent="#faqAccordion">
                                <div class="faq-answer">
                                    <p>Waktu pengiriman bervariasi tergantung:</p>
                                    <ul>
                                        <li><strong>Jabodetabek:</strong> 1-3 hari kerja</li>
                                        <li><strong>Pulau Jawa:</strong> 2-5 hari kerja</li>
                                        <li><strong>Luar Jawa:</strong> 3-7 hari kerja</li>
                                        <li><strong>Indonesia Timur:</strong> 5-10 hari kerja</li>
                                    </ul>
                                    <p class="mb-0">Waktu di atas belum termasuk waktu proses dari gudang kami.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 3 -->
                        <div class="faq-item">
                            <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Bagaimana cara melakukan retur produk?
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div id="faq3" class="collapse" data-bs-parent="#faqAccordion">
                                <div class="faq-answer">
                                    <p>Produk dapat diretur dalam waktu 7 hari setelah diterima dengan syarat:</p>
                                    <ul>
                                        <li>Produk dalam kondisi original</li>
                                        <li>Masih terdapat segel dan packaging lengkap</li>
                                        <li>Ada bukti pembelian</li>
                                        <li>Produk tidak termasuk kategori non-returnable</li>
                                    </ul>
                                    <p class="mb-0">Untuk proses retur, silakan hubungi customer service kami.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 4 -->
                        <div class="faq-item">
                            <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Metode pembayaran apa saja yang diterima?
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div id="faq4" class="collapse" data-bs-parent="#faqAccordion">
                                <div class="faq-answer">
                                    <p>Kami menerima berbagai metode pembayaran:</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Transfer Bank</h6>
                                            <ul>
                                                <li>BCA</li>
                                                <li>BNI</li>
                                                <li>BRI</li>
                                                <li>Mandiri</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Digital Payment</h6>
                                            <ul>
                                                <li>Gopay</li>
                                                <li>OVO</li>
                                                <li>Dana</li>
                                                <li>ShopeePay</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 5 -->
                        <div class="faq-item">
                            <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                Bagaimana cara melacak pesanan saya?
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div id="faq5" class="collapse" data-bs-parent="#faqAccordion">
                                <div class="faq-answer">
                                    <p>Anda dapat melacak pesanan dengan beberapa cara:</p>
                                    <ol>
                                        <li>Login ke akun Anda dan buka halaman "Pesanan Saya"</li>
                                        <li>Klik "Detail Pesanan" pada pesanan yang ingin dilacak</li>
                                        <li>Nomor resi akan tersedia dan dapat dilacak melalui website kurir</li>
                                        <li>Atau gunakan fitur tracking di website kami dengan memasukkan nomor pesanan</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="contact-card">
                        <h5 class="fw-bold text-dark mb-4">Butuh Bantuan Lebih Lanjut?</h5>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-headset text-purple fs-4 me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Customer Service</h6>
                                    <p class="text-muted mb-0">Bantuan 24/7 melalui chat</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="status-indicator status-online"></span>
                                <small class="text-muted">Online sekarang</small>
                            </div>
                            <button class="btn btn-purple w-100">
                                <i class="bi bi-chat-left-text me-2"></i>Mulai Chat
                            </button>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-telephone text-purple me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Telepon</h6>
                                    <p class="text-muted mb-0">(021) 1234-5678</p>
                                </div>
                            </div>
                            <small class="text-muted">Senin - Jumat, 08:00 - 17:00 WIB</small>
                        </div>
                        
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-envelope text-purple me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Email</h6>
                                    <p class="text-muted mb-0">help@celvion.com</p>
                                </div>
                            </div>
                            <small class="text-muted">Response dalam 24 jam</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="help-section">
            <div class="card card-custom">
                <div class="card-body p-5 text-center">
                    <h3 class="h2 fw-bold text-dark mb-3">Masih Tidak Menemukan Solusi?</h3>
                    <p class="text-muted mb-4">Tim support kami siap membantu menyelesaikan masalah Anda</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <button class="btn btn-purple btn-lg">
                            <i class="bi bi-chat-left-text me-2"></i>Hubungi Customer Service
                        </button>
                        <button class="btn btn-outline-purple btn-lg">
                            <i class="bi bi-telephone me-2"></i>Telepon Kami
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold">CELVION</h5>
                    <p class="text-light">Toko elektronik terpercaya dengan produk berkualitas dan harga terbaik.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold">Bantuan</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Pusat Bantuan</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Cara Pembelian</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Pengiriman</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Pengembalian</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold">Tentang Kami</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Tentang CELVION</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Karir</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Blog</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="fw-bold">Hubungi Kami</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-geo-alt me-2"></i>
                            <span class="text-light">Jl. Contoh No. 123, Jakarta</span>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone me-2"></i>
                            <span class="text-light">+62 21 1234 5678</span>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-envelope me-2"></i>
                            <span class="text-light">info@celvion.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 CELVION. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="bi bi-chevron-up"></i>
    </a>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Back to top button
            const backToTop = document.getElementById('backToTop');
            
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTop.classList.add('show');
                } else {
                    backToTop.classList.remove('show');
                }
            });
            
            backToTop.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            
            // FAQ accordion animation
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (this.getAttribute('aria-expanded') === 'true') {
                        icon.style.transform = 'rotate(0deg)';
                    } else {
                        icon.style.transform = 'rotate(180deg)';
                    }
                });
            });
            
            // Smooth scroll for quick links
            const quickLinks = document.querySelectorAll('.quick-link');
            
            quickLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                });
            });
            
            // Search functionality
            const searchInput = document.querySelector('.search-input');
            const searchButton = document.querySelector('.btn-light');
            
            searchButton.addEventListener('click', function() {
                const searchTerm = searchInput.value.trim();
                if (searchTerm) {
                    // Simulate search - in real implementation, this would filter FAQ items
                    alert(`Mencari: ${searchTerm}\n\nFitur pencarian akan menyaring FAQ dan konten bantuan berdasarkan kata kunci.`);
                }
            });
            
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchButton.click();
                }
            });
        });
    </script>
</body>
</html>