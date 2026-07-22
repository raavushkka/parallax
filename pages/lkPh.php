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
                            <h2 class="title">профиль фотографа</h2>
                            <a href="?page=editLkPh"><input type="submit" value="редактировать" class="btn btn-no-back edit"></a>

                            <div class="photographer-page m20">
                                <div class="photographer-header">
                                    <!-- Левая часть - фото -->
                                    <div class="photographer-image">
                                        <img src="<?= $USER['img'] ?>" alt="<?= $USER['name'] ?>">
                                    </div>

                                    <!-- Правая часть - вся информация -->
                                    <div class="photographer-info">
                                        <?php
                                        // Получаем дополнительные данные фотографа
                                        $sql = "SELECT * FROM photogs WHERE id = ?";
                                        $stmt = $connect->prepare($sql);
                                        $stmt->execute([$USER['id']]);
                                        $photographer_data = $stmt->fetch();
                                        ?>

                                        <h1 class="photographer-name"><?= $USER['name'] ?></h1>
                                        <p class="photographer-desc">
                                            <?= htmlspecialchars($photographer_data['desc']) ?>
                                        </p>

                                        <div class="divider"></div>

                                        <div class="achievements">
                                            <div class="achievement-item">
                                                <div class="achievement-number"><?= $photographer_data['experience'] ?></div>
                                                <p class="achievement-text">лет опыта</p>
                                            </div>
                                            <div class="achievement-item">
                                                <div class="achievement-number"><?= $photographer_data['projects'] ?>+</div>
                                                <p class="achievement-text">проектов</p>
                                            </div>
                                            <div class="achievement-item">
                                                <div class="achievement-number"><?= $photographer_data['exhibitions'] ?></div>
                                                <p class="achievement-text">выставок</p>
                                            </div>
                                        </div>

                                        <!-- Блок с направлениями-кнопочками -->
                                        <div class="photographer-sessions">
                                            <p class="sessions-title">направления съемки</p>
                                            <div class="sessions-categories">
                                                <?php
                                                // Получаем названия фотосессий этого фотографа
                                                $sql_sessions = "SELECT name FROM fs WHERE photogs_id = ?";
                                                $stmt_sessions = $connect->prepare($sql_sessions);
                                                $stmt_sessions->execute([$USER['id']]);
                                                $sessions = $stmt_sessions->fetchAll();

                                                foreach ($sessions as $index => $session) {
                                                    $active_class = $index === 0 ? 'active' : '';
                                                ?>
                                                    <button class="category-btn <?= $active_class ?>"><?= htmlspecialchars($session['name']) ?></button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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