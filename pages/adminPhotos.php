<div class="personal-cabinet-wrapper mt80">
    <?
    if (isset($_SESSION['USER'])) {
        if ($USER['role'] == 2) {
            // Получаем все фотосессии с данными фотографов и категорий
            $sql = "SELECT fs.*, u.name as photographer_name, c.name as category_name 
                    FROM `fs` fs 
                    LEFT JOIN `user` u ON fs.photogs_id = u.id 
                    LEFT JOIN `category` c ON fs.category = c.id 
                    ORDER BY fs.id DESC";
            $photosessions = $connect->query($sql);

            // Получаем категории для фильтра
            $categories = $connect->query("SELECT * FROM `category`")->fetchAll();
    ?>
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
                        <div class="profile-form">
                            <div class="btns-portfolio">
                                <h2 class="title">Фотосессии</h2>
                                <a href="?page=addPhoto" class="btn btn-black edit">добавить</a>
                            </div>



                            <div class="mini_catalog_cards">
                                <?
                                if ($photosessions->rowCount() > 0) {
                                    foreach ($photosessions as $session) {
                                        // Получаем первое изображение для превью
                                        $image_sql = "SELECT filename FROM imagesPhotogs WHERE fs_id = ? LIMIT 1";
                                        $image_stmt = $connect->prepare($image_sql);
                                        $image_stmt->execute([$session['id']]);
                                        $image = $image_stmt->fetch();
                                        $image_path = $image ? $image['filename'] : 'assets/img/home/card2.png';
                                ?>
                                        <div class="card_catalog">
                                            <div class="card-img">
                                                <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($session['name']) ?>">
                                            </div>
                                            <p class="name_card">
                                                <?= htmlspecialchars($session['name']) ?>
                                            </p>
                                            <p class="desc_card">
                                                <?= htmlspecialchars($session['desc']) ?>
                                            </p>
                                            <div class="card-info">
                                                <p><strong>Фотограф:</strong> <?= htmlspecialchars($session['photographer_name']) ?></p>
                                                <p><strong>Категория:</strong> <?= htmlspecialchars($session['category_name']) ?></p>
                                                <p><strong>Цена:</strong> от <?= htmlspecialchars($session['price']) ?>₽</p>
                                            </div>
                                            <div class="btns-column-edit-delete">
                                                <a href="?page=editPhoto&id=<?= $session['id'] ?>" class="btn btn-no-back edit">редактировать</a>
                                                <button type="button" class="btn edit" onclick="openDeleteModal(<?= $session['id'] ?>, '<?= htmlspecialchars($session['name'], ENT_QUOTES) ?>')">удалить</button>
                                            </div>
                                        </div>
                                <?
                                    }
                                } else {
                                    echo '<p class="no-data">Фотосессии не найдены</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                        <p id="deleteMessage">вы действительно хотите удалить фотосессию</p>
                    </div>
                    <div class="modal-footer">
                        <form method="post" id="deleteForm" style="display: contents;">
                            <input type="hidden" name="delete_id" id="deleteId">
                            <button type="button" class="btn btn-black" onclick="closeDeleteModal()">нет</button>
                            <button type="submit" class="btn" name="delete_photo">да</button>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function openDeleteModal(id, name) {
                    document.getElementById('deleteId').value = id;
                    document.getElementById('deleteMessage').textContent = 'вы действительно хотите удалить фотосессию «' + name + '»';
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
    <?
            // Обработка удаления фотосессии
            if (isset($_POST['delete_photo'])) {
                $delete_id = $_POST['delete_id'];

                try {
                    // Начинаем транзакцию
                    $connect->beginTransaction();

                    // Удаляем изображения из imagesPhotogs
                    $sql_delete_images = "DELETE FROM `imagesPhotogs` WHERE `fs_id` = ?";
                    $stmt_images = $connect->prepare($sql_delete_images);
                    $stmt_images->execute([$delete_id]);

                    // Удаляем фотосессию из fs
                    $sql_delete_fs = "DELETE FROM `fs` WHERE `id` = ?";
                    $stmt_fs = $connect->prepare($sql_delete_fs);
                    $result_fs = $stmt_fs->execute([$delete_id]);

                    // Подтверждаем транзакцию
                    $connect->commit();

                    if ($result_fs) {
                        echo '<script>document.location.href="?page=adminPhotos"</script>';
                    } else {
                        echo '<p class="error">Ошибка при удалении фотосессии</p>';
                    }
                } catch (Exception $e) {
                    // Откатываем транзакцию в случае ошибки
                    $connect->rollBack();
                    echo '<p class="error">Ошибка при удалении: ' . $e->getMessage() . '</p>';
                }
            }
        } else {
            echo '<script>document.location.href="?page=error403"</script>';
        }
    } else {
        echo '<script>document.location.href="?page=error403"</script>';
    }
    ?>
</div>