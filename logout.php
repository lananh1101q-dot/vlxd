<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng xuất</title>
</head>
<body>
<script>
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = 'dangnhap.php';
</script>
</body>
</html>