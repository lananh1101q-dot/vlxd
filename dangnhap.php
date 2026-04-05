<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Đăng nhập — Hệ thống Quản lý kho</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: radial-gradient(ellipse at center, rgba(17,24,39,0.95) 0%, #080808 60%);
      min-height: 100vh;
    }
  </style>
</head>
<body class="antialiased text-gray-100">

  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-xl">
      <div class="bg-slate-900/60 backdrop-blur-md border border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
        <div class="md:flex">
          <div class="w-full p-8 md:p-10">
            <h1 class="text-center text-2xl font-semibold text-white">Quản Lý Kho Hàng</h1>
            <p class="text-center text-slate-300 mt-2 font-medium text-lg">Đăng nhập hệ thống (Plaintext)</p>

            <div id="errorMsg" class="mt-4 bg-red-900/60 border border-red-700 text-red-200 px-4 py-3 rounded hidden"></div>

            <form id="loginForm" class="mt-6 space-y-4">
              <div>
                <label class="block text-sm text-slate-300 mb-2">Tên đăng nhập <span class="text-red-400">*</span></label>
                <input required id="username" type="text" placeholder="Nhập tên đăng nhập"
                  class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500" />
              </div>

              <div>
                <label class="block text-sm text-slate-300 mb-2">Mật khẩu <span class="text-red-400">*</span></label>
                <input required id="password" type="password" placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500" />
              </div>

              <div class="pt-4">
                <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-slate-900 font-semibold px-4 py-3 rounded-lg shadow">
                  Đăng nhập
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const u = document.getElementById('username').value.trim();
    const p = document.getElementById('password').value.trim();
    const errorDiv = document.getElementById('errorMsg');

    errorDiv.classList.add('hidden');

    // ❗ kiểm tra rỗng
    if (!u || !p) {
        errorDiv.textContent = "Vui lòng nhập đầy đủ thông tin!";
        errorDiv.classList.remove('hidden');
        return;
    }

    try {
        const response = await fetch('http://localhost:8000/api/v1/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                Tendangnhap: u, 
                Matkhau: p 
            })
        });

        const result = await response.json();

        // ❗ nếu login thành công
        if (result.success) {
    const user = result.data.user;

    // 🔥 THÊM DÒNG NÀY
    localStorage.setItem('token', result.data.token);

    // Gửi session
    const formData = new FormData();
    formData.append("user", JSON.stringify(user));

    await fetch("set_session.php", {
        method: "POST",
        body: formData,
        credentials: "same-origin"
    });

    window.location.href = "trangchu.php";
}
        // ❗ nếu sai tài khoản
        else {
            errorDiv.textContent = result.message || "Sai tài khoản hoặc mật khẩu!";
            errorDiv.classList.remove('hidden');
        }

    } catch (error) {
        errorDiv.textContent = "Không kết nối được server (API 8000)";
        errorDiv.classList.remove('hidden');
    }
});
</script>
</body>
</html>