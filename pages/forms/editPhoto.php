<div class="personal-cabinet-wrapper mt80">
    <?
    if (isset($_SESSION['USER'])) {
        if ($USER['role'] == 2) {
            // Получаем ID фотосессии для редактирования
            $fs_id = $_GET['id'] ?? 0;
            $is_edit = !empty($fs_id);

            // Определяем переменные заранее
            $name = '';
            $description = '';
            $location = '';
            $photographer_id = '';
            $category_id = '';
            $price = '';
            $existingImages = [];

            // Если это редактирование, загружаем данные фотосессии
            if ($is_edit) {
                try {
                    $stmt = $connect->prepare("SELECT * FROM fs WHERE id = :id");
                    $stmt->execute([':id' => $fs_id]);
                    $fs_data = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($fs_data) {
                        $name = $fs_data['name'];
                        $description = $fs_data['desc'];
                        $location = $fs_data['location'];
                        $price = $fs_data['price'];
                        $photographer_id = $fs_data['photogs_id'];
                        $category_id = $fs_data['category'];

                        // Загружаем существующие изображения
                        $img_stmt = $connect->prepare("SELECT * FROM imagesPhotogs WHERE fs_id = :fs_id");
                        $img_stmt->execute([':fs_id' => $fs_id]);
                        $existingImages = $img_stmt->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        echo '<script>document.location.href="?page=adminPhotos"</script>';
                        exit;
                    }
                } catch (PDOException $e) {
                    echo '<p class="error">Ошибка загрузки данных: ' . $e->getMessage() . '</p>';
                }
            }

            if (isset($_POST['save_fs'])) {
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $location = $_POST['location'] ?? '';
                $photographer_id = $_POST['photographer_id'] ?? '';
                $category_id = $_POST['category_id'] ?? '';
                $price = $_POST['price'] ?? '';

                $flag = true;

                // Проверка обязательных полей
                if (empty($name)) {
                    $flag = false;
                }
                if (empty($description)) {
                    $flag = false;
                }
                if (empty($location)) {
                    $flag = false;
                }
                if (empty($photographer_id)) {
                    $flag = false;
                }
                if (empty($category_id)) {
                    $flag = false;
                }
                if (empty($price)) {
                    $flag = false;
                }

                // Если все проверки пройдены, сохраняем в БД
                if ($flag) {
                    try {
                        // Очищаем цену от пробелов и букв, оставляем только цифры
                        $clean_price = preg_replace('/[^0-9]/', '', $price);

                        if ($is_edit) {
                            // Обновляем фотосессию
                            $fs = $connect->prepare("UPDATE `fs` SET `name` = :name, `desc` = :desc, `location` = :location, `price` = :price, `category` = :category, `photogs_id` = :photogs_id WHERE `id` = :id");
                            $fs->execute([
                                ':name' => $name,
                                ':desc' => $description,
                                ':location' => $location,
                                ':price' => $clean_price,
                                ':category' => $category_id,
                                ':photogs_id' => $photographer_id,
                                ':id' => $fs_id
                            ]);
                        } else {
                            // Добавляем новую фотосессию
                            $fs = $connect->prepare("INSERT INTO `fs` (`name`, `desc`, `location`, `price`, `category`, `photogs_id`) 
                                                VALUES (:name, :desc, :location, :price, :category, :photogs_id)");
                            $fs->execute([
                                ':name' => $name,
                                ':desc' => $description,
                                ':location' => $location,
                                ':price' => $clean_price,
                                ':category' => $category_id,
                                ':photogs_id' => $photographer_id
                            ]);
                            $fs_id = $connect->lastInsertId();
                        }

                        // Обрабатываем новые изображения (только измененные)
                        for ($i = 0; $i < 4; $i++) {
                            if (!empty($_FILES['images']['name'][$i]) && $_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                                $filename = $_FILES['images']['name'][$i];
                                $imagePath = 'assets/img/catalog/' . uniqid() . '_' . $filename;

                                if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $imagePath)) {
                                    // Если это редактирование, обновляем существующее изображение
                                    if ($is_edit && isset($existingImages[$i])) {
                                        // Удаляем старый файл
                                        if (file_exists($existingImages[$i]['filename'])) {
                                            unlink($existingImages[$i]['filename']);
                                        }
                                        // Обновляем запись в базе
                                        $update_stmt = $connect->prepare("UPDATE imagesPhotogs SET filename = :filename WHERE images_id = :images_id");
                                        $update_stmt->execute([
                                            ':filename' => $imagePath,
                                            ':images_id' => $existingImages[$i]['images_id']
                                        ]);
                                    } else {
                                        // Добавляем новое изображение
                                        $images = $connect->prepare("INSERT INTO `imagesPhotogs` (`fs_id`, `filename`) 
                                                               VALUES (:fs_id, :filename)");
                                        $images->execute([
                                            ':fs_id' => $fs_id,
                                            ':filename' => $imagePath
                                        ]);
                                    }
                                }
                            }
                        }

                        echo '<script>document.location.href="?page=adminPhotos"</script>';
                        exit;
                    } catch (PDOException $e) {
                        echo '<p class="error">Ошибка базы данных: ' . $e->getMessage() . '</p>';
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
                            <a href="#" class="nav-item">выйти</a>
                        </div>
                    </nav>

                    <div class="cabinet-content">
                        <div class="profile-form">
                            <h2 class="title"><?= $is_edit ? 'редактирование фотосессии' : 'добавление фотосессии' ?></h2>

                            <form method="post" class="form" enctype="multipart/form-data">
                                <? if ($is_edit): ?>
                                    <input type="hidden" name="fs_id" value="<?= $fs_id ?>">
                                <? endif; ?>

                                <div class="img_text">
                                    <div class="label_input photo_acc">
                                        <label>Кликните на изображение чтобы изменить его</label>
                                        <div class="multiple-images-container">
                                            <!-- Скрытые инпуты для каждого изображения -->
                                            <? for ($i = 0; $i < 4; $i++): ?>
                                                <input type="file" id="imageUpload<?= $i ?>" class="file-input" name="images[]" accept="image/*" style="display: none;">
                                            <? endfor; ?>

                                            <!-- Контейнер для превью -->
                                            <div class="previews-container" id="previewsContainer">
                                                <? if ($is_edit && !empty($existingImages)): ?>
                                                    <? foreach ($existingImages as $index => $image): ?>
                                                        <div class="preview-item" data-index="<?= $index ?>">
                                                            <img src="<?= $image['filename'] ?>" alt="Изображение <?= $index + 1 ?>">
                                                            <div class="preview-count"><?= $index + 1 ?></div>
                                                            <div class="change-overlay">✎</div>
                                                        </div>
                                                    <? endforeach; ?>
                                                <? else: ?>
                                                    <!-- Пустые слоты для новых изображений -->
                                                    <? for ($i = 0; $i < 4; $i++): ?>
                                                        <div class="preview-item empty" data-index="<?= $i ?>">
                                                            <div class="empty-slot">+</div>
                                                            <div class="preview-count"><?= $i + 1 ?></div>
                                                        </div>
                                                    <? endfor; ?>
                                                <? endif; ?>
                                            </div>
                                        </div>

                                        <? if ($is_edit && !empty($existingImages)): ?>
                                            <p class="info">Кликните на изображение чтобы изменить его</p>
                                        <? endif; ?>
                                    </div>
                                    <div class="text_profile"></div>
                                </div>

                                <script>
                                    // Клик по превью для изменения изображения
                                    document.querySelectorAll('.preview-item').forEach(previewItem => {
                                        previewItem.addEventListener('click', function(e) {
                                            const index = this.getAttribute('data-index');
                                            const fileInput = document.getElementById('imageUpload' + index);
                                            fileInput.click();
                                        });
                                    });

                                    // Обработка выбора файла для каждого инпута
                                    <? for ($i = 0; $i < 4; $i++): ?>
                                        document.getElementById('imageUpload<?= $i ?>').addEventListener('change', function(e) {
                                            if (e.target.files.length > 0) {
                                                const file = e.target.files[0];
                                                if (file && file.type.startsWith('image/')) {
                                                    const index = this.id.replace('imageUpload', '');
                                                    updatePreview(file, index);
                                                }
                                            }
                                        });
                                    <? endfor; ?>

                                    function updatePreview(file, index) {
                                        const reader = new FileReader();
                                        reader.onload = function(e) {
                                            const previewItems = document.querySelectorAll('.preview-item');
                                            const previewItem = previewItems[index];

                                            // Обновляем изображение
                                            const img = previewItem.querySelector('img');
                                            if (img) {
                                                img.src = e.target.result;
                                            } else {
                                                // Если это был пустой слот, создаем изображение
                                                previewItem.innerHTML = `
                                                    <img src="${e.target.result}" alt="Preview ${parseInt(index) + 1}">
                                                    <div class="preview-count">${parseInt(index) + 1}</div>
                                                    <div class="change-overlay">✎</div>
                                                `;
                                                previewItem.classList.remove('empty');
                                            }
                                        };
                                        reader.readAsDataURL(file);
                                    }
                                </script>

                                <!-- Остальные поля формы -->
                                <!-- Поле имени -->
                                <div class="form-group">
                                    <label for="name" class="form-label">введите название</label>
                                    <input type="text" id="name" name="name" class="form-input" placeholder="креативная" value="<?= htmlspecialchars($name) ?>" maxlength="21">
                                    <?
                                    if (isset($_POST['save_fs']) && empty($name)) {
                                        echo '<p class="error">Введите название</p>';
                                    }
                                    ?>
                                </div>

                                <!-- Поле цены -->
                                <div class="form-group">
                                    <label for="price" class="form-label">введите цену</label>
                                    <input type="text" id="price" name="price" class="form-input" placeholder="от 8000Р" value="<?= htmlspecialchars($price) ?>" maxlength="21">
                                    <?
                                    if (isset($_POST['save_fs']) && empty($price)) {
                                        echo '<p class="error">Введите цену</p>';
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <div class="select-form">
                                        <select class="custom-select" name="photographer_id">
                                            <option value="" disabled selected>выберите фотографа</option>
                                            <?php
                                            // Получаем фотографов из базы данных
                                            $photographers = $connect->query("SELECT * FROM user WHERE role = 3")->fetchAll();
                                            foreach ($photographers as $photographer) {
                                                $selected = ($photographer_id == $photographer['id']) ? 'selected' : '';
                                                echo "<option value='{$photographer['id']}' $selected>{$photographer['name']}</option>";
                                            }
                                            ?>
                                        </select>
                                        <div class="select-arrow">
                                            <img src="assets/img/catalog/arrow_down.svg" alt="▼">
                                        </div>
                                    </div>
                                    <?
                                    if (isset($_POST['save_fs']) && empty($photographer_id)) {
                                        echo '<p class="error">Выберите фотографа</p>';
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <div class="select-form">
                                        <select class="custom-select" name="category_id">
                                            <option value="" disabled selected>выберите категорию</option>
                                            <?php
                                            // Получаем категории из базы данных
                                            $categories = $connect->query("SELECT * FROM category")->fetchAll();
                                            foreach ($categories as $category) {
                                                $selected = ($category_id == $category['id']) ? 'selected' : '';
                                                echo "<option value='{$category['id']}' $selected>{$category['name']}</option>";
                                            }
                                            ?>
                                        </select>
                                        <div class="select-arrow">
                                            <img src="assets/img/catalog/arrow_down.svg" alt="▼">
                                        </div>
                                    </div>
                                    <?
                                    if (isset($_POST['save_fs']) && empty($category_id)) {
                                        echo '<p class="error">Выберите категорию</p>';
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="form-label">введите описание</label>
                                    <textarea name="description" id="description" class="form-input textarea" maxlength="251" placeholder="описание"><?= htmlspecialchars($description) ?></textarea>
                                    <?
                                    if (isset($_POST['save_fs']) && empty($description)) {
                                        echo '<p class="error">Введите описание</p>';
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="location" class="form-label">введите локацию</label>
                                    <input type="text" id="location" name="location" class="form-input" placeholder="студия" value="<?= htmlspecialchars($location) ?>" maxlength="31">
                                    <?
                                    if (isset($_POST['save_fs']) && empty($location)) {
                                        echo '<p class="error">Введите локацию</p>';
                                    }
                                    ?>
                                </div>

                                <!-- Кнопка сохранения -->
                                <input type="submit" name="save_fs" class="btn btn-hover-red-wh-fon" value="<?= $is_edit ? 'сохранить изменения' : 'добавить' ?>">

                            </form>
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
</div>

<style>
    .preview-item {
        position: relative;
        width: 250px;
        height: 250px;
        border: 2px dashed #ccc;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        overflow: hidden;
    }

    .preview-item.empty {
        background: #f8f9fa;
    }

    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .empty-slot {
        font-size: 24px;
        color: #6c757d;
    }

    .preview-count {
        position: absolute;
        /* top: 5px;
        left: 5px; */
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
    }

    .change-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 20px;
    }

    .preview-item:hover .change-overlay {
        opacity: 1;
    }

    .previews-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
</style>