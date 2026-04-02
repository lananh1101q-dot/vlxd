<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title>Dashboard — Client Fetch API</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 antialiased p-8">

  <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <button id="logoutBtn" class="bg-red-500 text-white px-4 py-2 rounded">Đăng xuất</button>
    </div>

    <p id="welcomeMsg" class="mb-4 text-emerald-600 font-medium"></p>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-blue-50 border border-blue-200 p-4 rounded">
            <h2 class="font-semibold text-lg mb-2">Người dùng (Từ User Service qua API Gateway)</h2>
            <ul id="userList" class="list-disc pl-5 text-sm text-gray-700">Đang tải...</ul>
        </div>
        
        <div class="bg-green-50 border border-green-200 p-4 rounded">
            <h2 class="font-semibold text-lg mb-2">Sản phẩm (Từ Product Service)</h2>
            <ul id="productList" class="list-disc pl-5 text-sm text-gray-700">Đang tải...</ul>
        </div>
    </div>
  </div>

<script>
  // Auth check
  const token = localStorage.getItem('token');
  const user = JSON.parse(localStorage.getItem('user') || 'null');
  
  if (!token || !user) {
      window.location.href = 'dangnhap.php';
  } else {
      document.getElementById('welcomeMsg').textContent = 'Xin chào, ' + user.Hovaten + ' (' + user.Tendangnhap + ')';
  }
  
  // Logout
  document.getElementById('logoutBtn').addEventListener('click', () => {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = 'dangnhap.php';
  });

  // Call API Gateway cho Users (Port 8000 -> 3001)
  fetch('http://localhost:8000/api/v1/users', {
      headers: { 'Authorization': 'Bearer ' + token }
  })
  .then(res => res.json())
  .then(data => {
      const ul = document.getElementById('userList');
      ul.innerHTML = '';
      if(data.success && data.data.users) {
          data.data.users.forEach(u => {
              const li = document.createElement('li');
              li.textContent = `${u.Tendangnhap} - ${u.Hovaten}`;
              ul.appendChild(li);
          });
      } else {
          ul.innerHTML = '<li class="text-red-500">Lỗi: ' + (data.message || 'Không thể tải') + '</li>';
      }
  })
  .catch(err => {
      document.getElementById('userList').innerHTML = '<li class="text-red-500">Lỗi kết nối API User</li>';
  });

  // Call API Gateway cho Products (Port 8000 -> 3002)
  fetch('http://localhost:8000/api/v1/products', {
      headers: { 'Authorization': 'Bearer ' + token }
  })
  .then(res => res.json())
  .then(data => {
      const ul = document.getElementById('productList');
      ul.innerHTML = '';
      if(data.success && data.data.products) {
          data.data.products.forEach(p => {
              const li = document.createElement('li');
              li.textContent = `${p.Masp} - ${p.Tensp} - ${p.Giaban} đ`;
              ul.appendChild(li);
          });
      } else {
          ul.innerHTML = '<li class="text-red-500">Lỗi: ' + (data.message || 'Không thể tải') + '</li>';
      }
  })
  .catch(err => {
      document.getElementById('productList').innerHTML = '<li class="text-red-500">Lỗi kết nối API Product</li>';
  });

</script>
</body>
</html>
