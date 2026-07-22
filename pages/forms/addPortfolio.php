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
                            <h2 class="title">добавление в портфолио</h2>

                            <?php
                            // Определяем переменные
                            $name = '';
                            $date = '';
                            $location = '';
                            $fs_id = '';
                            $flag = true;
                            $uploadedImages = [];
                            $imageError = '';
                            $dateError = '';

                            // Получаем фотосессии фотографа для выбора
                            $sql_sessions = "SELECT id, name FROM fs WHERE photogs_id = ?";
                            $stmt_sessions = $connect->prepare($sql_sessions);
                            $stmt_sessions->execute([$USER['id']]);
                            $sessions = $stmt_sessions->fetchAll();

                            if (isset($_POST['add_portfolio'])) {
                                $name = $_POST['name'] ?? '';
                                $date_input = $_POST['date'] ?? '';
                                $location = $_POST['location'] ?? '';
                                $fs_id = $_POST['fs_id'] ?? '';

                                $flag = true;
                                $uploadedImages = [];
                                $imageError = '';
                                $dateError = '';

                                // Проверка обязательных полей
                                if (empty($name)) {
                                    $flag = false;
                                }
                                if (empty($date_input)) {
                                    $flag = false;
                                    $dateError = 'Введите дату съемки';
                                }
                                if (empty($location)) {
                                    $flag = false;
                                }
                                if (empty($fs_id)) {
                                    $flag = false;
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

                                // Проверка загрузки изображений
                                if (!empty($_FILES['images']['name'][0])) {
                                    $uploadedFilesCount = count(array_filter($_FILES['images']['name']));

                                    if ($uploadedFilesCount < 1) {
                                        $flag = false;
                                        $imageError = '<p class="error">Необходимо загрузить хотя бы 1 изображение.</p>';
                                    } else {
                                        foreach ($_FILES['images']['name'] as $index => $filename) {
                                            if (!empty($filename)) {
                                                $imagePath = 'assets/img/portfolio/' . uniqid() . '_' . $filename;
                                                if (move_uploaded_file($_FILES['images']['tmp_name'][$index], $imagePath)) {
                                                    $uploadedImages[] = $imagePath;
                                                } else {
                                                    $flag = false;
                                                    $imageError = '<p class="error">Ошибка загрузки изображения: ' . $filename . '</p>';
                                                }
                                            }
                                        }

                                        if (count($uploadedImages) < 1) {
                                            $flag = false;
                                            $imageError = '<p class="error">Не удалось загрузить изображения</p>';
                                        }
                                    }
                                } else {
                                    $flag = false;
                                    $imageError = '<p class="error">Необходимо загрузить хотя бы 1 изображение</p>';
                                }

                                // Если все проверки пройдены, добавляем в БД
                                if ($flag && count($uploadedImages) >= 1) {
                                    try {
                                        // Добавляем работу в портфолио
                                        $portfolio = $connect->prepare("INSERT INTO `portfolio` (`name`, `date`, `location`, `fs`, `photogs_id`) 
                                                            VALUES (:name, :date, :location, :fs, :photogs_id)");
                                        $portfolio->execute([
                                            ':name' => $name,
                                            ':date' => $date,
                                            ':location' => $location,
                                            ':fs' => $fs_id,
                                            ':photogs_id' => $USER['id']
                                        ]);

                                        // Получаем ID добавленной работы
                                        $lastPortfolioID = $connect->lastInsertId();

                                        // Добавляем все изображения в таблицу imagesPortfolio
                                        foreach ($uploadedImages as $imagePath) {
                                            $images = $connect->prepare("INSERT INTO `imagesPortfolio` (`portfolio_id`, `filename`) 
                                                           VALUES (:portfolio_id, :filename)");
                                            $images->execute([
                                                ':portfolio_id' => $lastPortfolioID,
                                                ':filename' => $imagePath
                                            ]);
                                        }

                                        echo '<script>document.location.href="?page=lkPortfolioPh"</script>';
                                        exit;
                                    } catch (PDOException $e) {
                                        echo '<p class="error">Ошибка базы данных: ' . $e->getMessage() . '</p>';
                                    }
                                } else {
                                    if (empty($imageError) && empty($dateError)) {
                                        $imageError = '<p class="error">Пожалуйста, заполните все поля правильно и загрузите изображения</p>';
                                    }
                                }
                            }
                            ?>

                            <form method="post" class="form" enctype="multipart/form-data">
                                <div class="img_text">
                                    <div class="label_input photo_acc">
                                        <label>Перетащите сюда изображения или кликните для выбора</label>
                                        <div class="multiple-images-container">
                                            <!-- Контейнер для загрузки -->
                                            <div class="file-input-container acc-file multiple-file-input" id="multipleFileContainer">
                                                <input type="file" id="multipleImageUpload" class="file-input" name="images[]" accept="image/*" multiple>
                                                <div class="upload-content">
                                                    <div class="upload-text">+ Добавить фото</div>
                                                </div>
                                            </div>

                                            <!-- Контейнер для превью -->
                                            <div class="previews-container" id="previewsContainer"></div>
                                        </div>
                                        <div class="image-counter" id="imageCounter">
                                            <p>
                                                выбрано: 0 изображений
                                            </p>
                                        </div>

                                        <!-- ВЫВОД ОШИБКИ ЗАГРУЗКИ ИЗОБРАЖЕНИЙ -->
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
                                        for (let i = 0; i < files.length; i++) {
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
                                        imageCounter.textContent = `Выбрано: ${selectedFiles.length} изображений`;

                                        if (selectedFiles.length > 0) {
                                            imageCounter.style.color = 'green';
                                        } else {
                                            imageCounter.style.color = 'red';
                                        }

                                        // Обновляем input files
                                        const dt = new DataTransfer();
                                        selectedFiles.forEach(file => dt.items.add(file));
                                        multipleFileInput.files = dt.files;
                                    }
                                </script>

                                <!-- Поле названия работы -->
                                <div class="form-group">
                                    <label for="name" class="form-label">введите название работы</label>
                                    <input type="text" id="name" name="name" class="form-input" placeholder="Портретная серия" value="<?= htmlspecialchars($name) ?? '' ?>">
                                    <?php
                                    if (isset($_POST['add_portfolio'])) {
                                        if (empty($name)) {
                                            echo '<p class="error">Введите название работы</p>';
                                        }
                                    }
                                    ?>
                                </div>

                                <!-- Поле даты -->
                                <div class="form-group">
                                    <label for="date" class="form-label">введите дату съемки</label>
                                    <input type="text" id="date" name="date" class="form-input" placeholder="ДД.ММ.ГГГГ" value="<?= htmlspecialchars($_POST['date'] ?? '') ?>">
                                    <?php
                                    if (isset($_POST['add_portfolio'])) {
                                        if (!empty($dateError)) {
                                            echo '<p class="error">' . $dateError . '</p>';
                                        }
                                    }
                                    ?>
                                </div>

                                <!-- Поле локации -->
                                <div class="form-group">
                                    <label for="location" class="form-label">введите локацию</label>
                                    <input type="text" id="location" name="location" class="form-input" placeholder="студия/выездная" value="<?= htmlspecialchars($location) ?? '' ?>">
                                    <?php
                                    if (isset($_POST['add_portfolio'])) {
                                        if (empty($location)) {
                                            echo '<p class="error">Введите локацию</p>';
                                        }
                                    }
                                    ?>
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
                                    <?php
                                    if (isset($_POST['add_portfolio'])) {
                                        if (empty($fs_id)) {
                                            echo '<p class="error">Выберите фотосессию</p>';
                                        }
                                    }
                                    ?>
                                </div>

                                <!-- Кнопка добавления -->
                                <input type="submit" name="add_portfolio" class="btn btn-hover-red-wh-fon" value="добавить в портфолио">

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
<?
    } else {
        echo '<script>document.location.href="?page=error403"</script>';
    }
} else {
    echo '<script>document.location.href="?page=error403"</script>';
}
?>