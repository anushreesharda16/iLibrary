<?php include 'config/constants.php'; ?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>
    /* Minimal custom styles */
    .intro-section {
        padding: 3rem 1rem;
        text-align: center;
        max-width: 700px;
        margin: 0 auto 3rem auto;
    }

    .btn-browse {
        background-color: #007bff;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 30px;
        font-weight: 500;
        transition: background-color 0.3s ease;
        box-shadow: 0 4px 10px rgb(0 123 255 / 0.3);
    }

    .btn-browse:hover {
        background-color: #0056b3;
        box-shadow: 0 6px 15px rgb(0 86 179 / 0.5);
        color: white;
    }

    .cta-section {
        text-align: center;
        margin-bottom: 3rem;
    }

    .btn-outline-custom {
        border-color: #007bff;
        color: #007bff;
        font-weight: 500;
        border-radius: 30px;
        padding: 0.6rem 1.5rem;
        margin: 0 0.4rem;
        transition: all 0.3s ease;
    }

    .btn-outline-custom:hover {
        background-color: #007bff;
        color: white;
    }

    /* Carousel card styles */
    .category-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        background: #fff;
        transition: transform 0.3s ease;
    }

    .category-card:hover {
        transform: scale(1.05);
    }

    .category-card img {
        width: 100%;
        height: 180px;
        object-fit: contain; 
        background-color: #f8f9fa;
    }

    .category-card-body {
        padding: 1rem;
        text-align: center;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: #007bff;
        border-radius: 50%;
    }
</style>

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
                                    <p>Dive into the past with rich historic</p>
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

                <!-- Add more categories here -->

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