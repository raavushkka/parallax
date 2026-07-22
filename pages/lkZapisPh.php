<?
if (isset($_SESSION['USER'])) {
    if ($USER['role'] == 3) {
?>
        <div class="personal-cabinet-wrapper mt80">
            <div class="container">
                <div class="personal-cabinet">
                    <nav class="sidebar">
                        <div class="user-info">
                            <div class="avatar">
                                <img src="<?= $USER['img'] ?>" alt="<?= $USER['name'] ?>">
                            </div>
                            <div class="user-name"><?= $USER['name'] ?></div>
                        </div>

                        <div class="nav-menu">
                            <a href="?page=lkPh" class="nav-item">профиль</a>
                            <a href="?page=lkPortfolioPh" class="nav-item">портфолио</a>
                            <a href="?page=lkZapisPh" class="nav-item">записи</a>
                            <a href="?exit" class="nav-item">выйти</a>
                        </div>
                    </nav>

                    <div class="cabinet-content">
                        <div class="profile-form">
                            <h2 class="title">записи</h2>

                            <div class="cards-zapisi-user m20">
                                <?php
                                // Получаем записи для текущего фотографа
                                $sql = "SELECT z.*, 
                                u.name as user_name, 
                                u.phone as user_phone,
                                f.name as fs_name,
                                p.name as packet_name,
                                p.price as packet_price,
                                c.name as connection_name
                                FROM zapis z
                                JOIN user u ON z.user_id = u.id
                                JOIN fs f ON z.fs_id = f.id
                                JOIN packets p ON z.packet_id = p.id
                                JOIN connectionUser c ON z.connection = c.id
                                WHERE f.photogs_id = ?
                                ORDER BY z.id DESC";
                                $stmt = $connect->prepare($sql);
                                $stmt->execute([$USER['id']]);
                                $zapisi = $stmt->fetchAll();

                                if (empty($zapisi)) {
                                    echo '<div class="empty-cart-message">У вас пока нет записей.<br>Записи появятся здесь когда клиенты запишутся к вам!</div>';
                                } else {
                                    foreach ($zapisi as $zapis) {
                                        // Форматируем дату, время, локацию и статус
                                        $date_display = ($zapis['date'] == 'появится позже' || empty($zapis['date'])) ? 'появится позже' : $zapis['date'];
                                        $time_display = ($zapis['time'] == 'появится позже' || empty($zapis['time'])) ? 'появится позже' : $zapis['time'];
                                        $location_display = ($zapis['location'] == 'появится позже' || empty($zapis['location'])) ? 'появится позже' : $zapis['location'];
                                        $status_display = ($zapis['status_photos'] == 'в обработке' || empty($zapis['status_photos'])) ? 'появится позже' : $zapis['status_photos'];
                                ?>
                                        <div class="card-zapisi-user">
                                            <div class="info-zapis-user">
                                                <div class="info-double gray-lk">
                                                    <p class="photogr">
                                                        имя: <span><?= htmlspecialchars($zapis['user_name']) ?></span>
                                                    </p>
                                                    <p class="photogr-price">
                                                        телефон: <span><?= htmlspecialchars($zapis['user_phone']) ?></span>
                                                    </p>
                                                    <p class="photogr-price">
                                                        способ связи: <span><?= htmlspecialchars($zapis['connection_name']) ?></span>
                                                    </p>
                                                </div>
                                                <p class="name-foto">
                                                    Фотосессия «<?= htmlspecialchars($zapis['fs_name']) ?>»
                                                </p>
                                                <div class="info-double">
                                                    <p class="photogr">
                                                        фотограф: <span><?= htmlspecialchars($USER['name']) ?></span>
                                                    </p>
                                                    <p class="photogr">
                                                        пакет: <span><?= htmlspecialchars($zapis['packet_name']) ?></span>
                                                    </p>
                                                    <p class="photogr-price">
                                                        цена: <span><?= number_format($zapis['packet_price'], 0, '', ' ') ?> ₽</span>
                                                    </p>
                                                </div>
                                                <div class="info-double gray-lk">
                                                    <p class="photogr">
                                                        дата: <span><?= htmlspecialchars($date_display) ?></span>
                                                    </p>
                                                    <p class="photogr-price">
                                                        время: <span><?= htmlspecialchars($time_display) ?></span>
                                                    </p>
                                                    <p class="photogr-price">
                                                        локация: <span><?= htmlspecialchars($location_display) ?></span>
                                                    </p>
                                                </div>
                                                <p class="status-photo">
                                                    статус фотографий: <span class="gray-lk"><?= htmlspecialchars($status_display) ?></span>
                                                </p>
                                                <a href="?page=editZapis&id=<?= $zapis['id'] ?>">
                                                    <input type="submit" value="редактировать" class="btn btn-no-back edit">
                                                </a>
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