<?php include 'config/constants.php'; ?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/navbar.php'; ?>

<main class="container py-5">

    <!-- Intro Section -->
    <section class="intro-section">
        <h1 class="display-4 mb-3">Welcome to iLibrary</h1>
        <p class="lead text-muted">Discover thousands of books across a variety of genres and categories.</p>
    </section>

    <!-- Categories Carousel Preview -->
    <section>
        <h3 class="text-center mb-4">Explore Categories</h3>
        <div id="categoriesCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">

                <div class="carousel-item active">
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <div class="category-card">
                                <img src="assets/images/book1.jpg" alt="Fiction">
                                <div class="category-card-body">
                                    <h5>Fiction</h5>
                                    <p>Engaging novels and stories</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <div class="category-card">
                                <img src="assets/images/Sci1.jpeg" alt="Science">
                                <div class="category-card-body">
                                    <h5>Science</h5>
                                    <p>Books to boost your scientific knowledge</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <div class="category-card">
                                <img src="assets/images/SH1.jpeg" alt="Self Help">
                                <div class="category-card-body">
                                    <h5>Self-Help</h5>
                                    <p>Books to unleash your inner strength.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <div class="category-card">
                                <img src="assets/images/R3.jpeg" alt="Romance">
                                <div class="category-card-body">
                                    <h5>Romance</h5>
                                    <p>Read romance books</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="carousel-item">
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <div class="category-card">
                                <img src="assets/images/Bio1.jpg" alt="Biographies">
                                <div class="category-card-body">
                                    <h5>Biographies</h5>
                                    <p>Dive into the Biographies.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#categoriesCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#categoriesCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>
