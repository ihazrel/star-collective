<?php include('includes/header.php'); ?>

<div class="container-fluid tm-content-container py-5">
    <div class="mx-auto page-width-2">
        <div class="row">
            <div class="col-md-6 me-0 ms-auto">
                <h2 class="mb-4">Contact Us</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 tm-contact-left">
                <form action="#" method="POST" class="contact-form">
                    <div class="input-group tm-mb-30">
                        <input name="name" type="text" 
                               class="form-control rounded-0 border-top-0 border-end-0 border-start-0" 
                               placeholder="Name" required>
                    </div>
                    <div class="input-group tm-mb-30">
                        <input name="email" type="email" 
                               class="form-control rounded-0 border-top-0 border-end-0 border-start-0" 
                               placeholder="Email" required>
                    </div>
                    <div class="input-group tm-mb-30">
                        <textarea rows="5" name="message" 
                                  class="textarea form-control rounded-0 border-top-0 border-end-0 border-start-0" 
                                  placeholder="Message" required></textarea>
                    </div>
                    <div class="input-group justify-content-end">
                        <input type="submit" class="btn btn-primary tm-btn-pad-2" value="Send">
                    </div>
                </form>
            </div>

            <div class="col-md-6 tm-contact-right">
                <p class="mb-4">
                    Have questions about our latest collection or need assistance with your membership? 
                    Contact our support team below.
                </p>
                
                <div class="mb-3">
                    Email: <a href="mailto:info@starcollective.com" class="tm-link-white">info@starcollective.com</a>
                </div>
                
                <div class="tm-mb-45">
                    Tel: <a href="tel:0123456789" class="tm-link-white">012-3456 789</a>
                </div>

                <div class="map-outer">
                    <div class="gmap-canvas">
                        <iframe width="100%" height="400" id="gmap-canvas"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4741.134740909793!2d102.45530851097412!3d2.227615251520934!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d1c32d2db6b385%3A0x9e5830449f08e4ea!2sFSKM%20UiTM%20Kampus%20Jasin%20Melaka!5e0!3m2!1sen!2smy!4v1768244748640!5m2!1sen!2smy"
                            frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>