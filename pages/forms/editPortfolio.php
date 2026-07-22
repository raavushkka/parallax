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
                            <?php
                            // Получаем ID работы для редактирования
                            $portfolio_id = $_GET['id'] ?? 0;
                            $is_edit = !empty($portfolio_id);

                            // Определяем переменные
                            $name = '';
                            $date = '';
                            $location = '';
                            $fs_id = '';
                            $existingImages = [];
                            $dateError = '';

                            // Получаем фотосессии фотографа для выбора
                            $sql_sessions = "SELECT id, name FROM fs WHERE photogs_id = ?";
                            $stmt_sessions = $connect->prepare($sql_sessions);
                            $stmt_sessions->execute([$USER['id']]);
                            $sessions = $stmt_sessions->fetchAll();

                            // Если это редактирование, загружаем данные работы
                            if ($is_edit) {
                                try {
                                    $stmt = $connect->prepare("SELECT * FROM portfolio WHERE id = ? AND photogs_id = ?");
                                    $stmt->execute([$portfolio_id, $USER['id']]);
                                    $portfolio_data = $stmt->fetch(PDO::FETCH_ASSOC);

                                    if ($portfolio_data) {
                                        $name = $portfolio_data['name'];
                                        // Преобразуем дату из YYYY-MM-DD в DD.MM.YYYY для отображения
                                        $db_date = $portfolio_data['date'];
                                        if (!empty($db_date)) {
                                            $date_parts = explode('-', $db_date);
                                            if (count($date_parts) === 3) {
                                                $date = $date_parts[2] . '.' . $date_parts[1] . '.' . $date_parts[0];
                                            }
                                        }
                                        $location = $portfolio_data['location'];
                                        $fs_id = $portfolio_data['fs'];

                                        // Загружаем существующие изображения
                                        $img_stmt = $connect->prepare("SELECT * FROM imagesPortfolio WHERE portfolio_id = ?");
                                        $img_stmt->execute([$portfolio_id]);
                                        $existingImages = $img_stmt->fetchAll(PDO::FETCH_ASSOC);
                                    } else {
                                        echo '<script>document.location.href="?page=lkPortfolioPh"</script>';
                                        exit;
                                    }
                                } catch (PDOException $e) {
                                    echo '<p class="error">Ошибка загрузки данных: ' . $e->getMessage() . '</p>';
                                }
                            }

                            if (isset($_POST['save_portfolio'])) {
                                $name = $_POST['name'] ?? '';
                                $date_input = $_POST['date'] ?? '';
                                $location = $_POST['location'] ?? '';
                                $fs_id = $_POST['fs_id'] ?? '';

                                $flag = true;
                                $uploadedImages = [];
                                $dateError = '';

                                // Проверка обязательных полей
                                if (empty($name)) {
                                    $flag = false;
                                    echo '<p class="error">Введите название работы</p>';
                                }
                                if (empty($date_input)) {
                                    $flag = false;
                                    $dateError = 'Введите дату съемки';
                                }
                                if (empty($location)) {
                                    $flag = false;
                                    echo '<p class="error">Введите локацию</p>';
                                }
                                if (empty($fs_id)) {
                                    $flag = false;
                                    echo '<p class="error">Выберите фотосессию</p>';
                                }

                                // Проверка формата даты
                                if (!empty($date_input) && empty($dateError)) {
                                    // Проверяем формат DD.MM.YYYY
                                    $regex = '/^(\d{2})\.(\d{2})\.(\d{4})$/';
                                    if (!preg_match($regex, $date_input)) {
                                        $flag = false;
                                        $dateError = 'Неверный формат даты. Используйте ДД.ММ.ГГГГ';
                                    } else {
                                        $dateParts = explode('.', $date_input);
                                        $day = (int)$dateParts[0];
                                        $month = (int)$dateParts[1];
                                        $year = (int)$dateParts[2];

                                        // Проверяем корректность даты
                                        if (!checkdate($month, $day, $year) || $year < 1900 || $year > 2100) {
                                            $flag = false;
                                            $dateError = 'Неверная дата. Проверьте правильность ввода';
                                        } else {
                                            // Преобразуем в формат YYYY-MM-DD для базы данных
                                            $date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                                        }
                                    }
                                }

                                // Если все проверки пройдены, сохраняем в БД
                                if ($flag) {
                                    try {
                                        if ($is_edit) {
                                            // Обновляем работу в портфолио
                                            $portfolio = $connect->prepare("UPDATE `portfolio` SET `name` = ?, `date` = ?, `location` = ?, `fs` = ? WHERE `id` = ? AND `photogs_id` = ?");
                                            $portfolio->execute([
                                                $name,
                                                $date,
                                                $location,
                                                $fs_id,
                                                $portfolio_id,
                                                $USER['id']
                                            ]);
                                        } else {
                                            // Добавляем новую работу в портфолио
                                            $portfolio = $connect->prepare("INSERT INTO `portfolio` (`name`, `date`, `location`, `fs`, `photogs_id`) 
                                                                VALUES (?, ?, ?, ?, ?)");
                                            $portfolio->execute([
                                                $name,
                                                $date,
                                                $location,
                                                $fs_id,
                                                $USER['id']
                                            ]);
                                            $portfolio_id = $connect->lastInsertId();
                                        }

                                        // Обрабатываем загруженные изображения
                                        $upload_dir = 'assets/img/portfolio/';
                                        if (!is_dir($upload_dir)) {
                                            mkdir($upload_dir, 0755, true);
                                        }

                                        // Обрабатываем каждое изображение отдельно
                                        for ($i = 0; $i < 4; $i++) {
                                            $file_key = 'image_' . $i;

                                            if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK && !empty($_FILES[$file_key]['name'])) {
                                                $filename = $_FILES[$file_key]['name'];
                                                $imagePath = $upload_dir . uniqid() . '_' . $filename;

                                                if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $imagePath)) {
                                                    // Если это редактирование и есть существующее изображение по индексу, обновляем его
                                                    if ($is_edit && isset($existingImages[$i])) {
                                                        // Удаляем старый файл
                                                        if (file_exists($existingImages[$i]['filename'])) {
                                                            unlink($existingImages[$i]['filename']);
                                                        }
                                                        // Обновляем запись в базе
                                                        $update_stmt = $connect->prepare("UPDATE imagesPortfolio SET filename = ? WHERE images_id = ?");
                                                        $update_stmt->execute([
                                                            $imagePath,
                                                            $existingImages[$i]['images_id']
                                                        ]);
                                                    } else {
                                                        // Добавляем новое изображение
                                                        $images = $connect->prepare("INSERT INTO `imagesPortfolio` (`portfolio_id`, `filename`) 
                                                                       VALUES (?, ?)");
                                                        $images->execute([
                                                            $portfolio_id,
                                                            $imagePath
                                                        ]);
                                                    }
                                                    $uploadedImages[] = $imagePath;
                                                }
                                            }
                                        }

                                        // Если это новая работа и нет изображений, показываем ошибку
                                        if (!$is_edit && empty($uploadedImages)) {
                                            echo '<p class="error">Необходимо загрузить хотя бы одно изображение</p>';
                                        } else {
                                            echo '<script>document.location.href="?page=lkPortfolioPh"</script>';
                                            exit;
                                        }
                                    } catch (PDOException $e) {
                                        echo '<p class="error">Ошибка базы данных: ' . $e->getMessage() . '</p>';
                                    }
                                }
                            }
                            ?>

                            <h2 class="title"><?= $is_edit ? 'редактирование работы' : 'добавление в портфолио' ?></h2>

                            <form method="post" class="form" enctype="multipart/form-data">
                                <?php if ($is_edit): ?>
                                    <input type="hidden" name="portfolio_id" value="<?= $portfolio_id ?>">
                                <?php endif; ?>

                                <div class="img_text">
                                    <div class="label_input photo_acc">
                                        <label>Кликните на изображение чтобы изменить его</label>
                                        <div class="multiple-images-container">
                                            <!-- Контейнер для превью -->
                                            <div class="previews-container" id="previewsContainer">
                                                <?php for ($i = 0; $i < 4; $i++): ?>
                                                    <?php
                                                    $hasExistingImage = $is_edit && isset($existingImages[$i]);
                                                    $imageSrc = $hasExistingImage ? $existingImages[$i]['filename'] : '';
                                                    ?>
                                                    <div class="preview-item <?= !$hasExistingImage ? 'empty' : '' ?>" data-index="<?= $i ?>">
                                                        <?php if ($hasExistingImage): ?>
                                                            <img src="<?= $imageSrc ?>" alt="Изображение <?= $i + 1 ?>">
                                                            <input type="hidden" name="existing_images[<?= $i ?>]" value="<?= $existingImages[$i]['images_id'] ?>">
                                                        <?php else: ?>
                                                            <div class="empty-slot">+</div>
                                                        <?php endif; ?>
                                                        <div class="preview-count"><?= $i + 1 ?></div>
                                                        <div class="change-overlay">✎</div>
                                                        <!-- Отдельный input для каждого изображения -->
                                                        <input type="file" class="file-input single-file-input" name="image_<?= $i ?>" accept="image/*" style="display: none;" data-index="<?= $i ?>">
                                                    </div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>

                                        <p class="info">Кликните на изображение чтобы изменить его. Можно изменить несколько изображений за раз.</p>
                                    </div>
                                    <div class="text_profile"></div>
                                </div>

                                <script>
                                    // Клик по превью для изменения конкретного изображения
                                    document.querySelectorAll('.preview-item').forEach(previewItem => {
                                        previewItem.addEventListener('click', function(e) {
                                            const index = this.getAttribute('data-index');
                                            const fileInput = this.querySelector('.single-file-input');
                                            fileInput.click();
                                        });
                                    });

                                    // Обработка выбора файла для каждого инпута
                                    document.querySelectorAll('.single-file-input').forEach(fileInput => {
                                        fileInput.addEventListener('change', function(e) {
                                            if (this.files.length > 0) {
                                                const file = this.files[0];
                                                const index = this.getAttribute('data-index');

                                                if (file && file.type.startsWith('image/')) {
                                                    updatePreview(file, index);
                                                }
                                            }
                                        });
                                    });

                                    function updatePreview(file, index) {
                                        const reader = new FileReader();
                                        reader.onload = function(e) {
                                            const previewItems = document.querySelectorAll('.preview-item');
                                            const previewItem = previewItems[index];
                                            const img = previewItem.querySelector('img');

                                            if (img) {
                                                // Обновляем существующее изображение
                                                img.src = e.target.result;
                                            } else {
                                                // Создаем новое изображение
                                                const newImg = document.createElement('img');
                                                newImg.src = e.target.result;
                                                newImg.alt = 'Preview ' + (parseInt(index) + 1);

                                                // Удаляем пустой слот
                                                const emptySlot = previewItem.querySelector('.empty-slot');
                                                if (emptySlot) {
                                                    emptySlot.remove();
                                                }

                                                // Добавляем изображение
                                                previewItem.insertBefore(newImg, previewItem.querySelector('.preview-count'));
                                                previewItem.classList.remove('empty');
                                            }
                                        };
                                        reader.readAsDataURL(file);
                                    }

                                    // Drag and drop для каждого превью
                                    document.querySelectorAll('.preview-item').forEach(previewItem => {
                                        previewItem.addEventListener('dragover', function(e) {
                                            e.preventDefault();
                                            this.classList.add('dragover');
                                        });

                                        previewItem.addEventListener('dragleave', function(e) {
                                            e.preventDefault();
                                            this.classList.remove('dragover');
                                        });

                                        previewItem.addEventListener('drop', function(e) {
                                            e.preventDefault();
                                            this.classList.remove('dragover');

                                            const files = e.dataTransfer.files;
                                            if (files.length > 0) {
                                                const file = files[0];
                                                const index = this.getAttribute('data-index');
                                                const fileInput = this.querySelector('.single-file-input');

                                                if (file && file.type.startsWith('image/')) {
                                                    // Создаем новый FileList для input
                                                    const dt = new DataTransfer();
                                                    dt.items.add(file);
                                                    fileInput.files = dt.files;

                                                    updatePreview(file, index);
                                                }
                                            }
                                        });
                                    });
                                </script>

                                <!-- Поле названия работы -->
                                <div class="form-group">
                                    <label for="name" class="form-label">введите название работы</label>
                                    <input type="text" id="name" name="name" class="form-input" placeholder="Портретная серия" value="<?= htmlspecialchars($name) ?>">
                                </div>

                                <!-- Поле даты -->
                                <div class="form-group">
                                    <label for="date" class="form-label">введите дату съемки</label>
                                    <input type="text" id="date" name="date" class="form-input" placeholder="ДД.ММ.ГГГГ" value="<?= htmlspecialchars($date) ?>">
                                    <?php
                                    if (isset($_POST['save_portfolio']) && !empty($dateError)) {
                                        echo '<p class="error">' . $dateError . '</p>';
                                    }
                                    ?>
                                </div>

                                <!-- Поле локации -->
                                <div class="form-group">
                                    <label for="location" class="form-label">введите локацию</label>
                                    <input type="text" id="location" name="location" class="form-input" placeholder="студия/выездная" value="<?= htmlspecialchars($location) ?>">
                                </div>

                                <!-- Выбор фотосессии -->
                                <div class="form-group">
                                    <div class="select-form">
                                        <select class="custom-select" name="fs_id">
                                            <option value="" disabled selected>выберите фотосессию</option>
                                            <?php
                                            foreach ($sessions as $session) {
                                                $selected = ($fs_id == $session['id']) ? 'selected' : '';
                                                echo "<option value='{$session['id']}' $selected>{$session['name']}</option>";
                                            }
                                            ?>
                                        </select>
                                        <div class="select-arrow">
                                            <img src="assets/img/catalog/arrow_down.svg" alt="▼">
                                        </div>
                                    </div>
                                </div>

                                <!-- Кнопка сохранения -->
                                <input type="submit" name="save_portfolio" class="btn btn-hover-red-wh-fon" value="<?= $is_edit ? 'сохранить изменения' : 'добавить в портфолио' ?>">

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Правильная маска для поля даты
            document.addEventListener('DOMContentLoaded', function() {
                const dateInput = document.getElementById('date');

                dateInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');

                    // Ограничиваем длину 8 цифрами
                    if (value.length > 8) {
                        value = value.substring(0, 8);
                    }

                    // Форматируем по шаблону ДД.ММ.ГГГГ
                    let formattedValue = '';
                    if (value.length > 0) {
                        formattedValue = value.substring(0, 2);
                    }
                    if (value.length > 2) {
                        formattedValue += '.' + value.substring(2, 4);
                    }
                    if (value.length > 4) {
                        formattedValue += '.' + value.substring(4, 8);
                    }

                    e.target.value = formattedValue;
                });

                // Запрещаем ввод любых символов кроме цифр и управляющих клавиш
                dateInput.addEventListener('keydown', function(e) {
                    // Разрешаем: backspace, delete, tab, escape, enter, стрелки
                    if ([46, 8, 9, 27, 13, 37, 38, 39, 40].includes(e.keyCode) ||
                        // Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                        (e.keyCode === 65 && e.ctrlKey === true) ||
                        (e.keyCode === 67 && e.ctrlKey === true) ||
                        (e.keyCode === 86 && e.ctrlKey === true) ||
                        (e.keyCode === 88 && e.ctrlKey === true)) {
                        return;
                    }

                    // Запрещаем все, кроме цифр
                    if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
                });

                // Валидация при потере фокуса
                dateInput.addEventListener('blur', function() {
                    validateDate(dateInput);
                });

                function validateDate(input) {
                    const value = input.value;
                    if (!value) {
                        input.style.borderColor = '';
                        return;
                    }

                    const regex = /^(\d{2})\.(\d{2})\.(\d{4})$/;
                    const match = value.match(regex);

                    if (!match) {
                        input.style.borderColor = 'red';
                        return false;
                    }

                    const day = parseInt(match[1]);
                    const month = parseInt(match[2]);
                    const year = parseInt(match[3]);

                    // Проверяем корректность даты
                    const date = new Date(year, month - 1, day);
                    const isValid = date.getDate() === day &&
                        date.getMonth() === month - 1 &&
                        date.getFullYear() === year &&
                        year >= 1900 && year <= 2100;

                    input.style.borderColor = isValid ? '' : 'red';
                    return isValid;
                }

                // Автоматическая расстановка точек при вводе
                dateInput.addEventListener('keyup', function(e) {
                    const value = e.target.value.replace(/\D/g, '');

                    if (e.key !== 'Backspace' && e.key !== 'Delete') {
                        if (value.length === 2 && e.target.value.length === 2) {
                            e.target.value = value + '.';
                        } else if (value.length === 4 && e.target.value.length === 5) {
                            e.target.value = value.substring(0, 2) + '.' + value.substring(2, 4) + '.';
                        }
                    }
                });
            });
        </script>

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
                transition: border-color 0.3s;
            }

            .preview-item.empty {
                background: #f8f9fa;
            }

            .preview-item.dragover {
                border-color: #B12221;
                background: rgba(177, 34, 33, 0.1);
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
                background: rgba(0, 0, 0, 0.7);
                color: white;
                padding: 2px 6px;
                border-radius: 4px;
                font-size: 12px;
                z-index: 2;
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
                z-index: 1;
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
<?
    } else {
        echo '<script>document.location.href="?page=error403"</script>';
    }
} else {
    echo '<script>document.location.href="?page=error403"</script>';
}
?>