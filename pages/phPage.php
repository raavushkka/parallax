<div class="photographer-page container">
    <?php
    if (isset($_GET['id'])) {
        $photographer_id = $_GET['id'];

        // Получаем данные фотографа
        $sql = "SELECT u.*, p.* 
            FROM user u 
            JOIN photogs p ON u.id = p.id 
            WHERE u.id = ? AND u.role = 3";
        $stmt = $connect->prepare($sql);
        $stmt->execute([$photographer_id]);
        $photographer = $stmt->fetch();

        if ($photographer) {
    ?>
            <div class="photographer-header">
                <!-- Левая часть - фото -->
                <div class="photographer-image">
                    <img src="<?= $photographer['img'] ?>" alt="<?= $photographer['name'] ?>">
                </div>

                <!-- Правая часть - вся информация -->
                <div class="photographer-info">
                    <h1 class="photographer-name"><?= htmlspecialchars($photographer['name']) ?></h1>
                    <p class="photographer-desc">
                        <?= htmlspecialchars($photographer['desc']) ?>
                    </p>

                    <div class="divider"></div>

                    <div class="achievements">
                        <div class="achievement-item">
                            <div class="achievement-number"><?= $photographer['experience'] ?></div>
                            <p class="achievement-text">лет опыта</p>
                        </div>
                        <div class="achievement-item">
                            <div class="achievement-number"><?= $photographer['projects'] ?>+</div>
                            <p class="achievement-text">проектов</p>
                        </div>
                        <div class="achievement-item">
                            <div class="achievement-number"><?= $photographer['exhibitions'] ?></div>
                            <p class="achievement-text">выставок</p>
                        </div>
                    </div>

                    <!-- Блок с направлениями-кнопочками -->
                    <div class="photographer-sessions">
                        <p class="sessions-title">направления съемки</p>
                        <div class="sessions-categories">
                            <?php
                            // Получаем названия фотосессий этого фотографа
                            $sql_sessions = "SELECT name FROM fs WHERE photogs_id = ?";
                            $stmt_sessions = $connect->prepare($sql_sessions);
                            $stmt_sessions->execute([$photographer_id]);
                            $sessions = $stmt_sessions->fetchAll();

                            foreach ($sessions as $index => $session) {
                                $active_class = $index === 0 ? 'active' : '';
                            ?>
                                <button class="category-btn <?= $active_class ?>"><?= htmlspecialchars($session['name']) ?></button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        } else {
            echo '<p>Фотограф не найден</p>';
        }
    } else {
        echo '<p>ID фотографа не указан</p>';
    }
    ?>


    <div class="divider"></div>
</div>

<div class="portfolio container mt80">
    <h2 class="title">
        портфолио
    </h2>

    <?php
    // Получаем ID фотографа из параметра
    $photographer_id = $_GET['id'] ?? 0;

    if ($photographer_id) {
        // Получаем работы из портфолио фотографа
        $sql = "SELECT p.*, f.name as fs_name, c.name as category_name
                FROM portfolio p 
                LEFT JOIN fs f ON p.fs = f.id 
                LEFT JOIN category c ON f.category = c.id
                WHERE p.photogs_id = ? 
                ORDER BY p.date DESC";
        $stmt = $connect->prepare($sql);
        $stmt->execute([$photographer_id]);
        $portfolio_works = $stmt->fetchAll();

        // Получаем уникальные категории для фильтров
        $sql_categories = "SELECT DISTINCT c.id, c.name 
                         FROM portfolio p 
                         JOIN fs f ON p.fs = f.id 
                         JOIN category c ON f.category = c.id 
                         WHERE p.photogs_id = ?";
        $stmt_categories = $connect->prepare($sql_categories);
        $stmt_categories->execute([$photographer_id]);
        $categories = $stmt_categories->fetchAll();
    ?>

        <!-- <div class="sessions-categories">
            <button class="category-btn active" data-category="all">все</button>
            <?php foreach ($categories as $category) { ?>
                <button class="category-btn" data-category="<?= $category['id'] ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </button>
            <?php } ?>
        </div> -->

        <div class="works-ph-portgopio" id="portfolioWorks">
            <?php
            if (empty($portfolio_works)) {
                echo '<div class="empty-cart-message">В портфолио пока нет работ.</div>';
            } else {
                foreach ($portfolio_works as $work) {
                    // Получаем изображения для работы
                    $sql_images = "SELECT filename FROM imagesPortfolio WHERE portfolio_id = ?";
                    $stmt_images = $connect->prepare($sql_images);
                    $stmt_images->execute([$work['id']]);
                    $images = $stmt_images->fetchAll();

                    if (!empty($images)) {
            ?>
                        <div class="work-ph-portgopio" data-category="<?= $work['category_id'] ?? '' ?>">
                            <p class="name-work">
                                <?= htmlspecialchars($work['name']) ?>
                            </p>
                            <p class="desc-work">
                                <?= htmlspecialchars($work['fs_name'] ?? '') ?> - <?= htmlspecialchars($work['date']) ?> - <?= htmlspecialchars($work['location']) ?>
                            </p>
                            <div class="fotos-work">
                                <?php foreach ($images as $image) { ?>
                                    <div class="image-container">
                                        <img src="<?= $image['filename'] ?>"
                                            alt="<?= htmlspecialchars($work['name']) ?>"
                                            class="portfolio-image"
                                            onclick="openModal('<?= $image['filename'] ?>', '<?= htmlspecialchars($work['name']) ?>')">
                                        <div class="zoom-icon"></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>

    <?php } else { ?>
        <div class="empty-cart-message">Фотограф не найден.</div>
    <?php } ?>
</div>

<!-- Модальное окно для просмотра изображений -->
<div id="imageModal" class="modal" style="display: none;">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImage">
    <div class="modal-caption" id="modalCaption"></div>
</div>

<style>
    /* Стили для модального окна */
    .modal {
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .modal.show {
        opacity: 1;
    }

    .modal-content {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        transform: scale(0.7);
        transition: transform 0.3s ease;
    }

    .modal.show .modal-content {
        transform: scale(1);
    }

    .close {
        position: absolute;
        top: 20px;
        right: 35px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 1001;
        transition: color 0.3s ease;
    }

    .close:hover {
        color: #ccc;
    }

    .modal-caption {
        position: absolute;
        bottom: 20px;
        left: 0;
        width: 100%;
        text-align: center;
        color: white;
        font-size: 18px;
        padding: 10px;
        background: rgba(0, 0, 0, 0.7);
        font-family: 'Prosto One';
    }

    /* Стили для контейнера изображений */
    .fotos-work {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .image-container {
        position: relative;
        display: inline-block;
        overflow: hidden;
        border-radius: 8px;
    }

    .portfolio-image {
        cursor: zoom-in;
        transition: transform 0.3s ease, filter 0.3s ease;
        display: block;
        max-width: 100%;
        height: auto;
    }

    .portfolio-image:hover {
        transform: scale(1.05);
        filter: brightness(0.8);
    }

    .zoom-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0);
        font-size: 24px;
        color: white;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        pointer-events: none;
        transition: transform 0.3s ease;
        z-index: 2;
    }

    .image-container:hover .zoom-icon {
        transform: translate(-50%, -50%) scale(1);
    }

    /* Альтернативный вариант с псевдоэлементом вместо эмодзи */
    .portfolio-image::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0);
        font-size: 24px;
        transition: transform 0.3s ease;
        pointer-events: none;
    }

    .image-container:hover .portfolio-image::after {
        transform: translate(-50%, -50%) scale(1);
    }

    /* Адаптивность */
    @media (max-width: 768px) {
        .modal-content {
            max-width: 95%;
            max-height: 95%;
        }

        .close {
            top: 10px;
            right: 20px;
            font-size: 30px;
        }

        .modal-caption {
            font-size: 16px;
            padding: 8px;
        }

        .zoom-icon {
            font-size: 20px;
        }

        .fotos-work {
            gap: 8px;
        }
    }

    @media (max-width: 480px) {
        .zoom-icon {
            font-size: 18px;
        }
    }
</style>

<script>
    // Функция для открытия модального окна
    function openModal(imageSrc, caption) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        const captionText = document.getElementById('modalCaption');

        // Устанавливаем изображение и подпись
        modalImg.src = imageSrc;
        captionText.innerHTML = caption;

        // Показываем модальное окно с анимацией
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);

        // Блокируем прокрутку страницы
        document.body.style.overflow = 'hidden';
    }

    // Функция для закрытия модального окна
    function closeModal() {
        const modal = document.getElementById('imageModal');

        // Скрываем с анимацией
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);

        // Восстанавливаем прокрутку страницы
        document.body.style.overflow = 'auto';
    }

    // Закрытие модального окна при клике вне изображения
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Закрытие модального окна клавишей ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Фильтрация работ по категориям (существующий код)
    document.addEventListener('DOMContentLoaded', function() {
        const categoryBtns = document.querySelectorAll('.category-btn');
        const portfolioWorks = document.querySelectorAll('.work-ph-portgopio');

        categoryBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Убираем активный класс у всех кнопок
                categoryBtns.forEach(b => b.classList.remove('active'));
                // Добавляем активный класс текущей кнопке
                this.classList.add('active');

                const category = this.getAttribute('data-category');

                // Показываем/скрываем работы в зависимости от категории
                portfolioWorks.forEach(work => {
                    if (category === 'all' || work.getAttribute('data-category') === category) {
                        work.style.display = 'block';
                    } else {
                        work.style.display = 'none';
                    }
                });
            });
        });
    });
</script>