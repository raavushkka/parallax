<div class="personal-cabinet-wrapper mt80">
    <?
    if (isset($_SESSION['USER'])) {
        if ($USER['role'] == 2) {
            // Получаем всех фотографов (пользователей с ролью 1 и данными в таблице photogs)
            $sql = "SELECT u.*, p.desc, p.experience, p.projects, p.exhibitions 
                    FROM `user` u 
                    LEFT JOIN `photogs` p ON u.id = p.id 
                    WHERE u.role = 3";
            $photogs = $connect->query($sql);
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
                                <h2 class="title">фотографы</h2>
                                <a class="btn btn-black edit" href="?page=addPh">добавить</a>
                            </div>
                            <div class="cards-zapisi-user">
                                <?
                                if ($photogs->rowCount() > 0) {
                                    foreach ($photogs as $photog) {
                                ?>
                                        <div class="card-zapisi-user">
                                            <div class="info-zapis-user">
                                                <div class="info-double">
                                                    <p class="photogr">
                                                        id: <span><?= $photog['id'] ?></span>
                                                    </p>
                                                    <p class="photogr">
                                                        имя: <span><?= htmlspecialchars($photog['name']) ?></span>
                                                    </p>
                                                </div>
                                                <div class="info-double gray-lk">
                                                    <p class="photogr">
                                                        телефон: <span><?= htmlspecialchars($photog['phone']) ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                            <input type="submit" value="удалить" class="btn edit" onclick="openDeleteModal(<?= $photog['id'] ?>, '<?= htmlspecialchars($photog['name'], ENT_QUOTES) ?>')">
                                        </div>
                                <?
                                    }
                                } else {
                                    echo '<p class="no-data">Фотографы не найдены</p>';
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
                        <p id="deleteMessage">вы действительно хотите удалить фотографа</p>
                    </div>
                    <div class="modal-footer">
                        <form method="post" id="deleteForm" style="display: contents;">
                            <input type="hidden" name="delete_id" id="deleteId">
                            <button type="button" class="btn btn-black" onclick="closeDeleteModal()">нет</button>
                            <button type="submit" class="btn" name="delete_photog">да</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Модальное окно ошибки удаления -->
            <div class="modal-overlay" id="errorModal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="modal-close" onclick="closeErrorModal()"><img src="assets/img/modal/Path.svg" alt=""></span>
                    </div>
                    <div class="modal-title-section">
                        <h3 class="modal-title">Ошибка удаления</h3>
                    </div>
                    <div class="modal-body">
                        <p id="errorMessage">невозможно удалить фотографа, он используется для фотосессий</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-black" onclick="closeErrorModal()">понятно</button>
                    </div>
                </div>
            </div>

            <script>
                function openDeleteModal(id, name) {
                    document.getElementById('deleteId').value = id;
                    document.getElementById('deleteMessage').textContent = 'вы действительно хотите удалить фотографа «' + name + '»';
                    document.getElementById('deleteModal').style.display = 'flex';
                }

                function closeDeleteModal() {
                    document.getElementById('deleteModal').style.display = 'none';
                }

                function openErrorModal() {
                    document.getElementById('errorModal').style.display = 'flex';
                }

                function closeErrorModal() {
                    document.getElementById('errorModal').style.display = 'none';
                }

                // Закрытие по клику на оверлей
                document.getElementById('deleteModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeDeleteModal();
                    }
                });

                document.getElementById('errorModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeErrorModal();
                    }
                });

                // Закрытие по ESC
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeDeleteModal();
                        closeErrorModal();
                    }
                });
            </script>
    <?
            // Обработка удаления фотографа
            if (isset($_POST['delete_photog'])) {
                $delete_id = $_POST['delete_id'];

                // Проверяем, используется ли фотограф в фотосессиях
                $sql_check = "SELECT COUNT(*) as count FROM `fs` WHERE `photogs_id` = ?";
                $stmt_check = $connect->prepare($sql_check);
                $stmt_check->execute([$delete_id]);
                $usage_count = $stmt_check->fetch(PDO::FETCH_ASSOC)['count'];

                if ($usage_count > 0) {
                    // Если фотограф используется, показываем ошибку
                    echo '<script>openErrorModal();</script>';
                } else {
                    // Удаляем из таблицы photogs
                    $sql_delete_photogs = "DELETE FROM `photogs` WHERE `id` = '$delete_id'";
                    $result_photogs = $connect->query($sql_delete_photogs);

                    // Удаляем из таблицы user
                    $sql_delete_user = "DELETE FROM `user` WHERE `id` = '$delete_id' AND `role` = 3";
                    $result_user = $connect->query($sql_delete_user);

                    if ($result_user) {
                        echo '<script>document.location.href="?page=adminPh"</script>';
                    } else {
                        echo '<script>openErrorModal();</script>';
                    }
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