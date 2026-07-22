<div class="personal-cabinet-wrapper mt80">
    <?
    if (isset($_SESSION['USER'])) {
        if ($USER['role'] == 2) {
            // Определяем переменные заранее
            $name = '';
            $description = '';
            $location = '';
            $photographer_id = '';
            $category_id = '';
            $price = '';
            $flag = true;
            $uploadedImages = [];
            $imageError = '';

            if (isset($_POST['add_fs'])) {
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $location = $_POST['location'] ?? '';
                $photographer_id = $_POST['photographer_id'] ?? '';
                $category_id = $_POST['category_id'] ?? '';
                $price = $_POST['price'] ?? '';

                $flag = true;
                $uploadedImages = [];
                $imageError = '';

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

                // Проверка загрузки изображений - ТОЧНО 4 изображения
                if (!empty($_FILES['images']['name'][0])) {
                    $uploadedFilesCount = count(array_filter($_FILES['images']['name']));

                    if ($uploadedFilesCount !== 4) {
                        $flag = false;
                        $imageError = '<p class="error">Загружено ' . $uploadedFilesCount . ' из 4 изображений. Необходимо ровно 4 изображения.</p>';
                    } else {
                        foreach ($_FILES['images']['name'] as $index => $filename) {
                            if (!empty($filename)) {
                                $imagePath = 'assets/img/catalog/' . uniqid() . '_' . $filename;
                                if (move_uploaded_file($_FILES['images']['tmp_name'][$index], $imagePath)) {
                                    $uploadedImages[] = $imagePath;
                                } else {
                                    $flag = false;
                                    $imageError = '<p class="error">Ошибка загрузки изображения: ' . $filename . '</p>';
                                }
                            }
                        }

                        // Проверяем что загружено ровно 4 изображения
                        if (count($uploadedImages) !== 4) {
                            $flag = false;
                            $imageError = '<p class="error">Загружено ' . count($uploadedImages) . ' из 4 изображений</p>';
                        }
                    }
                } else {
                    $flag = false;
                    $imageError = '<p class="error">Необходимо загрузить 4 изображения</p>';
                }

                // Если все проверки пройдены, добавляем в БД
                if ($flag && count($uploadedImages) === 4) {
                    try {
                        // Очищаем цену от пробелов и букв, оставляем только цифры
                        $clean_price = preg_replace('/[^0-9]/', '', $price);

                        // Добавляем фотосессию в таблицу fs (с локацией)
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

                        // Получаем ID добавленной фотосессии
                        $lastFsID = $connect->lastInsertId();

                        // Добавляем все изображения в таблицу imagesPhotogs
                        foreach ($uploadedImages as $imagePath) {
                            $images = $connect->prepare("INSERT INTO `imagesPhotogs` (`fs_id`, `filename`) 
                                                   VALUES (:fs_id, :filename)");
                            $images->execute([
                                ':fs_id' => $lastFsID,
                                ':filename' => $imagePath
                            ]);
                        }

                        echo '<script>document.location.href="?page=adminPhotos"</script>';
                        exit;
                    } catch (PDOException $e) {
                        echo '<p class="error">Ошибка базы данных: ' . $e->getMessage() . '</p>';
                        echo '<p class="error">Проверьте наличие таблицы fs и поля location</p>';
                    }
                } else {
                    // Если не все проверки пройдены, показываем общую ошибку
                    if (empty($imageError)) {
                        $imageError = '<p class="error">Пожалуйста, заполните все поля правильно и загрузите 4 изображения</p>';
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
                            <h2 class="title">добавление фотосессии</h2>

                            <form method="post" class="form" enctype="multipart/form-data">
                                <div class="img_text">
                                    <div class="label_input photo_acc">
                                        <label>Перетащите сюда изображения или кликните для выбора (обязательно 4 изображения)</label>
                                        <div class="multiple-images-container">
                                            <!-- Контейнер для загрузки -->
                                            <div class="file-input-container acc-file multiple-file-input" id="multipleFileContainer">
                                                <input type="file" id="multipleImageUpload" class="file-input" name="images[]" accept="image/*" multiple>
                                                <div class="upload-content">
                                                    <div class="upload-text">+ Добавить фото (4 шт.)</div>
                                                </div>
                                            </div>

                                            <!-- Контейнер для превью -->
                                            <div class="previews-container" id="previewsContainer"></div>
                                        </div>
                                        <div class="image-counter" id="imageCounter">
                                            <p>
                                                выбрано: 0/4 изображений
                                            </p>
                                        </div>

                                        <!-- ВЫВОД ОШИБКИ ЗАГРУЗКИ ИЗОБРАЖЕНИЙ СНИЗУ ПОЛЯ -->
                                        <?= $imageError ?>
                                    </div>
                                    <div class="text_profile"></div>
                                </div>

                                <script>
                                    const multipleFileInput = document.getElementById('multipleImageUpload');
                                    const previewsContainer = document.getElementById('previewsContainer');
                                    const multipleFileContainer = document.getElementById('multipleFileContainer');
                                    const imagesContainer = document.querySelector('.multiple-images-container');
                                    const imageCounter = document.getElementById('imageCounter');

                                    let selectedFiles = [];

                                    multipleFileInput.addEventListener('change', function(e) {
                                        handleFiles(e.target.files);
                                    });

                                    // Drag and drop
                                    multipleFileContainer.addEventListener('dragover', function(e) {
                                        e.preventDefault();
                                        this.classList.add('dragover');
                                    });

                                    multipleFileContainer.addEventListener('dragleave', function(e) {
                                        e.preventDefault();
                                        this.classList.remove('dragover');
                                    });

                                    multipleFileContainer.addEventListener('drop', function(e) {
                                        e.preventDefault();
                                        this.classList.remove('dragover');

                                        const files = e.dataTransfer.files;
                                        handleFiles(files);
                                    });

                                    function handleFiles(files) {
                                        const remainingSlots = 4 - selectedFiles.length;

                                        if (files.length > remainingSlots) {
                                            return;
                                        }

                                        for (let i = 0; i < Math.min(files.length, remainingSlots); i++) {
                                            const file = files[i];
                                            if (file && file.type.startsWith('image/')) {
                                                selectedFiles.push(file);
                                                createPreview(file, selectedFiles.length - 1);
                                            }
                                        }

                                        updateUI();
                                    }

                                    function createPreview(file, index) {
                                        const reader = new FileReader();
                                        reader.onload = function(e) {
                                            const previewItem = document.createElement('div');
                                            previewItem.className = 'preview-item';
                                            previewItem.innerHTML = `
                                                <img src="${e.target.result}" alt="Preview ${index + 1}">
                                                <button type="button" class="remove-btn1" onclick="removeImage(${index})">×</button>
                                                <div class="preview-count">${index + 1}</div>
                                            `;
                                            previewsContainer.appendChild(previewItem);
                                        };
                                        reader.readAsDataURL(file);
                                    }

                                    function removeImage(index) {
                                        selectedFiles.splice(index, 1);
                                        updatePreviews();
                                    }

                                    function updatePreviews() {
                                        previewsContainer.innerHTML = '';
                                        selectedFiles.forEach((file, index) => {
                                            createPreview(file, index);
                                        });
                                        updateUI();
                                    }

                                    function updateUI() {
                                        // Обновляем счетчик
                                        imageCounter.innerHTML = `<p>Выбрано: ${selectedFiles.length}/4 изображений</p>`;

                                        if (selectedFiles.length >= 4) {
                                            imagesContainer.classList.add('at-limit');
                                            imageCounter.style.color = 'green';
                                        } else {
                                            imagesContainer.classList.remove('at-limit');
                                            imageCounter.style.color = selectedFiles.length > 0 ? 'orange' : 'red';
                                        }

                                        // Обновляем input files
                                        const dt = new DataTransfer();
                                        selectedFiles.forEach(file => dt.items.add(file));
                                        multipleFileInput.files = dt.files;
                                    }
                                </script>

                                <!-- Поле имени -->
                                <div class="form-group">
                                    <label for="name" class="form-label">введите название</label>
                                    <input type="text" id="name" name="name" class="form-input" placeholder="креативная" value="<?= htmlspecialchars($name) ?? '' ?>" maxlength="21">
                                    <?
                                    if (isset($_POST['add_fs'])) {
                                        if (empty($name)) {
                                            echo '<p class="error">Введите название</p>';
                                        } elseif (mb_strlen($name) > 20) {
                                            echo '<p class="error">Превышено максимальное количество символов (100)</p>';
                                        }
                                    }
                                    ?>
                                </div>

                                <!-- Поле цены -->
                                <div class="form-group">
                                    <label for="price" class="form-label">введите цену</label>
                                    <input type="text" id="price" name="price" class="form-input" placeholder="8000" value="<?= htmlspecialchars($price) ?? '' ?>" maxlength="21">
                                    <?
                                    if (isset($_POST['add_fs'])) {
                                        if (empty($price)) {
                                            $flag = false;
                                            echo '<p class="error">введите данные</p>';
                                        } elseif (!is_numeric($price)) {
                                            $flag = false;
                                            echo '<p class="error">Цена должна быть числом</p>';
                                        } elseif (mb_strlen($price) > 10) {
                                            $flag = false;
                                            echo '<p class="error">превышено максимальное количество символов</p';
                                        }
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
                                    if (isset($_POST['add_fs'])) {
                                        if (empty($photographer_id)) {
                                            echo '<p class="error">Выберите фотографа</p>';
                                        }
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
                                    if (isset($_POST['add_fs'])) {
                                        if (empty($category_id)) {
                                            echo '<p class="error">Выберите категорию</p>';
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="form-label">введите описание</label>
                                    <textarea name="description" id="description" class="form-input textarea" maxlength="251" placeholder="описание"><?= htmlspecialchars($description) ?? '' ?></textarea>
                                    <?
                                    if (isset($_POST['add_fs'])) {
                                        if (empty($description)) {
                                            echo '<p class="error">Введите данные</p>';
                                        } elseif (mb_strlen($description) > 250) {
                                            echo '<p class="error">Превышено максимальное количество символов</p>';
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="location" class="form-label">введите локацию</label>
                                    <input type="text" id="location" name="location" class="form-input" placeholder="студия" value="<?= htmlspecialchars($location) ?? '' ?>" maxlength="31">
                                    <?
                                    if (isset($_POST['add_fs'])) {
                                        if (empty($location)) {
                                            echo '<p class="error">Введите данные</p>';
                                        } elseif (mb_strlen($location) > 30) {
                                            echo '<p class="error">Превышено максимальное количество символов (100)</p>';
                                        }
                                    }
                                    ?>
                                </div>

                                <!-- Кнопка добавления -->
                                <input type="submit" name="add_fs" class="btn btn-hover-red-wh-fon" value="добавить">

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