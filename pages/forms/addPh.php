<?
if (isset($_SESSION['USER'])) {
    if ($USER['role'] == 2) {
        if (isset($_POST['add'])) {
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $password = $_POST['password'];
            $desc = $_POST['desc'];
            $experience = $_POST['experience'];
            $projects = $_POST['projects'];
            $exhibitions = $_POST['exhibitions'];

            $defaultImage = 'assets/img/modal/zaglushka.png'; // Путь к заглушке
            $img = $defaultImage;

            if (!empty($_FILES['img']['name'])) {
                $img = "assets/img/team/" . time() . $_FILES['img']['name'];
                move_uploaded_file($_FILES['img']['tmp_name'], $img);
            }

            $flag = true;

            $errors = [
                '<p class="error">Введите данные</p>',
                '<p class="error">Превышено максимальное количество символов</p>',
                '<p class="error">Телефон должен содержать 11 цифр</p>',
                '<p class="error">Неверный формат телефона</p>',
                '<p class="error">Такой номер уже зарегистрирован</p>'
            ];
        }
?>
        <div class="personal-cabinet-wrapper mt80">
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
                            <h2 class="title">добавление фотографа</h2>
                            <form method="post" name="add" enctype="multipart/form-data" class="form">
                                <div class="img_text">
                                    <div class="label_input photo_acc">
                                        <label>перетащите сюда изображение или кликните для выбора</label>
                                        <div class="file-input-container acc-file" id="fileContainer">
                                            <input type="file" id="imageUpload" class="file-input" name="img" accept="image/*">
                                            <img id="imagePreview" class="preview-image" src="assets/img/modal/zaglushka.png">
                                        </div>
                                    </div>
                                    <div class="text_profile">
                                    </div>
                                </div>

                                <!-- Поле имени -->
                                <div class="form-group">
                                    <label for="name" class="form-label">введите имя</label>
                                    <input type="text" id="name" class="form-input" placeholder="Каролина" name="name" value="<?= $name ?? '' ?>" maxlength="16">
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($name)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($name) > 15) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="phone" class="form-label">введите номер телефона</label>
                                    <input type="text" id="phone" class="form-input" placeholder="+7 (999) 999-99-99" name="phone" value="<?= $phone ?? '' ?>" maxlength="18">
                                    <?
                                    if (isset($_POST['add'])) {
                                        $phone = $_POST['phone'] ?? '';
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

                                        if (empty($cleanPhone)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (strlen($cleanPhone) !== 11) {
                                            $flag = false;
                                            echo $errors[2];
                                        } elseif (!preg_match('/^[78]\d{10}$/', $cleanPhone)) {
                                            $flag = false;
                                            echo $errors[3];
                                        } else {
                                            $sql = "SELECT * FROM `user` WHERE `phone`='$phone'";
                                            $res = $connect->query($sql)->fetchColumn();
                                            if ($res != 0) {
                                                $flag = false;
                                                echo $errors[4];
                                            }
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="password" class="form-label">введите пароль</label>
                                    <input type="password" id="password" class="form-input" placeholder="******" name="password" value="<?= $password ?? '' ?>" maxlength="7">
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($password)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($password) > 6) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="desc" class="form-label">введите описание</label>
                                    <textarea id="desc" class="form-input textarea" name="desc" maxlength="75"><?= $desc ?? '' ?></textarea>
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($desc)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($desc) > 74) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="experience" class="form-label">введите опыт</label>
                                    <input type="text" id="experience" class="form-input" placeholder="5" name="experience" value="<?= $experience ?? '' ?>" maxlength="3">
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($experience)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($experience) > 2) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="projects" class="form-label">введите количество проектов</label>
                                    <input type="text" id="projects" class="form-input" placeholder="100" name="projects" value="<?= $projects ?? '' ?>" maxlength="3">
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($projects)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($projects) > 2) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="exhibitions" class="form-label">введите количество выставок</label>
                                    <input type="text" id="exhibitions" class="form-input" placeholder="10" name="exhibitions" value="<?= $exhibitions ?? '' ?>" maxlength="3">
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($exhibitions)) {
                                            $flag = false;
                                            echo $errors[0];
                                        }
                                    } elseif (mb_strlen($exhibitions) > 2) {
                                        $flag = false;
                                        echo $errors[1];
                                    }
                                    ?>
                                </div>

                                <!-- Кнопка добавления -->
                                <input type="submit" class="btn btn-hover-red-wh-fon" value="добавить" name="add">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Используем флаг для предотвращения множественных обработчиков
            let fileHandlersInitialized = false;

            function initializeFileHandlers() {
                if (fileHandlersInitialized) return;
                fileHandlersInitialized = true;

                const fileContainer = document.getElementById('fileContainer');
                const imageUpload = document.getElementById('imageUpload');
                const imagePreview = document.getElementById('imagePreview');

                // Один обработчик клика на контейнер
                fileContainer.addEventListener('click', function(e) {
                    e.stopPropagation();
                    imageUpload.click();
                });

                // Один обработчик выбора файла
                imageUpload.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        imagePreview.src = 'assets/img/modal/zaglushka.png';
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

                            const dt = new DataTransfer();
                            dt.items.add(file);
                            imageUpload.files = dt.files;
                        }
                    } else {
                        imagePreview.src = 'assets/img/modal/zaglushka.png';
                    }
                });

                // Предотвращаем клик на самом input
                imageUpload.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Инициализируем обработчики после загрузки DOM
            document.addEventListener('DOMContentLoaded', function() {
                initializeFileHandlers();
            });

            // Маска телефона
            document.getElementById('phone').addEventListener('input', function(e) {
                let numbers = this.value.replace(/\D/g, '').substr(0, 11);

                let formatted = '+7';
                if (numbers.length > 1) formatted += ' (' + numbers.substr(1, 3);
                if (numbers.length > 4) formatted += ') ' + numbers.substr(4, 3);
                if (numbers.length > 7) formatted += '-' + numbers.substr(7, 2);
                if (numbers.length > 9) formatted += '-' + numbers.substr(9, 2);

                this.value = formatted;
            });
        </script>

<?
        if (isset($_POST['add'])) {
            if ($flag) {
                // Сначала добавляем в таблицу user
                $sql_user = "INSERT INTO `user` (`img`, `name`, `surname`, `phone`, `password`, `role`) 
                            VALUES ('$img', '$name', '', '$phone', '$password', 3)";

                $result_user = $connect->query($sql_user);
                $user_id = $connect->lastInsertId();

                if ($result_user && $user_id) {
                    // Затем добавляем в таблицу photogs
                    $sql_photogs = "INSERT INTO `photogs` (`id`, `desc`, `experience`, `projects`, `exhibitions`) 
                                   VALUES ('$user_id', '$desc', '$experience', '$projects', '$exhibitions')";

                    $result_photogs = $connect->query($sql_photogs);

                    if ($result_photogs) {
                        echo '<script>document.location.href="?page=adminPh"</script>';
                    } else {
                        echo '<p class="error">Ошибка при добавлении фотографа</p>';
                    }
                } else {
                    echo '<p class="error">Ошибка при создании пользователя</p>';
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