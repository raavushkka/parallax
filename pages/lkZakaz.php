<?
if (isset($_SESSION['USER'])) {
    if ($USER['role'] == 1) {
?>
        <div class="personal-cabinet-wrapper mt80">
            <div class="container">
                <div class="personal-cabinet">
                    <nav class="sidebar">
                        <div class="user-info">
                            <div class="avatar">
                                <img src="<?= $USER['img'] ?>" alt="фото профиля">
                            </div>
                            <div class="user-name"><?= $USER['name'] ?></div>
                        </div>

                        <div class="nav-menu">
                            <a href="?page=lk" class="nav-item">профиль</a>
                            <a href="?page=lkZapis" class="nav-item">записи</a>
                            <a href="?page=lkZakaz" class="nav-item">заказы</a>
                            <a href="?exit" class="nav-item">выйти</a>
                        </div>
                    </nav>

                    <div class="cabinet-content">
                        <div class="profile-form">
                            <h2 class="title">Заказы</h2>
                            <div class="cards_zapisi">
                                <?php
                                // Используем ID из сессии USER
                                $current_user_id = $_SESSION['USER'];

                                // Получаем заказы пользователя
                                $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC";
                                $stmt = $connect->prepare($sql);
                                $stmt->execute([$current_user_id]);
                                $orders = $stmt->fetchAll();

                                if (empty($orders)) {
                                    echo '<p>У вас еще нет заказов.<br>После оформления заказа он появится здесь...</p>';
                                } else {
                                    foreach ($orders as $order) {
                                        // Получаем состав заказа
                                        $sql_items = "SELECT oi.*, c.name, c.price 
                                             FROM order_item oi 
                                             JOIN certificate c ON oi.certificate_id = c.id 
                                             WHERE oi.order_id = ?";
                                        $stmt_items = $connect->prepare($sql_items);
                                        $stmt_items->execute([$order['id']]);
                                        $order_items = $stmt_items->fetchAll();

                                        // Определяем класс статуса для стилизации
                                        $status_class = 'status_zapis';
                                        if ($order['status'] == 'принят' || $order['status'] == 'выполнен' || $order['status'] == 'готов') {
                                            $status_class = 'status_gotov';
                                        } elseif ($order['status'] == 'отклонен' || $order['status'] == 'отменен') {
                                            $status_class = 'status_otdan';
                                        }
                                ?>
                                        <div class="card_zapis">
                                            <div class="order_row">
                                                <div class="order_composition_col">
                                                    <details class="order-composition">
                                                        <summary>состав заказа #<?= $order['id'] ?></summary>
                                                        <div class="order-items">
                                                            <ul>
                                                                <?php foreach ($order_items as $item) { ?>
                                                                    <li><?= htmlspecialchars($item['name']) ?> (<?= $item['count'] ?> шт.) - <?= number_format($item['price'] * $item['count'], 0, '', ' ') ?> ₽</li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </details>
                                                </div>
                                                <div class="order_status_col">
                                                    <p class="status_zapis">
                                                        статус: <span class="<?= $status_class ?>"><?= $order['status'] ?></span>
                                                    </p>
                                                </div>
                                                <div class="order_price_col">
                                                    <p class="actual_zapis">
                                                        <?= number_format($order['price'], 0, '', ' ') ?> ₽
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?
    } else {
        echo '<script>document.location.href="?page=error403"</script>';
    }
} else {
    echo '<script>document.location.href="?page=error403"</script>';
}
?>