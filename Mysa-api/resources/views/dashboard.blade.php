<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mysa API - E-commerce Dashboard V3</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a; --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --primary: #3b82f6; --primary-hover: #2563eb;
            --accent: #10b981; --warning: #f59e0b;
            --text-main: #f8fafc; --text-muted: #94a3b8; --danger: #ef4444;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-color); background-image: radial-gradient(circle at 15% 50%, rgba(59, 130, 246, 0.15), transparent 25%), radial-gradient(circle at 85% 30%, rgba(16, 185, 129, 0.15), transparent 25%); color: var(--text-main); min-height: 100vh; overflow-x: hidden; }
        .glass { background: var(--glass-bg); backdrop-filter: blur(12px); border: 1px solid var(--glass-border); border-radius: 16px; }
        nav { display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; margin: 1rem; position: sticky; top: 1rem; z-index: 100; }
        .logo { font-size: 1.5rem; font-weight: 800; background: -webkit-linear-gradient(45deg, #3b82f6, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .nav-actions { display: flex; gap: 1rem; align-items: center; }
        input, button, select, textarea { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: white; outline: none; transition: all 0.2s; }
        input:focus, textarea:focus { border-color: var(--primary); }
        button { background: var(--primary); border: none; cursor: pointer; font-weight: 600; }
        button:hover { background: var(--primary-hover); transform: translateY(-1px); }
        .btn-danger { background: var(--danger); } .btn-accent { background: var(--accent); } .btn-outline { background: transparent; border: 1px solid var(--primary); }
        main { padding: 2rem; max-width: 1200px; margin: 0 auto; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
        .product-card { padding: 1.5rem; display: flex; flex-direction: column; gap: 0.8rem; transition: transform 0.3s; cursor: pointer; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.2); }
        .product-image { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; background: rgba(0,0,0,0.2); }
        .product-name { font-size: 1.1rem; font-weight: 600; } .product-price { font-size: 1.2rem; color: var(--accent); font-weight: 800; } .product-desc { font-size: 0.85rem; color: var(--text-muted); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .side-panel { position: fixed; top: 0; right: -450px; width: 400px; height: 100vh; padding: 2rem; transition: right 0.3s ease; z-index: 200; display: flex; flex-direction: column; gap: 1.5rem; }
        .side-panel.open { right: 0; }
        .close-btn { background: transparent; color: white; border: 1px solid white; float: right; padding: 0.2rem 0.8rem;}
        .list-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--glass-border); }
        #admin-panel { margin-bottom: 2rem; padding: 1.5rem; display: none; }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 300; display: flex; justify-content: center; align-items: center; opacity: 0; pointer-events: none; transition: opacity 0.3s; }
        .modal-overlay.active { opacity: 1; pointer-events: all; }
        .modal-content { padding: 2rem; width: 500px; max-height: 90vh; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; }
        .modal-content.large { width: 800px; }
        .hidden { display: none !important; }
        .toast { position: fixed; bottom: 20px; right: 20px; padding: 1rem; border-radius: 8px; z-index: 1000; background: var(--accent); color: white; transform: translateY(100px); opacity: 0; transition: all 0.3s; }
        .toast.show { transform: translateY(0); opacity: 1; } .toast.error { background: var(--danger); }
        .review-box { background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .avatar { width: 32px; height: 32px; border-radius: 50%; background: #555; display: inline-block; vertical-align: middle; margin-right: 8px; }
    </style>
</head>
<body>
    <nav class="glass">
        <div class="logo">Mysa Store V3</div>
        <div id="auth-guest" class="nav-actions">
            <input type="email" id="login-email" placeholder="Email" value="khachhang@gmail.com">
            <input type="password" id="login-pass" placeholder="Password" value="123456">
            <button onclick="login()">Đăng Nhập</button>
        </div>
        <div id="auth-user" class="nav-actions hidden">
            <img id="nav-avatar" class="avatar" src="https://ui-avatars.com/api/?name=U" alt="avt">
            <span id="user-name" style="font-weight: 600;"></span>
            <button class="btn-outline" onclick="togglePanel('profile-panel')">👤 Hồ Sơ</button>
            <button class="btn-outline admin-only" onclick="togglePanel('admin-coupons-panel')">🎟 Khuyến Mãi</button>
            <button class="btn-outline admin-only" onclick="togglePanel('orders-panel')">📦 Đơn Hàng</button>
            <button class="btn-accent" onclick="togglePanel('cart-panel')">🛒 Giỏ Hàng</button>
            <button class="btn-danger" onclick="logout()">Thoát</button>
        </div>
    </nav>

    <main>
        <!-- Admin Panel (Products) -->
        <div id="admin-panel" class="glass admin-only">
            <h3 style="color: var(--accent); margin-bottom: 1rem;">🛠️ Thêm Sản Phẩm</h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <input type="text" id="add-name" placeholder="Tên sản phẩm">
                <input type="number" id="add-price" placeholder="Giá tiền">
                <input type="number" id="add-stock" placeholder="Số lượng kho">
                <button class="btn-accent" onclick="addProduct()">Thêm</button>
            </div>
        </div>

        <div class="header-section">
            <h2>Sản phẩm nổi bật</h2>
            <input type="text" id="search-input" placeholder="Tìm kiếm sản phẩm..." oninput="debounceSearch()">
        </div>

        <div id="products-grid" class="products-grid"></div>
        <div id="pagination" style="margin-top: 2rem; display: flex; gap: 0.5rem; justify-content: center;"></div>
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

    <!-- Profile Panel -->
    <div id="profile-panel" class="side-panel glass">
        <h2>Hồ Sơ Cá Nhân <button class="close-btn" onclick="togglePanel('profile-panel')">X</button></h2>
        <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
            <input type="text" id="prof-name" placeholder="Họ và tên">
            <input type="text" id="prof-phone" placeholder="Số điện thoại mặc định">
            <input type="text" id="prof-address" placeholder="Địa chỉ mặc định">
            <input type="text" id="prof-avatar" placeholder="URL Ảnh đại diện">
            <button class="btn-accent" onclick="updateProfile()">Lưu Thay Đổi</button>
        </div>
    </div>

    <!-- Admin Coupons Panel -->
    <div id="admin-coupons-panel" class="side-panel glass">
        <h2>Quản Lý Mã Giảm Giá <button class="close-btn" onclick="togglePanel('admin-coupons-panel')">X</button></h2>
        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
            <input type="text" id="c-code" placeholder="Mã code (VD: TET2024)">
            <input type="number" id="c-amount" placeholder="Mức giảm">
            <select id="c-type"><option value="fixed">Tiền mặt (VND)</option><option value="percent">Phần trăm (%)</option></select>
            <button class="btn-accent" onclick="addCoupon()">Tạo Mã</button>
        </div>
        <div id="coupons-list" style="flex: 1; overflow-y: auto;"></div>
    </div>

    <!-- Checkout Modal -->
    <div id="checkout-modal" class="modal-overlay">
        <div class="modal-content glass">
            <h2 style="color: var(--accent);">Thanh toán</h2>
            <input type="text" id="ship-phone" placeholder="Số điện thoại người nhận">
            <input type="text" id="ship-address" placeholder="Địa chỉ giao hàng">
            
            <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                <input type="text" id="coupon-code" placeholder="Mã giảm giá" style="flex:1;">
                <button onclick="applyCoupon()">Áp dụng</button>
            </div>
            
            <h3 style="margin-top: 1rem;">Tạm tính: <span id="chk-subtotal">0</span></h3>
            <h3 style="color: var(--accent);">Giảm giá: <span id="chk-discount">0</span></h3>
            <h2 style="color: var(--primary);">Tổng thu: <span id="chk-total">0</span></h2>

            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button style="flex: 1;" class="btn-danger" onclick="closeModal('checkout-modal')">Hủy</button>
                <button style="flex: 1;" class="btn-accent" onclick="processCheckout()">Chốt Đơn</button>
            </div>
        </div>
    </div>

    <!-- Product Detail Modal -->
    <div id="detail-modal" class="modal-overlay">
        <div class="modal-content large glass">
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                <h2 id="det-name" style="color: var(--primary);">Tên SP</h2>
                <button class="close-btn" onclick="closeModal('detail-modal')">X</button>
            </div>
            <div style="display: flex; gap: 2rem;">
                <img id="det-img" src="" style="width: 50%; border-radius: 8px; object-fit: cover;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 1rem;">
                    <h2 id="det-price" style="color: var(--accent);">0 đ</h2>
                    <p id="det-rating" style="color: var(--warning); font-weight: 600;">⭐ 5.0</p>
                    <p id="det-desc" style="color: var(--text-muted); line-height: 1.5;"></p>
                    <button class="btn-accent" onclick="addToCart(currentProductId)" style="margin-top: auto;">Thêm vào giỏ</button>
                </div>
            </div>
            <hr style="border-color: var(--glass-border); margin: 1rem 0;">
            <h3>Đánh giá từ khách hàng</h3>
            <div id="det-reviews" style="max-height: 200px; overflow-y: auto; margin-bottom: 1rem;"></div>
            
            <!-- Add review form -->
            <div id="review-form" class="review-box hidden">
                <h4>Viết đánh giá của bạn</h4>
                <select id="rev-rating" style="margin: 0.5rem 0;">
                    <option value="5">⭐⭐⭐⭐⭐ (Tuyệt vời)</option>
                    <option value="4">⭐⭐⭐⭐ (Tốt)</option>
                    <option value="3">⭐⭐⭐ (Bình thường)</option>
                    <option value="2">⭐⭐ (Tệ)</option>
                    <option value="1">⭐ (Rất tệ)</option>
                </select>
                <textarea id="rev-comment" rows="2" placeholder="Chia sẻ cảm nhận..." style="width: 100%; margin-bottom: 0.5rem;"></textarea>
                <input type="hidden" id="rev-id" value="">
                <button class="btn-accent" onclick="submitReview()">Gửi Đánh Giá</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        const API_URL = 'http://127.0.0.1:8000/api';
        let token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user'));
        let searchTimeout = null;
        let currentProductId = null;
        
        let cartTotalRaw = 0;
        let discountRaw = 0;
        let activeCoupon = '';

        const formatMoney = (val) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
        const showToast = (msg, isErr=false) => { const t=document.getElementById('toast'); t.textContent=msg; t.className='toast show '+(isErr?'error':''); setTimeout(()=>t.classList.remove('show'),3000); }
        const togglePanel = (id) => { document.querySelectorAll('.side-panel').forEach(p => p.id===id ? p.classList.toggle('open') : p.classList.remove('open')); if(id==='admin-coupons-panel') loadCoupons(); }
        const closeModal = (id) => document.getElementById(id).classList.remove('active');

        async function apiCall(endpoint, method = 'GET', body = null) {
            const h = { 'Accept': 'application/json', 'Content-Type': 'application/json' };
            if (token) h['Authorization'] = `Bearer ${token}`;
            const opt = { method, headers: h };
            if (body) opt.body = JSON.stringify(body);
            const res = await fetch(`${API_URL}${endpoint}`, opt);
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || data.error || 'Lỗi hệ thống');
            return data;
        }

        function checkAuth() {
            document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'none');
            if (token && user) {
                document.getElementById('auth-guest').classList.add('hidden');
                document.getElementById('auth-user').classList.remove('hidden');
                document.getElementById('user-name').textContent = user.name;
                document.getElementById('nav-avatar').src = user.avatar || `https://ui-avatars.com/api/?name=${user.name}`;
                
                document.getElementById('prof-name').value = user.name;
                document.getElementById('prof-phone').value = user.phone || '';
                document.getElementById('prof-address').value = user.address || '';
                document.getElementById('prof-avatar').value = user.avatar || '';

                if(user.role === 'admin') document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'flex');
                loadCart();
            } else {
                document.getElementById('auth-guest').classList.remove('hidden');
                document.getElementById('auth-user').classList.add('hidden');
            }
        }

        async function login() {
            try {
                const d = await apiCall('/login', 'POST', { email: document.getElementById('login-email').value, password: document.getElementById('login-pass').value });
                token = d.token; user = d.user;
                localStorage.setItem('token', token); localStorage.setItem('user', JSON.stringify(user));
                checkAuth(); showToast(`Xin chào, ${user.name}!`);
            } catch (e) { showToast(e.message, true); }
        }

        function logout() { token = null; user = null; localStorage.clear(); checkAuth(); showToast('Đã đăng xuất'); }

        async function updateProfile() {
            try {
                const d = await apiCall('/profile', 'PUT', {
                    name: document.getElementById('prof-name').value,
                    phone: document.getElementById('prof-phone').value,
                    address: document.getElementById('prof-address').value,
                    avatar: document.getElementById('prof-avatar').value
                });
                user = d.user; localStorage.setItem('user', JSON.stringify(user));
                checkAuth(); showToast('Đã cập nhật hồ sơ!');
            } catch(e){ showToast(e.message, true); }
        }

        async function loadProducts(page = 1, search = '') {
            try {
                const d = await apiCall(`/products?page=${page}&search=${search}`);
                document.getElementById('products-grid').innerHTML = d.data.map(p => `
                    <div class="product-card glass" onclick="viewProduct(${p.id})">
                        ${p.image_url ? `<img src="${p.image_url}" class="product-image">` : `<div class="product-image"></div>`}
                        <div class="product-meta"><span>${p.category?.name||'N/A'}</span><span class="rating">⭐ ${parseFloat(p.reviews_avg_rating||0).toFixed(1)}</span></div>
                        <div class="product-name">${p.name}</div>
                        <div class="product-desc">${p.description||''}</div>
                        <div class="product-price">${formatMoney(p.price)}</div>
                        <button style="margin-top: auto" onclick="event.stopPropagation(); addToCart(${p.id})">Thêm vào giỏ</button>
                    </div>`).join('');
                document.getElementById('pagination').innerHTML = `
                    ${d.current_page > 1 ? `<button onclick="loadProducts(${d.current_page - 1})">Trước</button>` : ''}
                    ${d.current_page < d.last_page ? `<button onclick="loadProducts(${d.current_page + 1})">Tiếp</button>` : ''}`;
            } catch (e) { showToast('Lỗi tải SP: ' + e.message, true); }
        }
        function debounceSearch() { clearTimeout(searchTimeout); searchTimeout = setTimeout(() => loadProducts(1, document.getElementById('search-input').value), 500); }

        async function viewProduct(id) {
            try {
                currentProductId = id;
                const p = await apiCall(`/products/${id}`);
                document.getElementById('det-name').textContent = p.name;
                document.getElementById('det-price').textContent = formatMoney(p.price);
                document.getElementById('det-rating').textContent = `⭐ ${parseFloat(p.reviews_avg_rating||0).toFixed(1)} (${p.reviews.length} đánh giá)`;
                document.getElementById('det-desc').textContent = p.description || 'Không có mô tả.';
                document.getElementById('det-img').src = p.image_url || '';
                
                let myReview = null;
                document.getElementById('det-reviews').innerHTML = p.reviews.length===0?'<p>Chưa có đánh giá.</p>':p.reviews.map(r => {
                    if(user && r.user_id === user.id) myReview = r;
                    return `
                    <div class="review-box">
                        <div style="display:flex; justify-content:space-between;">
                            <strong><img src="${r.user?.avatar||'https://ui-avatars.com/api/?name=U'}" class="avatar" style="width:20px;height:20px"> ${r.user?.name}</strong>
                            <span style="color:var(--warning)">${'⭐'.repeat(r.rating)}</span>
                        </div>
                        <p style="margin-top:0.5rem">${r.comment||''}</p>
                        ${user && (r.user_id === user.id || user.role === 'admin') ? `<button class="btn-danger" style="padding: 2px 5px; font-size: 0.7rem; margin-top: 5px;" onclick="deleteReview(${r.id})">Xóa</button>` : ''}
                    </div>`;
                }).join('');

                if(token) {
                    document.getElementById('review-form').classList.remove('hidden');
                    if(myReview) {
                        document.getElementById('rev-rating').value = myReview.rating;
                        document.getElementById('rev-comment').value = myReview.comment;
                        document.getElementById('rev-id').value = myReview.id;
                    } else {
                        document.getElementById('rev-rating').value = '5';
                        document.getElementById('rev-comment').value = '';
                        document.getElementById('rev-id').value = '';
                    }
                } else {
                    document.getElementById('review-form').classList.add('hidden');
                }
                
                document.getElementById('detail-modal').classList.add('active');
            } catch(e) { showToast(e.message, true); }
        }

        async function submitReview() {
            try {
                const rid = document.getElementById('rev-id').value;
                const body = { product_id: currentProductId, rating: document.getElementById('rev-rating').value, comment: document.getElementById('rev-comment').value };
                if(rid) await apiCall(`/reviews/${rid}`, 'PUT', body);
                else await apiCall('/reviews', 'POST', body);
                showToast('Gửi đánh giá thành công!');
                viewProduct(currentProductId); loadProducts();
            } catch(e) { showToast(e.message, true); }
        }
        async function deleteReview(id) {
            try { await apiCall(`/reviews/${id}`, 'DELETE'); showToast('Đã xóa'); viewProduct(currentProductId); loadProducts(); } catch(e){ showToast(e.message, true); }
        }

        // CART & CHECKOUT
        async function loadCart() {
            if(!token) return;
            const d = await apiCall('/cart');
            cartTotalRaw = 0; const items = d.items || [];
            document.getElementById('cart-items').innerHTML = items.length === 0 ? '<p>Giỏ trống</p>' : items.map(i => {
                cartTotalRaw += i.product.price * i.quantity;
                return `<div class="list-item"><div><strong>${i.product.name}</strong><br><small>${formatMoney(i.product.price)} x ${i.quantity}</small></div><button class="btn-danger" onclick="removeFromCart(${i.id})">X</button></div>`;
            }).join('');
            document.getElementById('cart-total').textContent = formatMoney(cartTotalRaw);
        }
        async function addToCart(pid) { if(!token) return showToast('Vui lòng đăng nhập!', true); try { await apiCall('/cart', 'POST', {product_id:pid, quantity:1}); showToast('Đã thêm'); loadCart(); } catch(e){showToast(e.message,true);} }
        async function removeFromCart(id) { await apiCall(`/cart/${id}`, 'DELETE'); loadCart(); }

        function openCheckoutModal() {
            if(!token || cartTotalRaw === 0) return showToast('Giỏ hàng trống!', true);
            document.getElementById('ship-phone').value = user.phone || '';
            document.getElementById('ship-address').value = user.address || '';
            document.getElementById('coupon-code').value = '';
            discountRaw = 0; activeCoupon = '';
            updateCheckoutUI();
            document.getElementById('checkout-modal').classList.add('active');
        }
        
        async function applyCoupon() {
            const code = document.getElementById('coupon-code').value;
            if(!code) return;
            try {
                const res = await apiCall('/coupons/verify', 'POST', {code});
                activeCoupon = code;
                const c = res.coupon;
                if(c.discount_type === 'percent') discountRaw = (cartTotalRaw * c.discount_amount) / 100;
                else discountRaw = c.discount_amount;
                showToast('Áp dụng mã thành công!');
                updateCheckoutUI();
            } catch(e) { showToast(e.message, true); activeCoupon=''; discountRaw=0; updateCheckoutUI(); }
        }

        function updateCheckoutUI() {
            document.getElementById('chk-subtotal').textContent = formatMoney(cartTotalRaw);
            document.getElementById('chk-discount').textContent = '-' + formatMoney(discountRaw);
            document.getElementById('chk-total').textContent = formatMoney(Math.max(0, cartTotalRaw - discountRaw));
        }

        async function processCheckout() {
            try {
                const body = { phone: document.getElementById('ship-phone').value, shipping_address: document.getElementById('ship-address').value };
                if(activeCoupon) body.coupon_code = activeCoupon;
                await apiCall('/orders', 'POST', body);
                showToast('🚀 Chốt đơn thành công!');
                closeModal('checkout-modal'); togglePanel('cart-panel');
                loadCart(); loadProducts(); loadOrders();
            } catch (e) { showToast(e.message, true); }
        }

        // ADMIN ORDERS
        async function loadOrders() {
            if(!token) return;
            try {
                const o = await apiCall('/orders');
                document.getElementById('orders-list').innerHTML = o.length===0?'<p>Trống.</p>':o.map(i => `
                    <div class="list-item" style="flex-direction:column; align-items:flex-start;">
                        <div style="width:100%; display:flex; justify-content:space-between;"><strong>Đơn #${i.id}</strong><span style="color:var(--accent)">${i.status.toUpperCase()}</span></div>
                        <small>SĐT: ${i.phone}<br>Đ/C: ${i.shipping_address}<br>Tổng: ${formatMoney(i.total_amount)}</small>
                        ${user.role==='admin'&&i.status!=='delivered'?`<select onchange="apiCall('/orders/${i.id}','PUT',{status:this.value}).then(()=>loadOrders())"><option value="pending" ${i.status==='pending'?'selected':''}>Chờ xử lý</option><option value="shipped" ${i.status==='shipped'?'selected':''}>Đang giao</option><option value="delivered">Đã giao</option></select>`:''}
                    </div>`).join('');
            } catch(e) {}
        }

        // ADMIN COUPONS
        async function loadCoupons() {
            try {
                const c = await apiCall('/coupons');
                document.getElementById('coupons-list').innerHTML = c.map(i => `
                    <div class="list-item">
                        <div><strong>${i.code}</strong> <small>(${i.used_count}/${i.usage_limit||'∞'})</small><br><small style="color:var(--accent)">Giảm ${i.discount_amount} ${i.discount_type==='percent'?'%':'đ'}</small></div>
                        <button class="btn-danger" onclick="apiCall('/coupons/${i.id}','DELETE').then(()=>loadCoupons())">Xóa</button>
                    </div>`).join('');
            } catch(e){}
        }
        async function addCoupon() {
            try {
                await apiCall('/coupons', 'POST', { code: document.getElementById('c-code').value, discount_amount: document.getElementById('c-amount').value, discount_type: document.getElementById('c-type').value, usage_limit: 100 });
                showToast('Tạo mã thành công'); loadCoupons();
            } catch(e) { showToast(e.message, true); }
        }

        checkAuth(); loadProducts();
    </script>
</body>
</html>
