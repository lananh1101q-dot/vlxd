<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: dangnhap.php');
    exit;
}
require_once __DIR__ . '/db.php';

$sql = "SELECT tk.Makho, k.Tenkho, tk.Masp, sp.Tensp, sp.Dvt, tk.Soluongton
        FROM Tonkho_sp tk
        JOIN Kho k ON tk.Makho = k.Makho
        JOIN Sanpham sp ON tk.Masp = sp.Masp
        ORDER BY k.Tenkho, sp.Tensp";
$rows = $pdo->query($sql)->fetchAll();
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Báo cáo tồn kho thành phẩm</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
  .main-content { margin: 0 auto; max-width: 1080px; padding: 20px; }
  .table-wrapper { background: white; border-radius: 8px; border: 1px solid #e2e8f0; padding: 16px; }
  </style>
</head>
<body>
<div class="main-content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3">Báo cáo tồn kho thành phẩm</h1>
      <p class="text-muted">Chỉ hiển thị tồn kho sản phẩm trong Tonkho_sp.</p>
    </div>
    <div>
      <a class="btn btn-secondary me-2" href="tonkho.php">Tổng hợp tồn kho</a>
      <a class="btn btn-outline-primary" href="tonkho_nvl.php">Sang tồn NVL</a>
    </div>
  </div>

  <div class="table-wrapper">
    <table class="table table-striped table-bordered">
      <thead class="table-dark">
      <tr>
        <th>Kho</th>
        <th>Mã sản phẩm</th>
        <th>Tên sản phẩm</th>
        <th>ĐVT</th>
        <th class="text-end">Số lượng tồn</th>
      </tr>
      </thead>
      <tbody>
      <?php if (empty($rows)): ?>
        <tr><td colspan="5" class="text-center">Không có dữ liệu tồn kho thành phẩm.</td></tr>
      <?php else: ?>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td>[<?= htmlspecialchars($r['Makho']) ?>] <?= htmlspecialchars($r['Tenkho']) ?></td>
            <td><?= htmlspecialchars($r['Masp']) ?></td>
            <td><?= htmlspecialchars($r['Tensp']) ?></td>
            <td><?= htmlspecialchars($r['Dvt']) ?></td>
            <td class="text-end"><?= number_format($r['Soluongton'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
