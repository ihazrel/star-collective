<?php include('includes/header.php'); ?>

<div class="container-fluid tm-content-container py-5">
    <div class="mx-auto page-width-1 tm-bg-dark content-pad tm-border-top tm-border-bottom">
        <div id="detail-content" class="row">
            <p class="text-center">Loading product details...</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const productId = params.get('id');
    const content = document.getElementById('detail-content');

    if(!productId) {
        content.innerHTML = "Product not found.";
        return;
    }

    fetch(`../backend/products/get_details.php?id=${productId}`)
        .then(res => res.json())
        .then(product => {
            content.innerHTML = `
                <div class="col-md-6">
                    <img src="assets/img/${product.IMAGE_PATH}" class="img-fluid rounded" alt="${product.NAME}">
                </div>
                <div class="col-md-6">
                    <h2 class="highlight mb-3">${product.NAME}</h2>
                    <h3 class="mb-4">$${product.PRICE}</h3>
                    <p class="mb-4">${product.DESCRIPTION}</p>
                    <p class="mb-5">In Stock: ${product.STOCK}</p>
                    
                    <form action="../backend/customer/add_to_cart.php" method="POST">
                        <input type="hidden" name="product_id" value="${product.ID}">
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="qty" value="1" min="1" max="${product.STOCK}" class="form-control w-25">
                        </div>
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                </div>
            `;
        });
});
</script>

<?php include('includes/footer.php'); ?>