<?php
if (isset($_SESSION['USER'])) {
    if ($USER['role'] == 2) {

        // Обработка принятия заказа
        if (isset($_POST['accept_order'])) {
            $order_id = $_POST['order_id'];
            $sql = "UPDATE orders SET status = 'принят' WHERE id = ?";
            $stmt = $connect->prepare($sql);
            $stmt->execute([$order_id]);

            echo '<script>document.location.href="?page=adminZakaz"</script>';
        }

        // Обработка удаления заказа
        if (isset($_POST['delete_order'])) {
            $order_id = $_POST['order_id'];
            $sql = "DELETE FROM orders WHERE id = ?";
            $stmt = $connect->prepare($sql);
            $stmt->execute([$order_id]);

            echo '<script>document.location.href="?page=adminZakaz"</script>';
        }
?>
        <div class="personal-cabinet-wrapper mt80">
            <div class="container">
                <div class="personal-cabinet">
                    <nav class="sidebar">
                        <div class="user-info">
                            <div class="avatar">
                                <img src="<?= $USER['img'] ?>" alt="">
                            </div>
                            <div class="user-name">Админ</div>
                        </div>

                        <div class="nav-menu">
                            <a href="?page=adminLk" class="nav-item">профиль</a>
                            <a href="?page=adminPh" class="nav-item">фотографы</a>
                            <a href="?page=adminCertificate" class="nav-item">сертификаты</a>
                            <a href="?page=adminPhotos" class="nav-item">фотосессии</a>
                            <a href="?page=adminPackets" class="nav-item">пакеты фотосессий</a>
                            <a href="?page=adminCategory" class="nav-item">категории фотосессий</a>
                            <a href="?page=adminZakaz" class="nav-item">заказы</a>
                            <a href="?page=adminUsers" class="nav-item">пользователи</a>
                            <a href="?exit" class="nav-item">выйти</a>
                        </div>
                    </nav>

                    <div class="cabinet-content">
                        <div class="profile-form photogr">
                            <div class="btns-portfolio">
                                <h2 class="title">заказы</h2>
                            </div>
                            <div class="cards-zapisi-user m20 zakazi-admin">
                                <?php
                                // Получаем все заказы с информацией о пользователях
                                $sql = "SELECT o.*, u.name, u.surname, u.phone 
                                FROM orders o 
                                JOIN user u ON o.user_id = u.id 
                                ORDER BY o.id DESC";
                                $stmt = $connect->prepare($sql);
                                $stmt->execute();
                                $orders = $stmt->fetchAll();

                                if (empty($orders)) {
                                    echo '<div class="empty-cart-message">Заказов пока нет</div>';
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

                                        // Определяем цвет статуса
                                        $status_color = 'gray-lk';
                                        if ($order['status'] == 'принят') {
                                            $status_color = 'status_gotov';
                                        } elseif ($order['status'] == 'отклонен' || $order['status'] == 'отменен') {
                                            $status_color = 'status_otdan';
                                        }
                                ?>
                                        <div class="card-zapisi-user">
                                            <div class="info-zapis-user">
                                                <div class="info-double gray-lk">
                                                    <p class="photogr">
                                                        имя: <span><?= htmlspecialchars($order['name'] . ' ' . $order['surname']) ?></span>
                                                    </p>
                                                    <p class="photogr-price">
                                                        телефон: <span><?= htmlspecialchars($order['phone']) ?></span>
                                                    </p>
                                                </div>
                                                <div class="info-double">
                                                    <p class="photogr">
                                                        номер заказа: <span><?= $order['id'] ?></span>
                                                    </p>
                                                </div>
                                                <div class="info-double">
                                                    <p class="photogr">
                                                        цена: <span><?= number_format($order['price'], 0, '', ' ') ?> ₽</span>
                                                    </p>
                                                </div>
                                                <div class="order_composition_col">
                                                    <details class="order-composition">
                                                        <summary>состав заказа</summary>
                                                        <div class="order-items">
                                                            <ul>
                                                                <?php foreach ($order_items as $item) { ?>
                                                                    <li><?= htmlspecialchars($item['name']) ?> (<?= $item['count'] ?> шт.) - <?= number_format($item['price'] * $item['count'], 0, '', ' ') ?> ₽</li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </details>
                                                </div>
                                                <p class="status-photo">
                                                    статус: <span class="<?= $status_color ?>"><?= $order['status'] ?></span>
                                                </p>
                                            </div>
                                            <div class="btns-column-edit-delete">
                                                <?php if ($order['status'] != 'принят') { ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                        <input type="submit" name="accept_order" value="принять" class="btn btn-no-back edit green btn-hover-red-wh-fon">
                                                    </form>
                                                <?php } ?>

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

<?php
    } else {
        echo '<script>document.location.href="?page=error403"</script>';
    }
} else {
    echo '<script>document.location.href="?page=error403"</script>';
}
?>