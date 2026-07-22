<div class="team-banner">
    <img src="assets/img/team/bigg.png" alt="#" class="banner-team-main">
    <div class="img-team-banner">
        <div class="left-g1">
            <img src="assets/img/team/g1.png" alt="#">
        </div>
        <div class="left-g2">
            <img src="assets/img/team/g2.png" alt="#">
        </div>
        <div class="left-g3">
            <img src="assets/img/team/g3.png" alt="#">
        </div>


        <div class="right-g1">
            <img src="assets/img/team/g2.png" alt="#">
        </div>
        <div class="right-g2">
            <img src="assets/img/team/g1.png" alt="#">
        </div>
        <div class="star-team-banner">
            <img src="assets/img/home/black_star.svg" alt="">
        </div>
        <div class="right-g3">
            <img src="assets/img/team/g4.png" alt="#">
        </div>
    </div>

    <h2 class="title">
        наша <span style="color: #F2EFE4">команда</span>
    </h2>

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


<!-- фотографы -->
<div class="fotogr container">
    <h2 class="title">
        фотографы
    </h2>
    <p class="subtitle">
        Наши фотографы — мастера с уникальным взглядом и авторским стилем. Выберите того, чье творчество откликается в вас, и создадим вместе кадры, которые будут говорить без слов.
    </p>
    <div class="mini-catalog">
        <div class="container">
            <div class="mini_catalog_cards">
                <?php
                // Получаем фотографов с данными из обеих таблиц
                $sql = "SELECT u.id, u.name, u.img, p.desc 
                    FROM user u 
                    JOIN photogs p ON u.id = p.id 
                    WHERE u.role = 3";
                $stmt = $connect->prepare($sql);
                $stmt->execute();
                $photographers = $stmt->fetchAll();

                foreach ($photographers as $photographer) {
                ?>
                    <div class="card_catalog">
                        <div class="card-img">
                            <img src="<?= $photographer['img'] ?>" alt="<?= $photographer['name'] ?>">
                        </div>
                        <div class="price_btn">
                            <p class="name_card">
                                <?= htmlspecialchars($photographer['name']) ?>
                            </p>
                            <a href="?page=phPage&id=<?= $photographer['id'] ?>">
                                <img src="assets/img/team/arrow_red.svg" alt="Перейти к фотографу">
                            </a>
                        </div>
                        <p class="desc_card desc_card1">
                            <?= htmlspecialchars($photographer['desc']) ?>
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- профильные спеуиалисты -->
<div class="profil-spec mt120">
    <h2 class="title">
        профильные специалисты
    </h2>
    <div class="speciapists container">
        <div class="spec-slider">
            <!-- Блок специалиста 1 -->
            <div class="specialist">
                <div class="left-block-spec">
                    <img src="assets/img/team/visaj.png" alt="#">
                    <p class="name-spec">Марина</p>
                    <p class="profil-spec">визажистка</p>
                </div>
                <div class="right-block-spec">
                    <p class="desc-spec">Создаю макияж, который работает на образ и идеально смотрится в кадре. Специализируюсь на естественной эстетике, которая подчеркивает ваши черты, а не скрывает их. делаю макияж любой сложности!</p>
                    <div class="spec-navik">
                        <div class="star-navik"><img src="assets/img/home/red_star.svg" alt="#">
                            <p class="navic">натуральный макияж</p>
                        </div>
                        <div class="star-navik"><img src="assets/img/home/red_star.svg" alt="#">
                            <p class="navic">дневной макияж</p>
                        </div>
                        <div class="star-navik"><img src="assets/img/home/red_star.svg" alt="#">
                            <p class="navic">вечерний макияж</p>
                        </div>
                        <div class="star-navik"><img src="assets/img/home/red_star.svg" alt="#">
                            <p class="navic">креативный макияж</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Слайдер 1 -->
            <div class="slider-container container" data-slider="1">
                <div class="slider-wrapper">
                    <div class="slider">
                        <div class="slide"><img src="assets/img/team/v_s1.png" alt="Фото 1"></div>
                        <div class="slide"><img src="assets/img/team/v_s2.png" alt="Фото 2"></div>
                        <div class="slide"><img src="assets/img/team/v_s3.png" alt="Фото 3"></div>
                        <div class="slide"><img src="assets/img/team/v_s4.png" alt="Фото 4"></div>
                        <div class="slide"><img src="assets/img/team/v_s5.jpg" alt="Фото 5"></div>
                        <div class="slide"><img src="assets/img/team/v_s6.jpg" alt="Фото 6"></div>
                        <div class="slide"><img src="assets/img/team/v_s7.jpg" alt="Фото 6"></div>
                        <div class="slide"><img src="assets/img/team/v_s8.jpg" alt="Фото 6"></div>
                    </div>
                </div>
                <div class="slider-nav">
                    <button class="nav-btn prev-btn"><img src="assets/img/home/icon_arrow.svg" alt="Предыдущий"></button>
                    <button class="nav-btn next-btn"><img src="assets/img/home/icon_arrow.svg" alt="Следующий"></button>
                </div>
            </div>
        </div>

        <div class="spec-slider">
            <!-- Блок специалиста 2 -->
            <div class="specialist">
                <div class="right-block-spec">
                    <p class="desc-spec">Создаю гармоничные образы, которые раскрывают вашу индивидуальность и идеально работают в кадре. Подбираю одежду с учетом концепции съемки, особенностей фигуры и личного стиля.</p>
                    <div class="spec-navik">
                        <div class="star-navik"><img src="assets/img/home/red_star.svg" alt="#">
                            <p class="navic">повседневный стиль</p>
                        </div>
                        <div class="star-navik"><img src="assets/img/home/red_star.svg" alt="#">
                            <p class="navic">FASHION-КОНЦЕПТ</p>
                        </div>
                        <div class="star-navik"><img src="assets/img/home/red_star.svg" alt="#">
                            <p class="navic">РЕТРО-ЭСТЕТИКА</p>
                        </div>
                        <div class="star-navik"><img src="assets/img/home/red_star.svg" alt="#">
                            <p class="navic">МИНИМАЛИЗМ</p>
                        </div>
                    </div>
                </div>
                <div class="left-block-spec">
                    <img src="assets/img/team/stilist.png" alt="#">
                    <p class="name-spec">Марья</p>
                    <p class="profil-spec">стилистка</p>
                </div>
            </div>
            <!-- Слайдер 2 -->
            <div class="slider-container container" data-slider="2">
                <div class="slider-wrapper">
                    <div class="slider">
                        <div class="slide"><img src="assets/img/team/s_s1.png" alt="Фото 1"></div>
                        <div class="slide"><img src="assets/img/team/s_s2.png" alt="Фото 2"></div>
                        <div class="slide"><img src="assets/img/team/s_s3.png" alt="Фото 3"></div>
                        <div class="slide"><img src="assets/img/team/s_s4.png" alt="Фото 4"></div>
                        <div class="slide"><img src="assets/img/team/s_s5.jpg" alt="Фото 5"></div>
                        <div class="slide"><img src="assets/img/team/s_s6.jpg" alt="Фото 6"></div>
                        <div class="slide"><img src="assets/img/team/s_s7.jpg" alt="Фото 6"></div>
                        <div class="slide"><img src="assets/img/team/s_s8.jpg" alt="Фото 6"></div>
                    </div>
                </div>
                <div class="slider-nav">
                    <button class="nav-btn prev-btn"><img src="assets/img/home/icon_arrow.svg" alt="Предыдущий"></button>
                    <button class="nav-btn next-btn"><img src="assets/img/home/icon_arrow.svg" alt="Следующий"></button>
                </div>
            </div>
        </div>
    </div>
</div>
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

    // Инициализация всех слайдеров
    document.addEventListener('DOMContentLoaded', function() {
        const sliderContainers = document.querySelectorAll('.slider-container');
        sliderContainers.forEach(container => {
            new Slider(container);
        });
    });
</script>