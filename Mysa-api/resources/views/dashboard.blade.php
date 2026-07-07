<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mysa API - E-commerce Dashboard V2</title>
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
            --warning: #f59e0b;
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

        input, button, select {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--glass-border);
            background: rgba(255,255,255,0.05);
            color: white;
            outline: none;
            transition: all 0.2s;
        }

        input:focus, select:focus { border-color: var(--primary); }

        button {
            background: var(--primary);
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        button:hover { background: var(--primary-hover); transform: translateY(-1px); }
        .btn-danger { background: var(--danger); }
        .btn-accent { background: var(--accent); }
        .btn-outline { background: transparent; border: 1px solid var(--primary); }

        /* Main Container */
        main { padding: 2rem; max-width: 1200px; margin: 0 auto; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.2); }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            background: rgba(0,0,0,0.2);
        }
        .product-name { font-size: 1.1rem; font-weight: 600; }
        .product-price { font-size: 1.2rem; color: var(--accent); font-weight: 800; }
        .product-desc { font-size: 0.85rem; color: var(--text-muted); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .product-meta { display: flex; justify-content: space-between; font-size: 0.85rem; }
        .rating { color: var(--warning); font-weight: 600; }

        /* Panels (Cart & Orders) */
        .side-panel {
            position: fixed; top: 0; right: -450px;
            width: 400px; height: 100vh;
            padding: 2rem; transition: right 0.3s ease;
            z-index: 200; display: flex; flex-direction: column; gap: 1.5rem;
        }
        .side-panel.open { right: 0; }
        
        .close-btn { background: transparent; color: white; border: 1px solid white; float: right; padding: 0.2rem 0.8rem;}
        
        .list-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1rem 0; border-bottom: 1px solid var(--glass-border);
        }

        /* Admin & Modal */
        #admin-panel { margin-bottom: 2rem; padding: 1.5rem; display: none; }
        
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5); z-index: 300;
            display: flex; justify-content: center; align-items: center;
            opacity: 0; pointer-events: none; transition: opacity 0.3s;
        }
        .modal-overlay.active { opacity: 1; pointer-events: all; }
        .modal-content { padding: 2rem; width: 400px; display: flex; flex-direction: column; gap: 1rem; }

        .hidden { display: none !important; }
        .pagination { margin-top: 2rem; display: flex; gap: 0.5rem; justify-content: center; }
        .toast {
            position: fixed; bottom: 20px; right: 20px; padding: 1rem; border-radius: 8px; z-index: 1000;
            background: var(--accent); color: white; transform: translateY(100px); opacity: 0; transition: all 0.3s;
        }
        .toast.show { transform: translateY(0); opacity: 1; }
        .toast.error { background: var(--danger); }
        
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;}
        .status-pending { background: rgba(245, 158, 11, 0.2); color: var(--warning); }
        .status-shipped { background: rgba(59, 130, 246, 0.2); color: var(--primary); }
        .status-delivered { background: rgba(16, 185, 129, 0.2); color: var(--accent); }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="glass">
        <div class="logo">Mysa Store Pro</div>
        
        <div id="auth-guest" class="nav-actions">
            <input type="email" id="login-email" placeholder="Email" value="khachhang@gmail.com">
            <input type="password" id="login-pass" placeholder="Password" value="123456">
            <button onclick="login()">Đăng Nhập</button>
        </div>

        <div id="auth-user" class="nav-actions hidden">
            <span id="user-name" style="font-weight: 600;"></span>
            <span id="user-role" style="font-size: 0.8rem; padding: 2px 6px; border-radius: 4px; background: rgba(255,255,255,0.1);"></span>
            <button class="btn-outline" onclick="togglePanel('orders-panel')">📦 Đơn Hàng</button>
            <button class="btn-accent" onclick="togglePanel('cart-panel')">🛒 Giỏ Hàng</button>
            <button class="btn-danger" onclick="logout()">Đăng Xuất</button>
        </div>
    </nav>

    <main>
        <!-- Admin Panel -->
        <div id="admin-panel" class="glass">
            <h3 style="color: var(--accent); margin-bottom: 1rem;">🛠️ Quản Trị - Thêm Sản Phẩm</h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <input type="text" id="add-name" placeholder="Tên sản phẩm">
                <input type="number" id="add-price" placeholder="Giá tiền">
                <input type="number" id="add-stock" placeholder="Số lượng kho">
                <input type="text" id="add-image" placeholder="URL Hình ảnh (tùy chọn)">
                <button class="btn-accent" onclick="addProduct()">Thêm</button>
            </div>
        </div>

        <div class="header-section">
            <h2>Sản phẩm nổi bật</h2>
            <div>
                <input type="text" id="search-input" placeholder="Tìm kiếm sản phẩm..." oninput="debounceSearch()">
            </div>
        </div>

        <div id="products-grid" class="products-grid"></div>
        <div id="pagination" class="pagination"></div>
    </main>

    <!-- Cart Panel -->
    <div id="cart-panel" class="side-panel glass">
        <h2>Giỏ Hàng <button class="close-btn" onclick="togglePanel('cart-panel')">X</button></h2>
        <div id="cart-items" style="flex: 1; overflow-y: auto;"></div>
        <div style="border-top: 1px solid var(--glass-border); padding-top: 1rem;">
            <h3 style="margin-bottom: 1rem;">Tổng: <span id="cart-total" style="color: var(--accent);">0 đ</span></h3>
            <button class="btn-accent" style="width: 100%; padding: 1rem; font-size: 1.1rem;" onclick="openCheckoutModal()">Thanh Toán</button>
        </div>
    </div>

    <!-- Orders Panel -->
    <div id="orders-panel" class="side-panel glass">
        <h2>Đơn Hàng <button class="close-btn" onclick="togglePanel('orders-panel')">X</button></h2>
        <div id="orders-list" style="flex: 1; overflow-y: auto;"></div>
    </div>

    <!-- Checkout Modal -->
    <div id="checkout-modal" class="modal-overlay">
        <div class="modal-content glass">
            <h2 style="color: var(--accent);">Thông tin Giao hàng</h2>
            <input type="text" id="ship-phone" placeholder="Số điện thoại người nhận">
            <input type="text" id="ship-address" placeholder="Địa chỉ giao hàng đầy đủ">
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button style="flex: 1;" class="btn-danger" onclick="closeCheckoutModal()">Hủy</button>
                <button style="flex: 1;" class="btn-accent" onclick="processCheckout()">Chốt Đơn</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        const API_URL = 'http://127.0.0.1:8000/api';
        let token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user'));
        let searchTimeout = null;

        const formatMoney = (val) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);

        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.className = 'toast show ' + (isError ? 'error' : '');
            setTimeout(() => { toast.classList.remove('show'); }, 3000);
        }

        async function apiCall(endpoint, method = 'GET', body = null) {
            const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json' };
            if (token) headers['Authorization'] = `Bearer ${token}`;
            const options = { method, headers };
            if (body) options.body = JSON.stringify(body);

            const res = await fetch(`${API_URL}${endpoint}`, options);
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || data.error || 'Lỗi hệ thống');
            return data;
        }

        function checkAuth() {
            if (token && user) {
                document.getElementById('auth-guest').classList.add('hidden');
                document.getElementById('auth-user').classList.remove('hidden');
                document.getElementById('user-name').textContent = user.name;
                document.getElementById('user-role').textContent = user.role.toUpperCase();
                
                document.getElementById('admin-panel').style.display = user.role === 'admin' ? 'block' : 'none';
                loadCart();
            } else {
                document.getElementById('auth-guest').classList.remove('hidden');
                document.getElementById('auth-user').classList.add('hidden');
                document.getElementById('admin-panel').style.display = 'none';
            }
        }

        async function login() {
            try {
                const data = await apiCall('/login', 'POST', {
                    email: document.getElementById('login-email').value,
                    password: document.getElementById('login-pass').value
                });
                token = data.token; user = data.user;
                localStorage.setItem('token', token); localStorage.setItem('user', JSON.stringify(user));
                checkAuth(); showToast(`Xin chào, ${user.name}!`);
            } catch (e) { showToast(e.message, true); }
        }

        function logout() {
            token = null; user = null; localStorage.clear();
            checkAuth(); showToast('Đã đăng xuất');
        }

        // -- PRODUCTS --
        async function loadProducts(page = 1, search = '') {
            try {
                const data = await apiCall(`/products?page=${page}&search=${search}`);
                const grid = document.getElementById('products-grid');
                grid.innerHTML = data.data.map(p => `
                    <div class="product-card glass">
                        ${p.image_url ? `<img src="${p.image_url}" class="product-image">` : `<div class="product-image"></div>`}
                        <div class="product-meta">
                            <span style="color: var(--primary)">${p.category ? p.category.name : 'Chưa phân loại'}</span>
                            <span class="rating">⭐ ${parseFloat(p.reviews_avg_rating || 5).toFixed(1)}</span>
                        </div>
                        <div class="product-name">${p.name}</div>
                        <div class="product-desc">${p.description || 'Chưa có mô tả'}</div>
                        <div class="product-price">${formatMoney(p.price)}</div>
                        <div class="product-meta" style="color: var(--text-muted)">Kho: ${p.stock}</div>
                        <button style="margin-top: auto" onclick="addToCart(${p.id})">Thêm vào giỏ</button>
                    </div>
                `).join('');

                document.getElementById('pagination').innerHTML = `
                    ${data.current_page > 1 ? `<button onclick="loadProducts(${data.current_page - 1})">Trước</button>` : ''}
                    ${data.current_page < data.last_page ? `<button onclick="loadProducts(${data.current_page + 1})">Tiếp</button>` : ''}
                `;
            } catch (e) { showToast('Lỗi tải sản phẩm: ' + e.message, true); }
        }

        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadProducts(1, document.getElementById('search-input').value), 500);
        }

        async function addProduct() {
            try {
                await apiCall('/products', 'POST', { 
                    name: document.getElementById('add-name').value, 
                    price: document.getElementById('add-price').value, 
                    stock: document.getElementById('add-stock').value,
                    image_url: document.getElementById('add-image').value,
                    category_id: 1 
                });
                showToast('Thêm thành công!'); loadProducts();
            } catch(e) { showToast(e.message, true); }
        }

        // -- CART --
        function togglePanel(id) {
            if(!token) return showToast('Vui lòng đăng nhập!', true);
            const panel = document.getElementById(id);
            if(panel.classList.contains('open')) {
                panel.classList.remove('open');
            } else {
                document.querySelectorAll('.side-panel').forEach(p => p.classList.remove('open'));
                panel.classList.add('open');
                if(id === 'cart-panel') loadCart();
                if(id === 'orders-panel') loadOrders();
            }
        }

        async function loadCart() {
            if(!token) return;
            const data = await apiCall('/cart');
            let total = 0;
            const items = data.items || [];
            
            document.getElementById('cart-items').innerHTML = items.length === 0 ? '<p>Giỏ trống</p>' : items.map(item => {
                total += item.product.price * item.quantity;
                return `
                    <div class="list-item">
                        <div>
                            <div style="font-weight: 600;">${item.product.name}</div>
                            <div style="font-size: 0.85rem;">${formatMoney(item.product.price)} x ${item.quantity}</div>
                        </div>
                        <button class="btn-danger" style="padding: 0.2rem 0.5rem;" onclick="removeFromCart(${item.id})">Xóa</button>
                    </div>
                `;
            }).join('');
            document.getElementById('cart-total').textContent = formatMoney(total);
        }

        async function addToCart(product_id) {
            if(!token) return showToast('Vui lòng đăng nhập!', true);
            try {
                await apiCall('/cart', 'POST', { product_id, quantity: 1 });
                showToast('Đã thêm vào giỏ'); loadCart();
            } catch(e) { showToast(e.message, true); }
        }

        async function removeFromCart(itemId) {
            await apiCall(`/cart/${itemId}`, 'DELETE'); loadCart();
        }

        // -- CHECKOUT --
        function openCheckoutModal() {
            if(!token) return;
            document.getElementById('checkout-modal').classList.add('active');
        }
        function closeCheckoutModal() {
            document.getElementById('checkout-modal').classList.remove('active');
        }

        async function processCheckout() {
            try {
                await apiCall('/orders', 'POST', {
                    phone: document.getElementById('ship-phone').value,
                    shipping_address: document.getElementById('ship-address').value
                });
                showToast('🚀 Đặt hàng thành công!');
                closeCheckoutModal();
                togglePanel('cart-panel');
                loadCart(); loadProducts(); loadOrders();
            } catch (e) { showToast(e.message, true); }
        }

        // -- ORDERS --
        async function loadOrders() {
            if(!token) return;
            try {
                const orders = await apiCall('/orders');
                document.getElementById('orders-list').innerHTML = orders.length === 0 ? '<p>Chưa có đơn hàng nào.</p>' : orders.map(o => `
                    <div class="list-item" style="flex-direction: column; align-items: flex-start; gap: 0.5rem;">
                        <div style="width: 100%; display: flex; justify-content: space-between;">
                            <strong>Đơn #${o.id}</strong>
                            <span class="status-badge status-${o.status}">${o.status.toUpperCase()}</span>
                        </div>
                        <div style="font-size: 0.85rem; color: var(--text-muted)">
                            ${user.role === 'admin' ? `Khách: ${o.user.name}<br>` : ''}
                            SĐT: ${o.phone}<br>Đ/c: ${o.shipping_address}<br>
                            Tổng: <span style="color:var(--accent)">${formatMoney(o.total_amount)}</span>
                        </div>
                        ${user.role === 'admin' && o.status !== 'delivered' ? `
                            <select onchange="updateOrderStatus(${o.id}, this.value)" style="margin-top: 0.5rem; width: 100%; font-size: 0.8rem; padding: 0.3rem;">
                                <option value="pending" ${o.status==='pending'?'selected':''}>Chờ xử lý (Pending)</option>
                                <option value="shipped" ${o.status==='shipped'?'selected':''}>Đang giao (Shipped)</option>
                                <option value="delivered" ${o.status==='delivered'?'selected':''}>Đã giao (Delivered)</option>
                            </select>
                        ` : ''}
                    </div>
                `).join('');
            } catch(e) { console.error(e); }
        }

        async function updateOrderStatus(orderId, status) {
            try {
                await apiCall(`/orders/${orderId}`, 'PUT', { status });
                showToast('Đã cập nhật trạng thái đơn');
                loadOrders();
            } catch(e) { showToast(e.message, true); }
        }

        // Init
        checkAuth();
        loadProducts();
    </script>
</body>
</html>
