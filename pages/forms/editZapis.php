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
                            <h2 class="title">редактирование записи</h2>

                            <?php
                            // Получаем ID записи для редактирования
                            $zapis_id = $_GET['id'] ?? 0;

                            // Определяем переменные
                            $user_name = '';
                            $user_phone = '';
                            $connection_id = '';
                            $fs_name = '';
                            $packet_price = '';
                            $date = '';
                            $time = '';
                            $status_photos = '';
                            $location = '';

                            // Получаем данные записи
                            if ($zapis_id) {
                                $sql = "SELECT z.*, 
                                u.name as user_name, 
                                u.phone as user_phone,
                                f.name as fs_name,
                                p.price as packet_price,
                                z.connection as connection_id
                                FROM zapis z
                                JOIN user u ON z.user_id = u.id
                                JOIN fs f ON z.fs_id = f.id
                                JOIN packets p ON z.packet_id = p.id
                                WHERE z.id = ? AND f.photogs_id = ?";
                                $stmt = $connect->prepare($sql);
                                $stmt->execute([$zapis_id, $USER['id']]);
                                $zapis_data = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($zapis_data) {
                                    $user_name = $zapis_data['user_name'];
                                    $user_phone = $zapis_data['user_phone'];
                                    $connection_id = $zapis_data['connection_id'];
                                    $fs_name = $zapis_data['fs_name'];
                                    $packet_price = $zapis_data['packet_price'];
                                    $date = $zapis_data['date'];
                                    $time = $zapis_data['time'];
                                    $status_photos = $zapis_data['status_photos'];
                                    $location = $zapis_data['location'] ?? '';
                                } else {
                                    echo '<script>document.location.href="?page=lkZapisPh"</script>';
                                    exit;
                                }
                            }

                            // Получаем способы связи для select
                            $sql_connections = "SELECT * FROM connectionUser";
                            $stmt_connections = $connect->prepare($sql_connections);
                            $stmt_connections->execute();
                            $connections = $stmt_connections->fetchAll();

                            // Допустимые статусы фотографий
                            $allowed_statuses = ['появится позже', 'в обработке', 'обрабатываются', 'готовы', 'отправлены', 'завершены'];
                            $max_status_length = 0;
                            foreach ($allowed_statuses as $status) {
                                if (mb_strlen($status) > $max_status_length) {
                                    $max_status_length = mb_strlen($status);
                                }
                            }

                            // Допустимые локации
                            $allowed_locations = ['появится позже', 'студия', 'выездная'];
                            $max_location_length = 0;
                            foreach ($allowed_locations as $location_item) {
                                if (mb_strlen($location_item) > $max_location_length) {
                                    $max_location_length = mb_strlen($location_item);
                                }
                            }

                            // Обработка сохранения
                            if (isset($_POST['save_zapis'])) {
                                $date = $_POST['date'] ?? '';
                                $time = $_POST['time'] ?? '';
                                $status_photos = $_POST['status_photos'] ?? '';
                                $location = $_POST['location'] ?? '';

                                $flag = true;

                                $errors = [
                                    '<p class="error">Введите дату</p>',
                                    '<p class="error">Неверный формат даты. Используйте ДД.ММ.ГГГГ</p>',
                                    '<p class="error">Неверная дата</p>',
                                    '<p class="error">Нельзя выбрать прошедшую дату</p>',
                                    '<p class="error">Введите время</p>',
                                    '<p class="error">Неверный формат времени. Используйте ЧЧ:ММ</p>',
                                    '<p class="error">Неверное время. Часы: 00-23, Минуты: 00-59</p>',
                                    '<p class="error">Введите статус фотографий</p>',
                                    '<p class="error">Неверный статус фотографий. Допустимые значения: появится позже, в обработке, обрабатываются, готовы, отправлены, завершены</p>',
                                    '<p class="error">Превышено максимальное количество символов для статуса</p>',
                                    '<p class="error">Введите локацию</p>',
                                    '<p class="error">Неверная локация. Допустимые значения: появится позже, студия, выездная</p>',
                                    '<p class="error">Превышено максимальное количество символов для локации</p>'
                                ];
                            }
                            ?>

                            <form class="form" method="POST">
                                <input type="hidden" name="zapis_id" value="<?= $zapis_id ?>">

                                <!-- Информация о клиенте (только для чтения) -->
                                <div class="form-group">
                                    <label class="form-label">имя клиента</label>
                                    <input type="text" class="form-input" value="<?= htmlspecialchars($user_name) ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">телефон клиента</label>
                                    <input type="text" class="form-input" value="<?= htmlspecialchars($user_phone) ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">способ связи</label>
                                    <div class="select-form">
                                        <select class="custom-select" disabled>
                                            <option value="">выберите способ связи</option>
                                            <?php foreach ($connections as $connection): ?>
                                                <option value="<?= $connection['id'] ?>" <?= $connection_id == $connection['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($connection['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="select-arrow">
                                            <img src="assets/img/catalog/arrow_down.svg" alt="▼">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">название фотосессии</label>
                                    <input type="text" class="form-input" value="<?= htmlspecialchars($fs_name) ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">цена</label>
                                    <input type="text" class="form-input" value="<?= number_format($packet_price, 0, '', ' ') ?> ₽" readonly>
                                </div>

                                <!-- Поля для редактирования -->
                                <div class="form-group">
                                    <label for="date" class="form-label">введите дату</label>
                                    <input type="text" id="date" name="date" class="form-input date-input"
                                        placeholder="ДД.ММ.ГГГГ или 'появится позже'" value="<?= htmlspecialchars($date) ?>" required>
                                    <div class="date-hint">Формат: ДД.ММ.ГГГГ или введите "появится позже"</div>
                                    <?
                                    if (isset($_POST['save_zapis'])) {
                                        if (empty($date)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif ($date == 'появится позже') {
                                            // Если выбрано "появится позже", пропускаем проверки формата
                                        } elseif (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date)) {
                                            $flag = false;
                                            echo $errors[1];
                                        } else {
                                            $date_parts = explode('.', $date);
                                            if (count($date_parts) === 3) {
                                                $day = (int)$date_parts[0];
                                                $month = (int)$date_parts[1];
                                                $year = (int)$date_parts[2];

                                                if (!checkdate($month, $day, $year)) {
                                                    $flag = false;
                                                    echo $errors[2];
                                                } else {
                                                    $input_date = DateTime::createFromFormat('d.m.Y', $date);
                                                    $today = new DateTime();
                                                    $today->setTime(0, 0, 0);

                                                    if ($input_date < $today) {
                                                        $flag = false;
                                                        echo $errors[3];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="time" class="form-label">введите время</label>
                                    <input type="text" id="time" name="time" class="form-input time-input"
                                        placeholder="ЧЧ:ММ или 'появится позже'" value="<?= htmlspecialchars($time) ?>" required>
                                    <div class="time-hint">Формат: ЧЧ:ММ (24-часовой) или введите "появится позже"</div>
                                    <?
                                    if (isset($_POST['save_zapis'])) {
                                        if (empty($time)) {
                                            $flag = false;
                                            echo $errors[4];
                                        } elseif ($time == 'появится позже') {
                                            // Если выбрано "появится позже", пропускаем проверки формата
                                        } elseif (!preg_match('/^\d{2}:\d{2}$/', $time)) {
                                            $flag = false;
                                            echo $errors[5];
                                        } else {
                                            $time_parts = explode(':', $time);
                                            if (count($time_parts) === 2) {
                                                $hours = (int)$time_parts[0];
                                                $minutes = (int)$time_parts[1];

                                                if ($hours < 0 || $hours > 23 || $minutes < 0 || $minutes > 59) {
                                                    $flag = false;
                                                    echo $errors[6];
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="location" class="form-label">локация</label>
                                    <input type="text" id="location" name="location" class="form-input"
                                        placeholder="студия, выездная или появится позже"
                                        value="<?= htmlspecialchars($location) ?>"
                                        maxlength="<?= $max_location_length ?>" required>
                                    <div class="location-hint">Допустимые значения: появится позже, студия, выездная</div>
                                    <?
                                    if (isset($_POST['save_zapis'])) {
                                        if (empty($location)) {
                                            $flag = false;
                                            echo $errors[10];
                                        } elseif (mb_strlen($location) > $max_location_length) {
                                            $flag = false;
                                            echo $errors[12];
                                        } elseif (!in_array($location, $allowed_locations)) {
                                            $flag = false;
                                            echo $errors[11];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="status_photos" class="form-label">статус фотографий</label>
                                    <input type="text" id="status_photos" name="status_photos" class="form-input"
                                        placeholder="в обработке, обрабатываются, готовы, отправлены, завершены или появится позже"
                                        value="<?= htmlspecialchars($status_photos) ?>"
                                        maxlength="<?= $max_status_length ?>" required>
                                    <div class="status-hint">Допустимые значения: появится позже, в обработке, обрабатываются, готовы, отправлены, завершены</div>
                                    <?
                                    if (isset($_POST['save_zapis'])) {
                                        if (empty($status_photos)) {
                                            $flag = false;
                                            echo $errors[7];
                                        } elseif (mb_strlen($status_photos) > $max_status_length) {
                                            $flag = false;
                                            echo $errors[9];
                                        } elseif (!in_array($status_photos, $allowed_statuses)) {
                                            $flag = false;
                                            echo $errors[8];
                                        }
                                    }
                                    ?>
                                </div>

                                <!-- Кнопка сохранения -->
                                <input type="submit" name="save_zapis" class="btn btn-hover-red-wh-fon" value="сохранить">

                            </form>

                            <?php
                            // Сохранение в БД после всех проверок
                            if (isset($_POST['save_zapis'])) {
                                if ($flag && $zapis_id) {
                                    try {
                                        // Обновляем запись
                                        $update_stmt = $connect->prepare("UPDATE zapis SET date = ?, time = ?, location = ?, status_photos = ? WHERE id = ?");
                                        $update_stmt->execute([
                                            $date,
                                            $time,
                                            $location,
                                            $status_photos,
                                            $zapis_id
                                        ]);

                                        echo '<script>document.location.href="?page=lkZapisPh"</script>';
                                        exit;
                                    } catch (PDOException $e) {
                                        echo '<p class="error">Ошибка базы данных: ' . $e->getMessage() . '</p>';
                                    }
                                }
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


<style>
    .date-hint,
    .time-hint,
    .status-hint,
    .location-hint {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
        font-family: 'ProstoOne', sans-serif;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Маска для даты (ДД.ММ.ГГГГ) - только если не "появится позже"
        const dateInput = document.getElementById('date');
        if (dateInput) {
            dateInput.addEventListener('input', function(e) {
                let value = e.target.value;

                // Если пользователь начинает вводить "появится позже", не применяем маску
                if (value.includes('появится позже')) {
                    return;
                }

                value = value.replace(/\D/g, '');

                // Ограничиваем длину до 8 цифр (ДДММГГГГ)
                if (value.length > 8) {
                    value = value.substring(0, 8);
                }

                // Форматируем в ДД.ММ.ГГГГ
                if (value.length > 0) {
                    let formatted = '';

                    // День (первые 2 цифры)
                    if (value.length >= 2) {
                        formatted += value.substring(0, 2) + '.';
                    } else {
                        formatted = value;
                    }

                    // Месяц (следующие 2 цифры)
                    if (value.length >= 4) {
                        formatted += value.substring(2, 4) + '.';
                    } else if (value.length > 2) {
                        formatted += value.substring(2);
                    }

                    // Год (последние 4 цифры)
                    if (value.length >= 8) {
                        formatted += value.substring(4, 8);
                    } else if (value.length > 4) {
                        formatted += value.substring(4);
                    }

                    value = formatted;
                }

                e.target.value = value;
            });
        }

        // Маска для времени (ЧЧ:ММ) - только если не "появится позже"
        const timeInput = document.getElementById('time');
        if (timeInput) {
            timeInput.addEventListener('input', function(e) {
                let value = e.target.value;

                // Если пользователь начинает вводить "появится позже", не применяем маску
                if (value.includes('появится позже')) {
                    return;
                }

                value = value.replace(/\D/g, '');

                if (value.length > 0) {
                    // Ограничиваем длину
                    if (value.length > 4) {
                        value = value.substring(0, 4);
                    }

                    // Автоматически добавляем двоеточие
                    if (value.length >= 2) {
                        value = value.substring(0, 2) + ':' + value.substring(2);
                    }
                }

                e.target.value = value;
            });
        }
    });
</script>