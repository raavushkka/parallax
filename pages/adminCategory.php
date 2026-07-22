<div class="personal-cabinet-wrapper mt80">
    <?
    if (isset($_SESSION['USER'])) {
        if ($USER['role'] == 2) {
            // Получаем все категории
            $sql = "SELECT * FROM `category`";
            $categories = $connect->query($sql);

            // Обработка добавления категории
            if (isset($_POST['add_category'])) {
                $name = $_POST['category_name'] ?? '';

                if (!empty(trim($name))) {
                    $sql_insert = "INSERT INTO `category` (`name`) VALUES ('$name')";
                    $result_insert = $connect->query($sql_insert);

                    if ($result_insert) {
                        echo '<script>document.location.href="?page=adminCategory"</script>';
                    } else {
                        echo '<p class="error">Ошибка при добавлении категории</p>';
                    }
                }
            }

            // Обработка редактирования категории
            if (isset($_POST['update_category'])) {
                $id = $_POST['category_id'] ?? '';
                $name = $_POST['category_name'] ?? '';

                if (!empty(trim($name)) && !empty($id)) {
                    $sql_update = "UPDATE `category` SET `name` = '$name' WHERE `id` = '$id'";
                    $result_update = $connect->query($sql_update);

                    if ($result_update) {
                        echo '<script>document.location.href="?page=adminCategory"</script>';
                    } else {
                        echo '<p class="error">Ошибка при обновлении категории</p>';
                    }
                }
            }
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
                            <h2 class="title">Категории фотосессий</h2>
                            <form method="post" class="add-category">
                                <input type="text" name="category_name" placeholder="введите название" required>
                                <input type="submit" value="добавить" class="btn" name="add_category">
                            </form>

                            <div class="cards-zapisi-user">
                                <?
                                if ($categories->rowCount() > 0) {
                                    foreach ($categories as $category) {
                                        // Проверяем, используется ли категория в фотосессиях
                                        $sql_check = "SELECT COUNT(*) as count FROM `fs` WHERE `category_id` = ?";
                                        $stmt_check = $connect->prepare($sql_check);
                                        $stmt_check->execute([$category['id']]);
                                        $usage_count = $stmt_check->fetch(PDO::FETCH_ASSOC)['count'];
                                        $is_used = $usage_count > 0;
                                ?>
                                        <div class="card-zapisi-user">
                                            <div class="info-zapis-user">
                                                <div class="info-double">
                                                    <p class="photogr">
                                                        id: <span><?= $category['id'] ?></span>
                                                    </p>
                                                    <?php if ($is_used): ?>
                                                        <p class="photogr" style="color: #666;">
                                                            используется в <?= $usage_count ?> фотосессиях
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="info-double">
                                                <p class="photogr">
                                                    название:
                                                    <span id="categoryName<?= $category['id'] ?>">
                                                        <?= htmlspecialchars($category['name']) ?>
                                                    </span>
                                                    <input type="text"
                                                        id="editInput<?= $category['id'] ?>"
                                                        class="edit-input"
                                                        value="<?= htmlspecialchars($category['name']) ?>"
                                                        style="display: none;"
                                                        maxlength="256">
                                                </p>
                                            </div>
                                            <div class="btns-column-edit-delete">
                                                <button type="button"
                                                    class="btn btn-no-back edit"
                                                    onclick="toggleEdit(<?= $category['id'] ?>)"
                                                    id="editBtn<?= $category['id'] ?>">
                                                    редактировать
                                                </button>
                                                <form method="post" style="display: none;" id="editForm<?= $category['id'] ?>">
                                                    <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                                    <input type="hidden" name="category_name" id="hiddenName<?= $category['id'] ?>">
                                                    <button type="submit" class="btn btn-no-back edit" name="update_category">сохранить</button>
                                                    <button type="button" class="btn edit" onclick="toggleEdit(<?= $category['id'] ?>)">отмена</button>
                                                </form>
                                                <?php if (!$is_used): ?>
                                                    <input type="submit"
                                                        value="удалить"
                                                        class="btn edit"
                                                        onclick="openDeleteModal(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name'], ENT_QUOTES) ?>')">
                                                <?php else: ?>
                                                    <button type="button"
                                                        class="btn edit"
                                                        style="background-color: #ccc; cursor: not-allowed;"
                                                        title="Нельзя удалить - категория используется в фотосессиях">
                                                        удалить
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                <?
                                    }
                                } else {
                                    echo '<p class="no-data">Категории не найдены</p>';
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
                        <p id="deleteMessage">вы действительно хотите удалить категорию</p>
                    </div>
                    <div class="modal-footer">
                        <form method="post" id="deleteForm" style="display: contents;">
                            <input type="hidden" name="delete_id" id="deleteId">
                            <button type="button" class="btn btn-black" onclick="closeDeleteModal()">нет</button>
                            <button type="submit" class="btn" name="delete_category">да</button>
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
                        <p id="errorMessage">невозможно удалить категорию, она используется для фотосессий</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-black" onclick="closeErrorModal()">понятно</button>
                    </div>
                </div>
            </div>

            <style>
                .edit-input {
                    padding: 5px 10px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    font-size: 14px;
                    width: 200px;
                }

                .edit-buttons {
                    display: flex;
                    gap: 10px;
                    margin-top: 10px;
                }
            </style>

            <script>
                function toggleEdit(categoryId) {
                    const nameSpan = document.getElementById('categoryName' + categoryId);
                    const editInput = document.getElementById('editInput' + categoryId);
                    const editBtn = document.getElementById('editBtn' + categoryId);
                    const editForm = document.getElementById('editForm' + categoryId);
                    const hiddenInput = document.getElementById('hiddenName' + categoryId);

                    if (editInput.style.display === 'none') {
                        // Включаем режим редактирования
                        nameSpan.style.display = 'none';
                        editInput.style.display = 'inline-block';
                        editBtn.style.display = 'none';
                        editForm.style.display = 'flex';
                        editForm.style.gap = '10px';
                        editInput.focus();
                    } else {
                        // Выключаем режим редактирования
                        nameSpan.style.display = 'inline';
                        editInput.style.display = 'none';
                        editBtn.style.display = 'inline-block';
                        editForm.style.display = 'none';
                        // Восстанавливаем оригинальное значение
                        editInput.value = nameSpan.textContent;
                    }
                }

                // Обновляем скрытое поле при изменении input
                document.querySelectorAll('[id^="editInput"]').forEach(input => {
                    input.addEventListener('input', function() {
                        const categoryId = this.id.replace('editInput', '');
                        document.getElementById('hiddenName' + categoryId).value = this.value;
                    });
                });

                function openDeleteModal(id, name) {
                    document.getElementById('deleteId').value = id;
                    document.getElementById('deleteMessage').textContent = 'вы действительно хотите удалить категорию «' + name + '»';
                    document.getElementById('deleteModal').style.display = 'flex';
                }

                function closeDeleteModal() {
                    document.getElementById('deleteModal').style.display = 'none';
                }

                function openErrorModal() {
                    // Используем текст из HTML, не меняем его
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
                        // Также выходим из режима редактирования при ESC
                        document.querySelectorAll('[id^="editInput"]').forEach(input => {
                            if (input.style.display !== 'none') {
                                const categoryId = input.id.replace('editInput', '');
                                toggleEdit(categoryId);
                            }
                        });
                    }
                });
            </script>
    <?
            // Обработка удаления категории
            if (isset($_POST['delete_category'])) {
                $delete_id = $_POST['delete_id'];

                // Проверяем, используется ли категория в фотосессиях
                $sql_check = "SELECT COUNT(*) as count FROM `fs` WHERE `category_id` = ?";
                $stmt_check = $connect->prepare($sql_check);
                $stmt_check->execute([$delete_id]);
                $usage_count = $stmt_check->fetch(PDO::FETCH_ASSOC)['count'];

                if ($usage_count > 0) {
                    // Если категория используется, показываем ошибку
                    echo '<script>openErrorModal();</script>';
                } else {
                    // Если категория не используется, удаляем
                    $sql_delete = "DELETE FROM `category` WHERE `id` = '$delete_id'";
                    $result_delete = $connect->query($sql_delete);

                    if ($result_delete) {
                        echo '<script>document.location.href="?page=adminCategory"</script>';
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