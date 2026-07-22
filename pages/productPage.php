<?php
// Обработчик записи на фотосессии
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_zapis'])) {

    // Проверка авторизации
    if (!isset($_SESSION['USER'])) {
        $_SESSION['ERROR'] = 'Для записи необходимо авторизоваться';
        header('Location: ?page=auto');
        exit();
    }

    // Проверка роли пользователя
    $user_id = $_SESSION['USER'];
    $sql = "SELECT role FROM user WHERE id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || $user['role'] != 1) {
        $_SESSION['ERROR'] = 'Запись доступна только для пользователей';
        header('Location: ?page=productPage&id=' . $_POST['session_id']);
        exit();
    }

    // Получаем данные из формы
    $session_id = $_POST['session_id'];
    $packet_id = $_POST['packet_id'];
    $connection = $_POST['connection'];

    // Валидация
    if (empty($connection)) {
        $_SESSION['ERROR'] = 'Выберите способ связи';
        header('Location: ?page=productPage&id=' . $session_id);
        exit();
    }

    // Добавляем запись
    try {
        $sql = "INSERT INTO zapis (user_id, fs_id, packet_id, connection, date, time,location, status_photos) 
                VALUES (?, ?, ?, ?, 'появится позже', 'появится позже','появится позже', 'в обработке')";

        $stmt = $connect->prepare($sql);
        $result = $stmt->execute([$user_id, $session_id, $packet_id, $connection]);

        if ($result) {
            $_SESSION['SUCCESS'] = 'Запись успешно оформлена! С вами свяжутся для подтверждения.';
            header('Location: ?page=lkZapis');
            exit();
        } else {
            $_SESSION['ERROR'] = 'Ошибка при записи в базу данных';
            header('Location: ?page=productPage&id=' . $session_id);
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['ERROR'] = 'Ошибка при записи: ' . $e->getMessage();
        header('Location: ?page=productPage&id=' . $session_id);
        exit();
    }
}

// Показываем сообщения
if (isset($_SESSION['ERROR'])) {
    echo '<div class="error-message">❌ ' . $_SESSION['ERROR'] . '</div>';
    unset($_SESSION['ERROR']);
}

if (isset($_SESSION['SUCCESS'])) {
    echo '<div class="success-message">✅ ' . $_SESSION['SUCCESS'] . '</div>';
    unset($_SESSION['SUCCESS']);
}

// Получаем ID фотосессии
$session_id = $_GET['id'] ?? '';
if (!$session_id) {
    echo '<p class="error">ID фотосессии не указан</p>';
    exit();
}

// Получаем данные фотосессии
$sql = "SELECT fs.*, u.name as photographer_name, p.desc as photographer_desc 
        FROM fs fs 
        LEFT JOIN user u ON fs.photogs_id = u.id 
        LEFT JOIN photogs p ON fs.photogs_id = p.id 
        WHERE fs.id = ?";
$stmt = $connect->prepare($sql);
$stmt->execute([$session_id]);
$session = $stmt->fetch();

if (!$session) {
    echo '<p class="error">Фотосессия не найдена</p>';
    exit();
}

// Получаем изображения фотосессии
$images_sql = "SELECT filename FROM imagesPhotogs WHERE fs_id = ?";
$images_stmt = $connect->prepare($images_sql);
$images_stmt->execute([$session_id]);
$images = $images_stmt->fetchAll();

// Получаем пакеты
$packets_sql = "SELECT * FROM packets";
$packets_stmt = $connect->prepare($packets_sql);
$packets_stmt->execute();
$packets = $packets_stmt->fetchAll();

// Получаем способы связи из таблицы connectionUser
$connection_sql = "SELECT * FROM connectionUser";
$connection_stmt = $connect->prepare($connection_sql);
$connection_stmt->execute();
$connections = $connection_stmt->fetchAll();
?>

<div class="session-page container mt120">
    <div class="session-content">
        <!-- Левая часть - слайдер -->
        <div class="session-gallery">
            <div class="main-slider">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $index => $image): ?>
                        <div class="main-slide <?= $index === 0 ? 'active' : '' ?>">
                            <img src="<?= $image['filename'] ?>" alt="Фото <?= $index + 1 ?>">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="main-slide active">
                        <img src="assets/img/phPage/w2.png" alt="Нет изображений">
                    </div>
                <?php endif; ?>

                <button class="slider-nav1 prev-btn1">
                    <img src="assets/img/home/icon_arrow.svg" alt="Предыдущий">
                </button>
                <button class="slider-nav1 next-btn">
                    <img src="assets/img/home/icon_arrow.svg" alt="Следующий">
                </button>
            </div>

            <!-- Миниатюры -->
            <div class="thumbnails">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $index => $image): ?>
                        <div class="thumb <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
                            <img src="<?= $image['filename'] ?>" alt="Миниатюра <?= $index + 1 ?>">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Правая часть - информация -->
        <div class="session-info">
            <div class="session-info-content">
                <h1 class="session-title"><?= htmlspecialchars($session['name']) ?></h1>

                <div class="session-meta">
                    <div class="meta-item">
                        <span class="meta-label">ФОТОГРАФ:</span>
                        <a href="?page=phPage&id=<?= $session['photogs_id'] ?>" class="meta-value">
                            <?= htmlspecialchars($session['photographer_name']) ?>
                        </a>
                    </div>

                    <div class="meta-item">
                        <span class="meta-label">ЛОКАЦИЯ:</span>
                        <span class="meta-value1"><?= htmlspecialchars($session['location']) ?></span>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="session-description">
                    <p><?= nl2br(htmlspecialchars($session['desc'])) ?></p>
                </div>
            </div>

            <div class="product-page-price-btn">
                <p class="product-page-price">от <?= number_format($session['price'], 0, '', ' ') ?> ₽</p>
                <a href="#packets-section" class="btn btn-hover-red-wh-fon w100">узнать стоимость</a>
            </div>
        </div>


    </div>
</div>

<div class="pacets container mt120" id="packets-section">
    <h2 class="title">стоимость фотосессии</h2>
    <div class="cards-packets">
        <?php if (!empty($packets)): ?>
            <?php foreach ($packets as $packet): ?>
                <div class="card-packet">
                    <p class="name-packet"><?= htmlspecialchars($packet['name']) ?></p>
                    <div class="packet-desc">
                        <p>ДЛИТЕЛЬНОСТЬ: <span><?= htmlspecialchars($packet['duration']) ?></span></p>
                        <p>ИСХОДНИКИ: <span><?= htmlspecialchars($packet['sources']) ?></span></p>
                        <p>ОБРАБОТКА: <span><?= htmlspecialchars($packet['processing']) ?></span></p>
                        <p>АРЕНДА СТУДИИ: <span><?= htmlspecialchars($packet['rent']) ?></span></p>
                        <p>РАБОТА СТИЛИСТА: <span><?= htmlspecialchars($packet['stylist']) ?></span></p>
                        <p>РАБОТА ВИЗАЖИСТА: <span><?= htmlspecialchars($packet['visagiste']) ?></span></p>
                    </div>
                    <p class="price-packet"><?= number_format($packet['price'], 0, '', ' ') ?> ₽</p>
                    <button class="btn btn-hover-red-wh-fon packet-btn"
                        data-packet="<?= htmlspecialchars($packet['name']) ?>"
                        data-price="<?= $packet['price'] ?>"
                        data-packet-id="<?= $packet['id'] ?>">
                        записаться
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Пакеты пока не добавлены</p>
        <?php endif; ?>
    </div>
</div>

<!-- Модальное окно записи -->
<form id="product_modal" class="form1" method="post" action="?page=productPage&id=<?= $session_id ?>" style="display: none;">
    <div class="form_content">
        <div class="close_right">
            <span><img src="assets/img/modal/Path.svg" alt=""></span>
        </div>
        <h2 class="title">ЗАБРОНИРУЙТЕ МЕСТО НА ФОТОСЕССИЮ</h2>

        <input type="hidden" name="session_id" value="<?= $session_id ?>">
        <input type="hidden" name="packet_id" id="packet_id">

        <div class="label_input">
            <label>Как с вами связаться?</label>
            <div class="select-wrapper">
                <select name="connection" class="custom-select" required>
                    <option value="">Выберите способ связи</option>
                    <?php foreach ($connections as $conn): ?>
                        <option value="<?= $conn['id'] ?>"><?= htmlspecialchars($conn['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="label_input">
            <label>Пакет фотосессии</label>
            <input type="text" id="packet_name" name="packet_name" readonly>
        </div>

        <div class="label_input">
            <label>Название фотосессии</label>
            <input type="text" value="<?= htmlspecialchars($session['name']) ?>" readonly>
        </div>

        <div class="label_input">
            <label>Фотограф</label>
            <input type="text" value="<?= htmlspecialchars($session['photographer_name']) ?>" readonly>
        </div>

        <input type="submit" class="btn btn2" value="записаться" name="add_zapis">
        <p class="exit">*С вами свяжется фотограф для подтверждения записи</p>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Слайдер
        const mainSlides = document.querySelectorAll('.main-slide');
        const thumbs = document.querySelectorAll('.thumb');
        const prevBtn = document.querySelector('.prev-btn1');
        const nextBtn = document.querySelector('.next-btn');
        let currentSlide = 0;

        function showSlide(index) {
            mainSlides.forEach(slide => slide.classList.remove('active'));
            thumbs.forEach(thumb => thumb.classList.remove('active'));

            mainSlides[index].classList.add('active');
            thumbs[index].classList.add('active');
            currentSlide = index;
        }

        thumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', () => showSlide(index));
        });

        prevBtn.addEventListener('click', () => {
            let newIndex = currentSlide - 1;
            if (newIndex < 0) newIndex = mainSlides.length - 1;
            showSlide(newIndex);
        });

        nextBtn.addEventListener('click', () => {
            let newIndex = currentSlide + 1;
            if (newIndex >= mainSlides.length) newIndex = 0;
            showSlide(newIndex);
        });

        // Модальное окно
        const productModal = document.getElementById('product_modal');
        const packetBtns = document.querySelectorAll(".packet-btn");
        const closeBtn = productModal.querySelector(".close_right");
        const packetNameInput = document.getElementById("packet_name");
        const packetIdInput = document.getElementById("packet_id");

        packetBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const packetName = this.getAttribute('data-packet');
                const packetId = this.getAttribute('data-packet-id');

                packetNameInput.value = packetName;
                packetIdInput.value = packetId;

                productModal.style.display = "flex";
                document.body.classList.add('modal-open');
            });
        });

        closeBtn.addEventListener('click', function() {
            productModal.style.display = "none";
            document.body.classList.remove('modal-open');
        });

        productModal.addEventListener('click', function(e) {
            if (e.target === productModal) {
                productModal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        });

        // FAQ аккордеон
        const acc = document.getElementsByClassName("accordion");
        for (let i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
                const panel = this.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }
    });
</script>



<!-- FAQ -->
<div class="faq container mt120">
    <h2 class="title">
        вопрос-ответ
    </h2>

    <div class="accordion_cards">
        <button class="accordion">фотостудия оплачивается отдельно?</button>
        <div class="panel">
            <p>Нет, фотостудия включена в стоимость съёмки. Никаких доплат к стоимости не предусмотрено</p>
        </div>

        <button class="accordion">Фотограф подсказывает как позировать?</button>
        <div class="panel">
            <p>Не нужно быть профессиональной моделью. Фотограф полностью ведёт съёмку и помогает в позировании</p>
        </div>

        <button class="accordion">Как быстро будет готов результат?</button>
        <div class="panel">
            <p>Исходники уже на следующий день после съёмки, фото в обработке уже через 7 дней</p>
        </div>

        <button class="accordion">Есть ли ограничения по макияжу?</button>
        <div class="panel">
            <p>Макияж любой сложности вам сделает профессиональный визажист за 1 час до съёмки</p>
        </div>

        <button class="accordion">Нужно ли заранее записываться на определенную дату и время?</button>
        <div class="panel">
            <p>Да, учитывая подготовку общей концепции, нужно записываться заранее</p>
        </div>
    </div>
</div>

<script>
    // Более надежная инициализация аккордеона
    function initAccordion() {
        console.log('Инициализация аккордеона...'); // Для отладки

        const accordions = document.querySelectorAll('.accordion');
        console.log('Найдено элементов:', accordions.length); // Для отладки

        accordions.forEach(accordion => {
            // Убираем предыдущие обработчики
            accordion.replaceWith(accordion.cloneNode(true));
        });

        // Снова находим элементы после клонирования
        const newAccordions = document.querySelectorAll('.accordion');

        newAccordions.forEach(accordion => {
            accordion.addEventListener('click', function() {
                console.log('Клик по аккордеону!'); // Для отладки

                // Закрываем все другие аккордеоны
                newAccordions.forEach(otherAcc => {
                    if (otherAcc !== this) {
                        otherAcc.classList.remove('active');
                        const otherPanel = otherAcc.nextElementSibling;
                        if (otherPanel && otherPanel.classList.contains('panel')) {
                            otherPanel.style.maxHeight = null;
                        }
                    }
                });

                // Переключаем текущий аккордеон
                this.classList.toggle('active');
                const panel = this.nextElementSibling;

                if (panel && panel.classList.contains('panel')) {
                    if (panel.style.maxHeight) {
                        panel.style.maxHeight = null;
                    } else {
                        panel.style.maxHeight = panel.scrollHeight + "px";
                    }
                }
            });
        });
    }

    // Инициализируем когда DOM загружен
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM загружен!'); // Для отладки

        // Инициализируем аккордеон
        initAccordion();

        // Остальной код для слайдера и модального окна...
        const mainSlides = document.querySelectorAll('.main-slide');
        const thumbs = document.querySelectorAll('.thumb');
        const prevBtn = document.querySelector('.prev-btn1');
        const nextBtn = document.querySelector('.next-btn');
        let currentSlide = 0;

        function showSlide(index) {
            mainSlides.forEach(slide => slide.classList.remove('active'));
            thumbs.forEach(thumb => thumb.classList.remove('active'));

            mainSlides[index].classList.add('active');
            thumbs[index].classList.add('active');
            currentSlide = index;
        }

        if (thumbs.length > 0) {
            thumbs.forEach((thumb, index) => {
                thumb.addEventListener('click', () => showSlide(index));
            });

            prevBtn.addEventListener('click', () => {
                let newIndex = currentSlide - 1;
                if (newIndex < 0) newIndex = mainSlides.length - 1;
                showSlide(newIndex);
            });

            nextBtn.addEventListener('click', () => {
                let newIndex = currentSlide + 1;
                if (newIndex >= mainSlides.length) newIndex = 0;
                showSlide(newIndex);
            });
        }

        // Модальное окно
        const productModal = document.getElementById('product_modal');
        const packetBtns = document.querySelectorAll(".packet-btn");
        const closeBtn = productModal?.querySelector(".close_right");
        const packetNameInput = document.getElementById("packet_name");
        const packetIdInput = document.getElementById("packet_id");

        if (packetBtns.length > 0) {
            packetBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const packetName = this.getAttribute('data-packet');
                    const packetId = this.getAttribute('data-packet-id');

                    packetNameInput.value = packetName;
                    packetIdInput.value = packetId;

                    productModal.style.display = "flex";
                    document.body.classList.add('modal-open');
                });
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                productModal.style.display = "none";
                document.body.classList.remove('modal-open');
            });

            productModal.addEventListener('click', function(e) {
                if (e.target === productModal) {
                    productModal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                }
            });
        }
    });

    // Альтернативная инициализация на случай если DOMContentLoaded уже прошел
    setTimeout(initAccordion, 100);
</script>