<?php
session_start();
require 'db.php';

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kullanıcı bilgilerini al
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

// Yeni görev ekleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task'])) {
    $task = $_POST['task'];
    $description = $_POST['description'];

    // Yeni görevi veritabanına ekle
    $stmt = $pdo->prepare("INSERT INTO todos (user_id, task, description) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $task, $description]);
}

// Filtreleme için parametre
$filter = 'all';
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
}

// Görevleri listele
$query = "SELECT * FROM todos WHERE user_id = ?";

if ($filter === 'completed') {
    $query .= " AND is_completed = 1";
} elseif ($filter === 'ongoing') {
    $query .= " AND is_completed = 0";
} elseif ($filter === 'monthly') {
    $query .= " AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();

// Görev tamamlama
if (isset($_GET['mark_complete'])) {
    $task_id = $_GET['mark_complete'];
    $stmt = $pdo->prepare("UPDATE todos SET is_completed = 1, completed_at = NOW() WHERE id = ?");
    $stmt->execute([$task_id]);
    header('Location: todo_list.php');
    exit();
}

// Görev silme
if (isset($_GET['delete_task'])) {
    $task_id = $_GET['delete_task'];
    $stmt = $pdo->prepare("DELETE FROM todos WHERE id = ?");
    $stmt->execute([$task_id]);
    header('Location: todo_list.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yapılacaklar Listesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style2.css">

</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4 table table-bordered">Yapılacaklar Listesi</h1>

        <!-- Yeni görev ekleme formu -->
        <form method="POST" class="task-form mb-4">
            <div class="mb-3">
                <input type="text" name="task" class="form-control" placeholder="Yeni Görev" required>
            </div>
            <div class="mb-3">
                <textarea name="description" class="form-control" placeholder="Açıklama"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Görev Ekle</button>
        </form>

        <!-- Filtreleme -->
        <div class="mb-4 navbar">
            <a href="?filter=all" class="btn btn-outline-secondary">Tüm Görevler</a>
            <a href="?filter=completed" class="btn btn-outline-success">Tamamlanmışlar</a>
            <a href="?filter=ongoing" class="btn btn-outline-warning">Devam Edenler</a>
            <a href="?filter=monthly" class="btn btn-outline-info">Aylık Görünüm</a>
        </div>

        <h3>Görevler</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Görev</th>
                        <th>Açıklama</th>
                        <th>Oluşturulma Tarihi</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['task']); ?></td>
                            <td><?php echo htmlspecialchars($task['description']); ?></td>
                            <td><?php echo date("d-m-Y H:i", strtotime($task['created_at'])); ?></td>
                            <td>
                                <?php if ($task['is_completed']): ?>
                                    <span class="badge status-completed">Tamamlandı</span>
                                <?php else: ?>
                                    <span class="badge status-ongoing">Devam Ediyor</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$task['is_completed']): ?>
                                    <a href="?mark_complete=<?php echo $task['id']; ?>" class="btn btn-success btn-sm">Tamamla</a>
                                <?php endif; ?>
                                <a href="?delete_task=<?php echo $task['id']; ?>" class="btn btn-danger btn-sm">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>