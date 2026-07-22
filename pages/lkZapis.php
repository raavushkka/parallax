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
                            <h2 class="title m0">Записи</h2>
                            <div class="cards-zapisi-user">
                                <?php
                                // Проверяем авторизацию
                                if (!isset($_SESSION['USER'])) {
                                    echo '<p>Для просмотра записей необходимо авторизоваться</p>';
                                } else {
                                    $user_id = $_SESSION['USER'];

                                    // Получаем записи пользователя с объединением таблиц
                                    $sql = "SELECT 
                            z.*,
                            fs.name as session_name,
                            fs.price as session_price,
                            u_photog.name as photographer_name,
                            p.name as packet_name,
                            p.price as packet_price,
                            cu.name as connection_name
                        FROM zapis z
                        LEFT JOIN fs ON z.fs_id = fs.id
                        LEFT JOIN user u_photog ON fs.photogs_id = u_photog.id
                        LEFT JOIN packets p ON z.packet_id = p.id
                        LEFT JOIN connectionUser cu ON z.connection = cu.id
                        WHERE z.user_id = ?
                        ORDER BY z.id DESC";

                                    $stmt = $connect->prepare($sql);
                                    $stmt->execute([$user_id]);
                                    $zapisi = $stmt->fetchAll();

                                    if (empty($zapisi)) {
                                        echo '<p>У вас пока нет записей на фотосессии</p>';
                                    } else {
                                        foreach ($zapisi as $zapis) {
                                            // Форматируем цену
                                            $price = !empty($zapis['packet_price']) ? $zapis['packet_price'] : $zapis['session_price'];
                                            $formatted_price = number_format($price, 0, '', ' ') . ' ₽';
                                ?>
                                            <div class="card-zapisi-user">
                                                <div class="info-zapis-user">
                                                    <p class="name-foto">
                                                        Фотосессия «<?= htmlspecialchars($zapis['session_name']) ?>»
                                                    </p>
                                                    <div class="info-double">
                                                        <p class="photogr">
                                                            фотограф: <span><?= htmlspecialchars($zapis['photographer_name']) ?></span>
                                                        </p>
                                                        <p class="photogr">
                                                            пакет: <span><?= htmlspecialchars($zapis['packet_name']) ?></span>
                                                        </p>
                                                        <p class="photogr-price">
                                                            цена: <span><?= $formatted_price ?></span>
                                                        </p>
                                                    </div>
                                                    <div class="info-double gray-lk">
                                                        <p class="photogr">
                                                            дата: <span><?= htmlspecialchars($zapis['date']) ?></span>
                                                        </p>
                                                        <p class="photogr-price">
                                                            время: <span><?= htmlspecialchars($zapis['time']) ?></span>
                                                        </p>
                                                    </div>
                                                    <div class="info-double gray-lk">
                                                        <p class="photogr">
                                                            способ связи: <span><?= htmlspecialchars($zapis['connection_name']) ?></span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <p class="status-photo">
                                                    статус фотографий: <br>
                                                    <span class="gray-lk"><?= htmlspecialchars($zapis['status_photos']) ?></span>
                                                </p>
                                            </div>
                                <?php
                                        }
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