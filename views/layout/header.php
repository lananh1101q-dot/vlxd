<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Quản lý Kho VLXD'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #1e3a5f, #0d2137); height: 100vh; position: fixed; width: 250px; color: white; padding-top: 20px; z-index: 1000; overflow-y: auto; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8) !important; padding: 12px 20px; border-radius: 5px; margin: 4px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,0.1); transform: translateX(5px); color: white !important; }
        .sidebar .nav-link.active { background: rgba(255,255,255,0.2) !important; color: white !important; font-weight: bold; }
        .main-content { margin-left: 250px; padding: 30px; min-height: 100vh; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .table thead { background-color: #f1f3f5; }
        .chip { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; background: #e8f0fe; color: #1967d2; }
        .btn-action { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; margin: 0 2px; }
    </style>
    <script>
        // Check if user is logged in
        const token = localStorage.getItem('token');
        if (!token && window.location.pathname.indexOf('dangnhap') === -1) {
            window.location.href = 'dangnhap';
        }
    </script>
</head>
<body>
