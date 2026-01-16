<?php include('includes/header.php'); ?>

<div class="container-fluid tm-content-container py-5">
    <div class="container gallery-container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="tm-link-white">Our Collections</h2>
                <p>Curated apparel for the modern lifestyle.</p>
            </div>
        </div>
        
        <div id="product-list" class="row">
            <div class="col-12 text-center">
                <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Fetching the latest styles from our warehouse...</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productList = document.getElementById('product-list');

    fetch('../backend/products/get_collections.php')
        .then(response => response.json())
        .then(data => {
            productList.innerHTML = '';

            if (data.length === 0) {
                productList.innerHTML = '<p class="text-center">No products found.</p>';
                return;
            }

            data.forEach(product => {
                const card = `
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-5">
                        <figure class="effect-julia">
                            <img src="assets/img/${product.IMAGE_PATH}" alt="${product.NAME}" class="img-fluid">
                            <figcaption>
                                <h2>${product.NAME}</h2>
                                <p>$${product.PRICE}</p>
                                <p>View Details</p>
                                <a href="detail.php?id=${product.ID}">View more</a>
                            </figcaption>			
                        </figure>
                    </div>
                `;
                productList.innerHTML += card;
            });
        })
        .catch(error => {
            console.error('Error:', error);
            productList.innerHTML = '<p class="text-center">Could not load products.</p>';
        });
});
</script>

<?php include('includes/footer.php'); ?>