<div class="personal-cabinet-wrapper mt80">
    <?
    if (isset($_SESSION['USER'])) {
        if ($USER['role'] == 2) {

            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $sql = "SELECT * FROM `user` WHERE `id`='$id'";
                $res = $connect->query($sql)->fetch();
                $old_img = $res['img'];
            }

            // Сохраняем текущее изображение в переменную
            $current_img = $res['img'] ?? $USER['img'];

            if (isset($_POST['update'])) {
                $name = $_POST['name'];
                $surname = $_POST['surname'];
                $phone = $_POST['phone'];

                // Используем текущее изображение по умолчанию
                $img = $current_img;

                // Если загружено новое изображение - обрабатываем его
                if (!empty($_FILES['img']['name']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
                    $img = "assets/img/" . time() . $_FILES['img']['name'];
                    move_uploaded_file($_FILES['img']['tmp_name'], $img);
                }

                $flag = true;

                $errors = [
                    '<p class="error">Введите данные</p>',
                    '<p class="error">Телефон должен содержать 11 цифр</p>',
                    '<p class="error">Неверный формат телефона</p>',
                    '<p class="error">Такой номер уже зарегистрирован</p>',
                    '<p class="error">Превышено максимальное количество символов</p>'
                ];
            }
    ?>
            <div class="container">
                <div class="personal-cabinet">
                    <nav class="sidebar">
                        <div class="user-info">
                            <div class="avatar">
                                <img src="<?= $current_img ?>" alt="фото профиля">
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
                            <h2 class="title">Редактирование профиля</h2>
                            <form method="post" name="update" enctype="multipart/form-data">
                                <div class="img_text">
                                    <div class="label_input photo_acc">
                                        <label>перетащите сюда изображение или кликните для выбора</label>
                                        <!-- Единый контейнер для отображения и загрузки -->
                                        <div class="file-input-container acc-file" id="fileContainer">
                                            <input type="file" id="imageUpload" class="file-input" name="img" accept="image/*">
                                            <img id="imagePreview" class="preview-image" src="<?= $current_img ?>" style="display: block;">
                                        </div>
                                    </div>
                                    <div class="text_profile">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="name">имя</label>
                                    <input type="text" id="name" name="name" value="<?= $name ?? $USER['name'] ?>" maxlength="16">
                                    <?
                                    if (isset($_POST['update'])) {
                                        if (empty($name)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($name) > 15) {
                                            $flag = false;
                                            echo $errors[4];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="surname">фамилия</label>
                                    <input type="text" id="surname" name="surname" value="<?= $surname ?? $USER['surname'] ?>" maxlength="31">
                                    <?
                                    if (isset($_POST['update'])) {
                                        if (mb_strlen($surname) > 30) {
                                            $flag = false;
                                            echo $errors[4];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="phoneInput">телефон</label>
                                    <input type="text"
                                        id="phoneInput"
                                        name="phone"
                                        placeholder="+7 (___) ___-__-__"
                                        maxlength="18"
                                        value="<?= $phone ?? $USER['phone'] ?>">
                                    <?
                                    if (isset($_POST['update'])) {
                                        $phone = $_POST['phone'] ?? '';
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

                                        if (empty($cleanPhone)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (strlen($cleanPhone) !== 11) {
                                            $flag = false;
                                            echo $errors[1];
                                        } elseif (!preg_match('/^[78]\d{10}$/', $cleanPhone)) {
                                            $flag = false;
                                            echo $errors[2];
                                        } else {
                                            $sql = "SELECT * FROM `user` WHERE `phone`='$phone' AND `id`!='$id'";
                                            $res = $connect->query($sql)->fetchColumn();
                                            if ($res != 0) {
                                                $flag = false;
                                                echo $errors[3];
                                            }
                                        }
                                    }
                                    ?>
                                </div>

                                <input type="submit" class="btn btn-hover-red-wh-fon" value="сохранить" name="update">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Используем флаг чтобы предотвратить множественные обработчики
                let fileHandlerInitialized = false;

                function initializeFileHandlers() {
                    if (fileHandlerInitialized) return;
                    fileHandlerInitialized = true;

                    const fileContainer = document.getElementById('fileContainer');
                    const imageUpload = document.getElementById('imageUpload');
                    const imagePreview = document.getElementById('imagePreview');

                    // Обработка клика на контейнер с изображением
                    fileContainer.addEventListener('click', function(e) {
                        // Предотвращаем всплытие события
                        e.stopPropagation();
                        imageUpload.click();
                    });

                    // Обработка выбора файла
                    imageUpload.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                imagePreview.src = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    // Drag and drop
                    fileContainer.addEventListener('dragover', function(e) {
                        e.preventDefault();
                        this.classList.add('dragover');
                    });

                    fileContainer.addEventListener('dragleave', function(e) {
                        e.preventDefault();
                        this.classList.remove('dragover');
                    });

                    fileContainer.addEventListener('drop', function(e) {
                        e.preventDefault();
                        this.classList.remove('dragover');

                        const files = e.dataTransfer.files;
                        if (files.length > 0) {
                            const file = files[0];
                            if (file && file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    imagePreview.src = e.target.result;
                                };
                                reader.readAsDataURL(file);

                                // Обновляем input file
                                const dt = new DataTransfer();
                                dt.items.add(file);
                                imageUpload.files = dt.files;
                            }
                        }
                    });

                    // Предотвращаем клик на самом input file
                    imageUpload.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }

                // Инициализируем обработчики после загрузки DOM
                document.addEventListener('DOMContentLoaded', function() {
                    initializeFileHandlers();
                });

                // Маска телефона
                document.getElementById('phoneInput').addEventListener('input', function(e) {
                    // Оставляем только цифры и ограничиваем 11 символами
                    let numbers = this.value.replace(/\D/g, '').substr(0, 11);

                    // Форматируем номер
                    let formatted = '+7';
                    if (numbers.length > 1) formatted += ' (' + numbers.substr(1, 3);
                    if (numbers.length > 4) formatted += ') ' + numbers.substr(4, 3);
                    if (numbers.length > 7) formatted += '-' + numbers.substr(7, 2);
                    if (numbers.length > 9) formatted += '-' + numbers.substr(9, 2);

                    this.value = formatted;
                });
            </script>

    <?
            if (isset($_POST['update'])) {
                if ($flag) {
                    $sql = "UPDATE `user` SET 
                            `img`='$img',
                            `name`='$name',
                            `surname`='$surname', 
                            `phone` ='$phone' WHERE `id`='$id'";

                    $result = $connect->query($sql);

                    if ($result) {
                        echo '<script>document.location.href="?page=adminLk&id=' . $id . '"</script>';
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