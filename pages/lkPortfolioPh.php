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
                            <h2 class="title m0">портфолио</h2>
                            <a href="?page=addPortfolio"><input type="submit" value="добавить" class="btn btn-black edit"></a>

                            <?php
                            // Получаем работы из портфолио фотографа
                            $sql = "SELECT p.*, f.name as fs_name 
                            FROM portfolio p 
                            LEFT JOIN fs f ON p.fs = f.id 
                            WHERE p.photogs_id = ? 
                            ORDER BY p.date DESC";
                            $stmt = $connect->prepare($sql);
                            $stmt->execute([$USER['id']]);
                            $portfolio_works = $stmt->fetchAll();

                            // Получаем уникальные категории для фильтров
                            $sql_categories = "SELECT DISTINCT c.id, c.name 
                                     FROM portfolio p 
                                     JOIN fs f ON p.fs = f.id 
                                     JOIN category c ON f.category = c.id 
                                     WHERE p.photogs_id = ?";
                            $stmt_categories = $connect->prepare($sql_categories);
                            $stmt_categories->execute([$USER['id']]);
                            $categories = $stmt_categories->fetchAll();
                            ?>

                            <div class="portfolio">
                                <!-- <div class="sessions-categories">
                                    <button class="category-btn active">все</button>
                                    <?php foreach ($categories as $category) { ?>
                                        <button class="category-btn"><?= htmlspecialchars($category['name']) ?></button>
                                    <?php } ?>
                                </div> -->

                                <div class="works-ph-portgopio">
                                    <?php
                                    if (empty($portfolio_works)) {
                                        echo '<div class="empty-cart-message">В портфолио пока нет работ.<br>Добавьте свои первые работы!</div>';
                                    } else {
                                        foreach ($portfolio_works as $work) {
                                            // Получаем изображения для работы
                                            $sql_images = "SELECT filename FROM imagesPortfolio WHERE portfolio_id = ?";
                                            $stmt_images = $connect->prepare($sql_images);
                                            $stmt_images->execute([$work['id']]);
                                            $images = $stmt_images->fetchAll();

                                            if (!empty($images)) {
                                    ?>
                                                <div class="work-ph-portgopio">
                                                    <p class="name-work">
                                                        <?= htmlspecialchars($work['name']) ?>
                                                    </p>
                                                    <div class="btns-portfolio">
                                                        <a href="?page=editPortfolio&id=<?= $work['id'] ?>">
                                                            <input type="submit" value="редактировать" class="btn btn-no-back edit ">
                                                        </a>
                                                        <button type="button" class="btn edit w30" onclick="openDeleteModal(<?= $work['id'] ?>, '<?= htmlspecialchars($work['name'], ENT_QUOTES) ?>')">
                                                            удалить
                                                        </button>
                                                    </div>
                                                    <p class="desc-work">
                                                        <?= htmlspecialchars($work['fs_name'] ?? '') ?> - <?= htmlspecialchars($work['date']) ?> - <?= htmlspecialchars($work['location']) ?>
                                                    </p>
                                                    <div class="fotos-work">
                                                        <?php foreach ($images as $image) { ?>
                                                            <img src="<?= $image['filename'] ?>" alt="<?= htmlspecialchars($work['name']) ?>">
                                                        <?php } ?>
                                                    </div>
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
        </div>
<?
    } else {
        echo '<script>document.location.href="?page=error403"</script>';
    }
} else {
    echo '<script>document.location.href="?page=error403"</script>';
}
?>


<!-- Модальное окно удаления -->
<div class="modal-overlay" id="deleteModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close" onclick="closeDeleteModal()"><img src="assets/img/modal/Path.svg" alt=""></span>
        </div>
        <div class="modal-title-section">
            <h3 class="modal-title">Подтверждение удаления</h3>
        </div>
        <div class="modal-body">
            <p id="deleteMessage">вы действительно хотите удалить работу</p>
        </div>
        <div class="modal-footer">
            <form method="post" id="deleteForm" style="display: contents;">
                <input type="hidden" name="portfolio_id" id="deleteId">
                <button type="button" class="btn btn-black" onclick="closeDeleteModal()">нет</button>
                <button type="submit" class="btn" name="delete_portfolio">да</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(id, name) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteMessage').textContent = 'вы действительно хотите удалить работу «' + name + '»';
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // Закрытие по клику на оверлей
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Закрытие по ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>

<?php
// Обработка удаления работы
if (isset($_POST['delete_portfolio'])) {
    $portfolio_id = $_POST['portfolio_id'];

    // Удаляем изображения
    $sql_delete_images = "DELETE FROM imagesPortfolio WHERE portfolio_id = ?";
    $stmt_delete_images = $connect->prepare($sql_delete_images);
    $stmt_delete_images->execute([$portfolio_id]);

    // Удаляем работу
    $sql_delete_work = "DELETE FROM portfolio WHERE id = ? AND photogs_id = ?";
    $stmt_delete_work = $connect->prepare($sql_delete_work);
    $stmt_delete_work->execute([$portfolio_id, $USER['id']]);

    echo '<script>document.location.href="?page=lkPortfolioPh"</script>';
}
?>