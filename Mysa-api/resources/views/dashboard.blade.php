<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mysa API - E-commerce Dashboard</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #0f172a;
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --accent: #10b981;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            background-image: radial-gradient(circle at 15% 50%, rgba(59, 130, 246, 0.15), transparent 25%),
                              radial-gradient(circle at 85% 30%, rgba(16, 185, 129, 0.15), transparent 25%);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Utils: Glassmorphism */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
        }

        /* Navbar */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            margin: 1rem;
            position: sticky;
            top: 1rem;
            z-index: 100;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: -webkit-linear-gradient(45deg, #3b82f6, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-actions { display: flex; gap: 1rem; align-items: center; }

        input, button {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--glass-border);
            background: rgba(255,255,255,0.05);
            color: white;
            outline: none;
            transition: all 0.2s;
        }

        input:focus { border-color: var(--primary); }

        button {
            background: var(--primary);
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        button:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-danger { background: var(--danger); }
        .btn-accent { background: var(--accent); }

        /* Main Container */
        main {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            border-color: rgba(255,255,255,0.2);
        }

        .product-name { font-size: 1.1rem; font-weight: 600; }
        .product-price { font-size: 1.2rem; color: var(--accent); font-weight: 800; }
        .product-stock { font-size: 0.85rem; color: var(--text-muted); }

        /* Cart Sidebar */
        #cart-panel {
            position: fixed;
            top: 0;
            right: -400px;
            width: 350px;
            height: 100vh;
            padding: 2rem;
            transition: right 0.3s ease;
            z-index: 200;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        #cart-panel.open { right: 0; }
        .close-cart { background: transparent; color: white; border: 1px solid white; float: right; }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--glass-border);
        }

        /* Admin Section */
        #admin-panel {
            margin-bottom: 2rem;
            padding: 1.5rem;
            display: none;
        }

        .admin-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        /* Misc */
        .hidden { display: none !important; }
        .pagination { margin-top: 2rem; display: flex; gap: 0.5rem; justify-content: center; }
        .toast {
            position: fixed; bottom: 20px; right: 20px;
            padding: 1rem; border-radius: 8px; z-index: 1000;
            background: var(--accent); color: white;
            transform: translateY(100px); opacity: 0; transition: all 0.3s;
        }
        .toast.show { transform: translateY(0); opacity: 1; }
        .toast.error { background: var(--danger); }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="glass">
        <div class="logo">Mysa Store</div>
        
        <!-- Logged Out State -->
        <div id="auth-guest" class="nav-actions">
            <input type="email" id="login-email" placeholder="Email" value="khachhang@gmail.com">
            <input type="password" id="login-pass" placeholder="Password" value="123456">
            <button onclick="login()">Đăng Nhập</button>
        </div>

        <!-- Logged In State -->
        <div id="auth-user" class="nav-actions hidden">
            <span id="user-name" style="font-weight: 600;"></span>
            <span id="user-role" style="font-size: 0.8rem; padding: 2px 6px; border-radius: 4px; background: rgba(255,255,255,0.1);"></span>
            <button class="btn-accent" onclick="toggleCart()">🛒 Giỏ Hàng</button>
            <button class="btn-danger" onclick="logout()">Đăng Xuất</button>
        </div>
    </nav>

    <main>
        <!-- Admin Panel -->
        <div id="admin-panel" class="glass">
            <h3 style="color: var(--accent);">🛠️ Khu Vực Quản Trị (Admin) - Thêm Sản Phẩm Mới</h3>
            <div class="admin-form">
                <input type="text" id="add-name" placeholder="Tên sản phẩm (VD: iPhone 16)">
                <input type="number" id="add-price" placeholder="Giá tiền (VND)">
                <input type="number" id="add-stock" placeholder="Số lượng kho">
                <button class="btn-accent" onclick="addProduct()">Thêm Sản Phẩm</button>
            </div>
        </div>

        <!-- Header & Search -->
        <div class="header-section">
            <h2>Sản phẩm nổi bật</h2>
            <div>
                <input type="text" id="search-input" placeholder="Tìm kiếm sản phẩm..." oninput="debounceSearch()">
            </div>
        </div>

        <!-- Products List -->
        <div id="products-grid" class="products-grid">
            <!-- Rendered via JS -->
        </div>
        
        <!-- Pagination -->
        <div id="pagination" class="pagination"></div>
    </main>

    <!-- Cart Sidebar -->
    <div id="cart-panel" class="glass">
        <div>
            <h2>Giỏ Hàng <button class="close-cart" onclick="toggleCart()">X</button></h2>
        </div>
        <div id="cart-items" style="flex: 1; overflow-y: auto;">
            <!-- Cart items here -->
        </div>
        <div style="border-top: 1px solid var(--glass-border); padding-top: 1rem;">
            <h3 style="margin-bottom: 1rem;">Tổng tiền: <span id="cart-total" style="color: var(--accent);">0 đ</span></h3>
            <button class="btn-accent" style="width: 100%; padding: 1rem; font-size: 1.1rem;" onclick="checkout()">🚀 CHỐT ĐƠN</button>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        const API_URL = 'http://127.0.0.1:8000/api';
        let token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user'));
        let currentPage = 1;
        let searchTimeout = null;

        // --- Core Functions ---
        
        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.className = 'toast show ' + (isError ? 'error' : '');
            setTimeout(() => { toast.classList.remove('show'); }, 3000);
        }

        async function apiCall(endpoint, method = 'GET', body = null) {
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const options = { method, headers };
            if (body) options.body = JSON.stringify(body);

            const res = await fetch(`${API_URL}${endpoint}`, options);
            const data = await res.json();
            
            if (!res.ok) throw new Error(data.message || data.error || 'Lỗi API');
            return data;
        }

        // --- Auth Logic ---

        function checkAuth() {
            if (token && user) {
                document.getElementById('auth-guest').classList.add('hidden');
                document.getElementById('auth-user').classList.remove('hidden');
                document.getElementById('user-name').textContent = user.name;
                document.getElementById('user-role').textContent = user.role.toUpperCase();
                
                if (user.role === 'admin') {
                    document.getElementById('admin-panel').style.display = 'block';
                }
                loadCart();
            } else {
                document.getElementById('auth-guest').classList.remove('hidden');
                document.getElementById('auth-user').classList.add('hidden');
                document.getElementById('admin-panel').style.display = 'none';
            }
        }

        async function login() {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-pass').value;
            try {
                const data = await apiCall('/login', 'POST', { email, password });
                token = data.token;
                user = data.user;
                localStorage.setItem('token', token);
                localStorage.setItem('user', JSON.stringify(user));
                checkAuth();
                showToast(`Xin chào, ${user.name}!`);
            } catch (e) {
                showToast(e.message, true);
            }
        }

        function logout() {
            token = null;
            user = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            checkAuth();
            showToast('Đã đăng xuất');
        }

        // --- Products Logic ---

        async function loadProducts(page = 1, search = '') {
            try {
                const data = await apiCall(`/products?page=${page}&search=${search}`);
                const grid = document.getElementById('products-grid');
                grid.innerHTML = '';
                
                data.data.forEach(p => {
                    const priceFmt = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p.price);
                    grid.innerHTML += `
                        <div class="product-card glass">
                            <span style="font-size: 0.8rem; color: var(--primary)">${p.category ? p.category.name : 'Chưa phân loại'}</span>
                            <div class="product-name">${p.name}</div>
                            <div class="product-price">${priceFmt}</div>
                            <div class="product-stock">Tồn kho: ${p.stock}</div>
                            <button style="margin-top: auto" onclick="addToCart(${p.id})">Thêm vào giỏ</button>
                        </div>
                    `;
                });

                // Render pagination
                const pag = document.getElementById('pagination');
                pag.innerHTML = '';
                if(data.current_page > 1) {
                    pag.innerHTML += `<button onclick="loadProducts(${data.current_page - 1})">Trước</button>`;
                }
                if(data.current_page < data.last_page) {
                    pag.innerHTML += `<button onclick="loadProducts(${data.current_page + 1})">Tiếp</button>`;
                }

            } catch (e) {
                showToast('Lỗi tải sản phẩm: ' + e.message, true);
            }
        }

        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const val = document.getElementById('search-input').value;
                loadProducts(1, val);
            }, 500);
        }

        // --- Admin Logic ---
        
        async function addProduct() {
            const name = document.getElementById('add-name').value;
            const price = document.getElementById('add-price').value;
            const stock = document.getElementById('add-stock').value;
            
            if(!name || !price || !stock) return showToast('Vui lòng điền đủ thông tin', true);

            try {
                await apiCall('/products', 'POST', { name, price, stock, category_id: 1 });
                showToast('Thêm sản phẩm thành công!');
                loadProducts();
                // Clear input
                document.getElementById('add-name').value = '';
                document.getElementById('add-price').value = '';
                document.getElementById('add-stock').value = '';
            } catch(e) {
                showToast(e.message, true);
            }
        }

        // --- Cart Logic ---

        function toggleCart() {
            if(!token) return showToast('Bạn cần đăng nhập để dùng giỏ hàng', true);
            document.getElementById('cart-panel').classList.toggle('open');
            loadCart();
        }

        async function loadCart() {
            if(!token) return;
            try {
                const data = await apiCall('/cart');
                const itemsDiv = document.getElementById('cart-items');
                let total = 0;
                
                if(!data.items || data.items.length === 0) {
                    itemsDiv.innerHTML = '<p style="color: var(--text-muted)">Giỏ hàng trống.</p>';
                } else {
                    itemsDiv.innerHTML = '';
                    data.items.forEach(item => {
                        const priceFmt = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.product.price);
                        total += item.product.price * item.quantity;
                        itemsDiv.innerHTML += `
                            <div class="cart-item">
                                <div>
                                    <div style="font-weight: 600;">${item.product.name}</div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted)">${priceFmt} x ${item.quantity}</div>
                                </div>
                                <button class="btn-danger" style="padding: 0.2rem 0.5rem;" onclick="removeFromCart(${item.id})">Xóa</button>
                            </div>
                        `;
                    });
                }
                document.getElementById('cart-total').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total);
            } catch (e) {
                console.error(e);
            }
        }

        async function addToCart(product_id) {
            if(!token) return showToast('Bạn cần đăng nhập để mua hàng!', true);
            try {
                await apiCall('/cart', 'POST', { product_id, quantity: 1 });
                showToast('Đã thêm vào giỏ hàng');
                loadCart(); // refresh data silently
            } catch(e) {
                showToast(e.message, true);
            }
        }

        async function removeFromCart(itemId) {
            try {
                await apiCall(`/cart/${itemId}`, 'DELETE');
                loadCart();
            } catch(e) {
                showToast(e.message, true);
            }
        }

        async function checkout() {
            try {
                await apiCall('/orders', 'POST');
                showToast('🚀 Đặt hàng thành công!');
                loadCart();
                loadProducts(); // Refresh to update stock UI
                setTimeout(() => toggleCart(), 1500); // close cart
            } catch (e) {
                showToast(e.message, true);
            }
        }

        // --- Init ---
        checkAuth();
        loadProducts();

    </script>
</body>
</html>
