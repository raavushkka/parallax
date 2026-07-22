<div class="personal-cabinet-wrapper mt80">
    <?
    if (isset($_SESSION['USER'])) {
        if ($USER['role'] == 2) {
            // Получаем все сертификаты
            $sql = "SELECT * FROM `certificate`";
            $certificates = $connect->query($sql);
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
                                <h2 class="title">сертификаты</h2>
                                <a class="btn btn-black edit" href="?page=addCertificat">добавить</a>
                            </div>
                            <div class="cards-zapisi-user">
                                <?
                                if ($certificates->rowCount() > 0) {
                                    foreach ($certificates as $cert) {
                                        // Форматируем цену с пробелами
                                        $formatted_price = number_format($cert['price'], 0, '', ' ') . ' ₽';
                                ?>
                                        <div class="card-zapisi-user">
                                            <div class="info-zapis-user">
                                                <div class="info-double">
                                                    <p class="photogr">
                                                        название: <span><?= htmlspecialchars($cert['name']) ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="info-double">
                                                <p class="photogr">
                                                    цена: <span><?= $formatted_price ?></span>
                                                </p>
                                            </div>
                                            <div class="btns-column-edit-delete">
                                                <a href="?page=editCertificate&id=<?= $cert['id'] ?>" class="btn btn-no-back edit">редактировать</a>
                                                <input type="submit" value="удалить" class="btn edit" onclick="openDeleteModal(<?= $cert['id'] ?>, '<?= htmlspecialchars($cert['name'], ENT_QUOTES) ?>')">
                                            </div>
                                        </div>
                                <?
                                    }
                                } else {
                                    echo '<p class="no-data">Сертификаты не найдены</p>';
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
                        <p id="deleteMessage">вы действительно хотите удалить сертификат</p>
                    </div>
                    <div class="modal-footer">
                        <form method="post" id="deleteForm" style="display: contents;">
                            <input type="hidden" name="delete_id" id="deleteId">
                            <button type="button" class="btn btn-black" onclick="closeDeleteModal()">нет</button>
                            <button type="submit" class="btn" name="delete_certificate">да</button>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function openDeleteModal(id, name) {
                    document.getElementById('deleteId').value = id;
                    document.getElementById('deleteMessage').textContent = 'вы действительно хотите удалить сертификат «' + name + '»';
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
            // Обработка удаления сертификата
            if (isset($_POST['delete_certificate'])) {
                $delete_id = $_POST['delete_id'];

                $sql_delete = "DELETE FROM `certificate` WHERE `id` = '$delete_id'";
                $result_delete = $connect->query($sql_delete);

                if ($result_delete) {
                    echo '<script>document.location.href="?page=adminCertificate"</script>';
                } else {
                    echo '<p class="error">Ошибка при удалении сертификата</p>';
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