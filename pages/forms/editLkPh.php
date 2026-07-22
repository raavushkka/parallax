<? if (isset($_SESSION['USER'])) {
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
                            <h2 class="title">редактирование профиля</h2>

                            <?php
                            // Получаем данные фотографа
                            $sql = "SELECT * FROM photogs WHERE id = ?";
                            $stmt = $connect->prepare($sql);
                            $stmt->execute([$USER['id']]);
                            $photographer_data = $stmt->fetch();

                            // Получаем фотосессии фотографа
                            $sql_sessions = "SELECT name FROM fs WHERE photogs_id = ?";
                            $stmt_sessions = $connect->prepare($sql_sessions);
                            $stmt_sessions->execute([$USER['id']]);
                            $sessions = $stmt_sessions->fetchAll();
                            $sessions_text = implode(', ', array_column($sessions, 'name'));
                            ?>

                            <form class="form" method="POST" enctype="multipart/form-data">
                                <div class="img_text">
                                    <div class="label_input photo_acc">
                                        <label>Перетащите сюда изображение или кликните для выбора</label>
                                        <div class="file-input-container acc-file" id="accountFileContainer">
                                            <input type="file" id="accountImageUpload" class="file-input" name="img" accept="image/*">
                                            <img id="accountImagePreview" class="preview-image" src="<?= $USER['img'] ?>">
                                        </div>
                                    </div>
                                    <div class="text_profile">
                                    </div>
                                </div>

                                <script>
                                    document.getElementById('accountImageUpload').addEventListener('change', function(e) {
                                        const file = e.target.files[0];
                                        if (file) {
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                document.getElementById('accountImagePreview').src = e.target.result;
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    });
                                </script>

                                <!-- Поле имени -->
                                <div class="form-group">
                                    <label for="name" class="form-label">введите имя</label>
                                    <input type="text" id="name" name="name" class="form-input" value="<?= htmlspecialchars($USER['name']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="desc" class="form-label">введите описание</label>
                                    <textarea id="desc" name="desc" class="form-input textarea" required><?= htmlspecialchars($photographer_data['desc']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="experience" class="form-label">введите опыт</label>
                                    <input type="text" id="experience" name="experience" class="form-input" value="<?= htmlspecialchars($photographer_data['experience']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="projects" class="form-label">введите количество проектов</label>
                                    <input type="number" id="projects" name="projects" class="form-input" value="<?= htmlspecialchars($photographer_data['projects']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="exhibitions" class="form-label">введите количество выставок</label>
                                    <input type="number" id="exhibitions" name="exhibitions" class="form-input" value="<?= htmlspecialchars($photographer_data['exhibitions']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="sessions" class="form-label">фотосессии (через запятую)</label>
                                    <input type="text" id="sessions" name="sessions" class="form-input" value="<?= htmlspecialchars($sessions_text) ?>" placeholder="индивидуальная, портретная, креативная">
                                </div>

                                <!-- Кнопка сохранения -->
                                <input type="submit" name="save_profile" class="btn btn-hover-red-wh-fon" value="сохранить">
                            </form>

                            <?php
                            // Обработка сохранения профиля
                            if (isset($_POST['save_profile'])) {
                                $name = $_POST['name'];
                                $desc = $_POST['desc'];
                                $experience = $_POST['experience'];
                                $projects = $_POST['projects'];
                                $exhibitions = $_POST['exhibitions'];
                                $sessions_input = $_POST['sessions'];

                                // Обработка загрузки изображения
                                if (isset($_FILES['img']) && $_FILES['img']['error'] === 0) {
                                    $upload_dir = 'assets/img/team/';
                                    $file_name = time() . '_' . basename($_FILES['img']['name']);
                                    $upload_file = $upload_dir . $file_name;

                                    if (move_uploaded_file($_FILES['img']['tmp_name'], $upload_file)) {
                                        // Обновляем фото в таблице user
                                        $sql_update_img = "UPDATE user SET img = ? WHERE id = ?";
                                        $stmt_update_img = $connect->prepare($sql_update_img);
                                        $stmt_update_img->execute([$upload_file, $USER['id']]);
                                    }
                                }

                                // Обновляем имя в таблице user
                                $sql_update_user = "UPDATE user SET name = ? WHERE id = ?";
                                $stmt_update_user = $connect->prepare($sql_update_user);
                                $stmt_update_user->execute([$name, $USER['id']]);

                                // Обновляем данные в таблице photogs
                                $sql_update_photog = "UPDATE photogs SET desc = ?, experience = ?, projects = ?, exhibitions = ? WHERE id = ?";
                                $stmt_update_photog = $connect->prepare($sql_update_photog);
                                $stmt_update_photog->execute([$desc, $experience, $projects, $exhibitions, $USER['id']]);

                                // Обработка фотосессий (упрощенная версия)
                                if (!empty($sessions_input)) {
                                    $sessions_array = array_map('trim', explode(',', $sessions_input));
                                    // Здесь можно добавить логику обновления фотосессий в таблице fs
                                }

                                echo '<script>document.location.href="?page=lkPh"</script>';
                            }
                            ?>

                        </div>
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