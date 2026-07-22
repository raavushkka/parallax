<!-- banner -->
<div class="banner">
    <div class="img-banner">
        <img src="assets/img/home/banner.png" alt="">
    </div>
    <div class="img-letter">
        <img src="assets/img/home/f.svg" alt="#" class="f">
        <div class="o-width">
            <img src="assets/img/home/o.svg" alt="#" class="o">
        </div>
        <div class="t-width">
            <img src="assets/img/home/t.svg" alt="#" class="t">
        </div>
        <div class="o-width">
            <img src="assets/img/home/o.svg" alt="#" class="o1">
        </div>
        <div class="s-width">
            <img src="assets/img/home/s.svg" alt="#" class="s">
        </div>
        <div class="t-width">
            <img src="assets/img/home/t.svg" alt="#" class="t1">
        </div>
        <img src="assets/img/home/u.svg" alt="#" class="u">
        <img src="assets/img/home/d.svg" alt="#" class="d">
        <img src="assets/img/home/i.svg" alt="#" class="i">
        <img src="assets/img/home/ia.svg" alt="#" class="ia">
    </div>
    <div class="star-banner-width">
        <img src="assets/img/home/red_star.svg" alt="#" class="star-banner">
    </div>
</div>
<!-- banner -->

<!-- about -->
<div class="about">
    <div class="marquee">
        <div class="marquee-content">
            <div class="star-text">
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
            </div>
        </div>
    </div>
    <h2 class="title">
        мы про...
    </h2>
    <div class="container">

        <div class="text-about ">
            <div class="titles-about">
                <h2 class="title-about">
                    АУТЕНТИчНОСТЬ.
                </h2>
                <h2 class="title-about">
                    профессионализм.
                </h2>
                <h2 class="title-about">
                    уникальность.
                </h2>
            </div>
            <div class="mini-text-about">
                <p>МЫ СОЗДАЕМ АТМОСФЕРУ, ГДЕ МОЖНО РАССЛАБИТЬСЯ И ПОЗВОЛИТЬ СЕБЕ ЛЮБУЮ СМЕЛУЮ ИДЕЮ. ЗДЕСЬ НЕТ ШАБЛОНОВ — ТОЛЬКО ВАШИ ЭМОЦИИ И НАШЕ МАСТЕРСТВО</p>
            </div>
        </div>

        <img src="assets/img/home/about1.png" alt="" class="img-about1">
        <img src="assets/img/home/about2.png" alt="" class="img-about2">
    </div>

</div>

<!-- catalog -->
<div class="mini-catalog mt120">
    <div class="stars-catalog">
        <img src="assets/img/home/red_star.svg" alt="" class="star_catalog_left1">
        <img src="assets/img/home/black_star.svg" alt="" class="star_catalog_left2">
        <img src="assets/img/home/red_star.svg" alt="" class="star_catalog_left3">
        <img src="assets/img/home/black_star.svg" alt="" class="star_catalog_left4">
        <img src="assets/img/home/red_star.svg" alt="" class="star_catalog_left5">
    </div>
    <div class="container">

        <h2 class="title">
            популярные фотосессии
        </h2>
        <p class="subtitle">
            Это процесс совместного творчества, где ваша аутентичность встречается с нашей творческой философией. Вместе мы создаём не просто изображения, а визуальные высказывания, наполненные смыслом и характером.
        </p>
        <div class="mini_catalog_cards">
            <?
            // Получаем последние 3 фотосессии
            $sql = "SELECT fs.*, c.name as category_name 
                    FROM `fs` fs 
                    LEFT JOIN `category` c ON fs.category = c.id 
                    ORDER BY fs.id DESC 
                    LIMIT 3";
            $latest_sessions = $connect->query($sql);

            if ($latest_sessions->rowCount() > 0) {
                foreach ($latest_sessions as $session) {
                    // Получаем первое изображение для превью
                    $image_sql = "SELECT filename FROM imagesPhotogs WHERE fs_id = ? LIMIT 1";
                    $image_stmt = $connect->prepare($image_sql);
                    $image_stmt->execute([$session['id']]);
                    $image = $image_stmt->fetch();
                    $image_path = $image ? $image['filename'] : 'assets/img/home/card2.png';

                    // Форматируем цену
                    $formatted_price = number_format($session['price'], 0, '', ' ');
            ?>
                    <div class="card_catalog">
                        <div class="card-img">
                            <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($session['name']) ?>">
                        </div>
                        <p class="name_card">
                            <?= htmlspecialchars($session['name']) ?>
                        </p>
                        <p class="desc_card" title="<?= htmlspecialchars($session['desc']) ?>">
                            <?= htmlspecialchars($session['desc']) ?>
                        </p>
                        <div class="price_btn">
                            <p class="price">
                                <?= $formatted_price ?> ₽
                            </p>
                            <a href="?page=productPage&id=<?= $session['id'] ?>">
                                <img src="assets/img/home/icon_arrow.svg" alt="" class="arrow">
                            </a>
                        </div>
                    </div>
            <?
                }
            }
            ?>
        </div>
        <div class="mini-catalog_btn">
            <a href="?page=catalog#sessionsBlock" class="btn btn-hover-red-wh-fon">показать еще</a>
        </div>
    </div>
    <div class="stars-catalog">
        <img src="assets/img/home/red_star.svg" alt="" class="star_catalog_left6">
        <img src="assets/img/home/black_star.svg" alt="" class="star_catalog_left7">
        <img src="assets/img/home/red_star.svg" alt="" class="star_catalog_left8">
        <img src="assets/img/home/black_star.svg" alt="" class="star_catalog_left9">
        <img src="assets/img/home/red_star.svg" alt="" class="star_catalog_left10">
    </div>
</div>

<style>

</style>

<!-- primeri rabot -->
<div class="raboti mt120">
    <h2 class="title">
        Примеры работ
    </h2>
    <div class="photo-gallery">
        <div class="left-column">
            <div class="img-container">
                <img src="assets/img/catalog/fs_port_br4.jpg" alt="" class="small">
            </div>
            <div class="img-container">
                <img src="assets/img/home/rab2.png" alt="" class="small">
            </div>
        </div>

        <div class="middle-column">
            <div class="img-container">
                <img src="assets/img/home/rab3.png" alt="" class="medium">
            </div>
            <div class="img-container">
                <img src="assets/img/home/rab4.png" alt="" class="medium">
            </div>
        </div>

        <div class="large-column">
            <div class="img-container">
                <img src="assets/img/home/rab5.png" alt="" class="large">
            </div>
        </div>

        <div class="right-column">
            <div class="img-container">
                <img src="assets/img/home/rab6.png" alt="" class="small">
            </div>
            <div class="img-container">
                <img src="assets/img/catalog/fs_port_br1.jpg" alt="" class="small">
            </div>
        </div>
    </div>
</div>


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

<!-- certificate -->
<div class="certificate mt120">
    <div class="stars-catalog">
        <img src="assets/img/home/red_star.svg" alt="#" class="star_catalog_left1">
        <img src="assets/img/home/white_star.svg" alt="#" class="star_catalog_left2">
        <img src="assets/img/home/red_star.svg" alt="#" class="star_catalog_left3">
        <img src="assets/img/home/white_star.svg" alt="#" class="star_catalog_left4">
        <img src="assets/img/home/red_star.svg" alt="#" class="star_catalog_left5">
    </div>
    <div class="text-certificate">
        <h2 class="title">
            подарочный сертификат <br>на фотосессию!
        </h2>
        <p class="subtitle-certificate">
            подари не просто съёмку, а право быть разным. злым, нежным, смешным, серьёзным.
            таким, каким хочешь быть сегодня
        </p>
        <a href="?page=certificate#certificate" class="btn btn-hover-red-bl-fon" style="text-align:center;">подробнее</a>
    </div>
    <div class="stars-catalog">
        <img src="assets/img/home/red_star.svg" alt="#" class="star_catalog_left6">
        <img src="assets/img/home/white_star.svg" alt="#" class="star_catalog_left7">
        <img src="assets/img/home/red_star.svg" alt="#" class="star_catalog_left8">
        <img src="assets/img/home/white_star.svg" alt="#" class="star_catalog_left9">
        <img src="assets/img/home/red_star.svg" alt="#" class="star_catalog_left10">
    </div>
</div>

<!-- contact -->
<div class="contact container mt120">
    <h2 class="title">
        контакты
    </h2>
    <div class="contact_block">
        <div class="contact_text">
            <div class="name_title_text_contacts">
                <div class="name_title_text_contact">
                    <div class="name_contact">
                        Адрес
                    </div>
                    <div class="name_contact_text">
                        г. Казань, ул. Баумана, 51
                    </div>
                </div>
                <div class="name_title_text_contact">
                    <div class="name_contact">
                        режим работы
                    </div>
                    <div class="name_contact_text">
                        ежедневно 10:00 - 20:00
                    </div>
                </div>
                <div class="name_title_text_contact">
                    <div class="name_contact">
                        телефон
                    </div>
                    <div class="name_contact_text">
                        <a href="tel:+7 (495) 021-07-10" style="color: #131313">+7 (495) 021-07-10</a>
                    </div>
                </div>
            </div>
            <div class="social">
                <img src="assets/img/home/whatsapp.svg" alt="">
                <img src="assets/img/home/vk.svg" alt="">
                <img src="assets/img/home/tg.svg" alt="">
            </div>
        </div>
        <div class="map">
            <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A0a3bfa14b77feedc7e27f5dcdd4d956d99dd8f3e6e2817d017ccde86b6a8df08&amp;width=590&amp;height=580&amp;lang=ru_RU&amp;scroll=true"></script>
        </div>
    </div>
</div>
<!-- contact -->