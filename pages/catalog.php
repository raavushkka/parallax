<div class="catalog-banner">
    <h2 class="title">
        погружение в съемочный процесс
    </h2>

    <div class="cards-catalog-banner container">
        <div class="card-catalog-banner">
            <div class="star-num-name">
                <div class="star-num">
                    <p class="num">1</p>
                    <div class="star-banner-catalog">
                        <img src="assets/img/home/red_star.svg" alt="">
                    </div>
                </div>
                <p class="name-banner-catalog">
                    заявка
                </p>
            </div>
            <p class="desc-card-banner-catalog">
                оставляете заявку на сайте
            </p>
        </div>
        <div class="card-catalog-banner">
            <div class="star-num-name">
                <div class="star-num">
                    <p class="num">2</p>
                    <div class="star-banner-catalog">
                        <img src="assets/img/home/red_star.svg" alt="#">
                    </div>
                </div>
                <p class="name-banner-catalog">
                    знакомство
                </p>
            </div>
            <p class="desc-card-banner-catalog">
                обсуждаем тематику съемки и общую концепцию
            </p>
        </div>
        <div class="card-catalog-banner">
            <div class="star-num-name">
                <div class="star-num">
                    <p class="num">3</p>
                    <div class="star-banner-catalog">
                        <img src="assets/img/home/red_star.svg" alt="#">
                    </div>
                </div>
                <p class="name-banner-catalog">
                    бронирование
                </p>
            </div>
            <p class="desc-card-banner-catalog">
                выбираем дату и время съемки
            </p>
        </div>
        <div class="card-catalog-banner">
            <div class="star-num-name">
                <div class="star-num">
                    <p class="num">4</p>
                    <div class="star-banner-catalog">
                        <img src="assets/img/home/red_star.svg" alt="#">
                    </div>
                </div>
                <p class="name-banner-catalog">
                    тз съемки
                </p>
            </div>
            <p class="desc-card-banner-catalog">
                уточняем детали, обсуждаем стилистику
            </p>
        </div>
        <div class="card-catalog-banner">
            <div class="star-num-name">
                <div class="star-num">
                    <p class="num">5</p>
                    <div class="star-banner-catalog">
                        <img src="assets/img/home/red_star.svg" alt="#">
                    </div>
                </div>
                <p class="name-banner-catalog">
                    съемка
                </p>
            </div>
            <p class="desc-card-banner-catalog">
                готовим образ, снимаем фото
            </p>
        </div>
        <div class="card-catalog-banner">
            <div class="star-num-name">
                <div class="star-num">
                    <p class="num">6</p>
                    <div class="star-banner-catalog">
                        <img src="assets/img/home/red_star.svg" alt="#">
                    </div>
                </div>
                <p class="name-banner-catalog">
                    готовые фото
                </p>
            </div>
            <p class="desc-card-banner-catalog">
                спустя 7 дней вы получите готовые фотографии
            </p>
        </div>

    </div>
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
</div>

<div class="slider-container container mt180" data-slider="studio">
    <h2 class="title">
        НАША ФОТОСТУДИЯ
    </h2>
    <div class="slider-wrapper">
        <div class="slider">
            <div class="slide"><img src="assets/img/team/studio1.jpg" alt="Интерьер 1"></div>
            <div class="slide"><img src="assets/img/team/studio2.jpg" alt="Интерьер 2"></div>
            <div class="slide"><img src="assets/img/team/studio3.jpg" alt="Интерьер 3"></div>
            <div class="slide"><img src="assets/img/team/studio4.jpg" alt="Интерьер 4"></div>
            <div class="slide"><img src="assets/img/team/studio5.jpg" alt="Интерьер 5"></div>
            <div class="slide"><img src="assets/img/team/studio6.jpg" alt="Интерьер 6"></div>
        </div>
    </div>
    <div class="slider-nav">
        <button class="nav-btn prev-btn"><img src="assets/img/home/icon_arrow.svg" alt="Предыдущий"></button>
        <button class="nav-btn next-btn"><img src="assets/img/home/icon_arrow.svg" alt="Следующий"></button>
    </div>
</div>

<div class="container">
    <h2 class="title mt180">
        фотосессии
    </h2>

    <?php
    // Получаем все категории для фильтра
    $sql_categories = "SELECT * FROM category";
    $categories = $connect->query($sql_categories)->fetchAll();

    // Получаем параметры фильтрации
    $search_query = $_GET['search'] ?? '';
    $category_filter = $_GET['category'] ?? '';
    ?>

    <div class="filters-container ">
        <form method="GET" class="filters-wrapper" id="filtersForm">
            <input type="hidden" name="page" value="catalog">

            <!-- Форма поиска -->
            <div class="search-form">
                <input type="text" name="search" placeholder="Поиск по названию..." class="search-input" value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit" class="search-btn">
                    <img src="assets/img/catalog/search.svg" alt="Поиск">
                </button>
            </div>

            <!-- Форма селекта -->
            <div class="select-form">
                <select class="custom-select" name="category" onchange="submitFormWithScroll()">
                    <option value="" <?= empty($category_filter) ? 'selected' : '' ?>>все категории</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $category_filter == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="select-arrow">
                    <img src="assets/img/catalog/arrow_down.svg" alt="▼">
                </div>
            </div>

            <!-- Кнопка сброса фильтров -->
            <?php if (!empty($search_query) || !empty($category_filter)): ?>
                <a href="?page=catalog" class="btn btn-no-back" onclick="resetFiltersWithScroll(event)" style="margin-left: 20px;">cбросить</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="mini-catalog mt40" id="sessionsBlock">
        <div class="container">
            <div class="mini_catalog_cards">
                <?php
                // Формируем SQL запрос с фильтрами
                $sql = "SELECT fs.*, c.name as category_name 
                        FROM `fs` fs 
                        LEFT JOIN `category` c ON fs.category = c.id 
                        WHERE 1=1";

                $params = [];

                // Добавляем фильтр по поиску (только по названию)
                if (!empty($search_query)) {
                    $sql .= " AND fs.name LIKE ?";
                    $search_term = "%$search_query%";
                    $params[] = $search_term;
                }

                // Добавляем фильтр по категории
                if (!empty($category_filter)) {
                    $sql .= " AND fs.category = ?";
                    $params[] = $category_filter;
                }

                $sql .= " ORDER BY fs.id DESC";

                // Выполняем запрос
                $stmt = $connect->prepare($sql);
                $stmt->execute($params);
                $sessions = $stmt->fetchAll();

                if (count($sessions) > 0) {
                    foreach ($sessions as $session) {
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
                                    от <?= $formatted_price ?> ₽
                                </p>
                                <a href="?page=productPage&id=<?= $session['id'] ?>">
                                    <img src="assets/img/home/icon_arrow.svg" alt="" class="arrow">
                                </a>
                            </div>
                            <!-- Бейдж категории -->
                            <div class="category-badge">
                                <?= htmlspecialchars($session['category_name']) ?>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<div class="empty-cart-message">Фотосессии не найдены. Попробуйте изменить параметры поиска.</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Слайдер фотостудии -->



<style>
    .category-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(19, 19, 19, 0.8);
        color: #F2EFE4;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-family: 'ProstoOne', sans-serif;
    }

    .card_catalog {
        position: relative;
    }

    .filters-wrapper {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .btn-no-back {
        background: transparent;
        border: 1px solid #131313;
        color: #131313;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        font-family: 'ProstoOne', sans-serif;
        transition: all 0.3s ease;
    }

    .btn-no-back:hover {
        background: #131313;
        color: #F2EFE4;
    }

    /* Плавный скролл */
    html {
        scroll-behavior: smooth;
    }
</style>

<script>
    class Slider {
        constructor(container) {
            this.container = container;
            this.slider = container.querySelector('.slider');
            this.slides = container.querySelectorAll('.slide');
            this.prevBtn = container.querySelector('.prev-btn');
            this.nextBtn = container.querySelector('.next-btn');

            this.currentIndex = 0;
            this.slidesToShow = 4;
            this.slideWidth = 283;
            this.gap = 20;

            this.init();
            this.handleResize();
            window.addEventListener('resize', () => this.handleResize());
        }

        handleResize() {
            if (window.innerWidth <= 768) {
                this.slideWidth = 220;
                this.slidesToShow = 3;
            } else if (window.innerWidth <= 567) {
                this.slideWidth = 180;
                this.slidesToShow = 2;
            } else if (window.innerWidth <= 380) {
                this.slideWidth = 150;
                this.slidesToShow = 2;
            } else {
                this.slideWidth = 283;
                this.slidesToShow = 4;
            }
            this.updateSlider();
        }

        init() {
            this.updateButtons();
            this.addEventListeners();
        }

        addEventListeners() {
            this.prevBtn.addEventListener('click', () => this.prev());
            this.nextBtn.addEventListener('click', () => this.next());
        }

        prev() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.updateSlider();
            }
        }

        next() {
            if (this.currentIndex < this.slides.length - this.slidesToShow) {
                this.currentIndex++;
                this.updateSlider();
            }
        }

        updateSlider() {
            const translateX = -this.currentIndex * (this.slideWidth + this.gap);
            this.slider.style.transform = `translateX(${translateX}px)`;
            this.updateButtons();
        }

        updateButtons() {
            this.prevBtn.disabled = this.currentIndex === 0;
            this.nextBtn.disabled = this.currentIndex >= this.slides.length - this.slidesToShow;
        }
    }

    // Глобальные функции (должны быть доступны из HTML)
    function resetFiltersWithScroll(event) {
        event.preventDefault();
        // Сохраняем флаг для скролла
        sessionStorage.setItem('shouldScrollToSessions', 'true');
        // Переходим на чистую страницу каталога
        window.location.href = '?page=catalog';
    }

    function submitFormWithScroll() {
        // Сохраняем позицию для скролла после обновления
        sessionStorage.setItem('shouldScrollToSessions', 'true');
        document.getElementById('filtersForm').submit();
    }

    function scrollToSessionsBlock() {
        const sessionsBlock = document.getElementById('sessionsBlock');
        if (sessionsBlock) {
            const offset = 320; // Отступ сверху
            const elementPosition = sessionsBlock.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        let searchTimeout;

        // Авто-поиск только по названию с задержкой
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                submitFormWithScroll();
            }, 900);
        });

        // Инициализация слайдера
        const sliderContainer = document.querySelector('.slider-container[data-slider="studio"]');
        if (sliderContainer) {
            new Slider(sliderContainer);
        }

        // Плавный скролл к блоку с фотосессиями при загрузке страницы с фильтрами
        if (window.location.search.includes('search=') || window.location.search.includes('category=')) {
            setTimeout(() => {
                scrollToSessionsBlock();
            }, 100);
        }

        // Скролл после загрузки страницы (если нужно)
        if (sessionStorage.getItem('shouldScrollToSessions') === 'true') {
            sessionStorage.removeItem('shouldScrollToSessions');

            setTimeout(() => {
                scrollToSessionsBlock();
            }, 300);
        }
    });
</script>