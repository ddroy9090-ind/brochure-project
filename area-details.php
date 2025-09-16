<?php
// index.php (Dashboard)

// 1) Auth guard MUST be first (no output before this)
require_once __DIR__ . '/includes/auth_check.php';

// 2) Optional: page title for your header include
$pageTitle = 'Dashboard';

// 3) Common header (HTML <head>, opening <body>, etc.)
require_once __DIR__ . '/includes/common-header.php';
?>

<div class="container-fluid cms-layout">
    <div class="row h-100">

        <!-- Sidebar -->
        <?php include 'includes/sidebar.php' ?>

        <!-- Main Content -->
        <div class="col content" id="content">
            <!-- Top Navbar -->
            <?php include 'includes/topbar.php' ?>

            <!-- Dashboard Content -->
            <div class="p-2">
                <form class="hh-area-form" action="#" method="post" novalidate>
                    <div class="form-section">

                        <div class="form-wrap">
                            <!-- Title -->
                            <div class="page-title">
                                <span class="pi-icon">
                                    <img src="assets/icons/property-information.png" alt="Info">
                                </span>
                                <h1>Project Information</h1>
                            </div>
                            <div class="page-sub">
                                Enter comprehensive property and area information
                            </div>

                            <!-- Two columns -->
                            <div class="row g-3">

                                <!-- Basic Information -->
                                <div class="col-12 col-lg-6">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/basic-info.png" alt="Info">
                                            <h3>Basic Information</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="field">
                                                        <label for="propertyId">Property ID <span class="req">*</span></label>
                                                        <!-- Field: property_id -->
                                                        <input type="text" id="propertyId" name="property_id" required>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="field">
                                                        <label for="regNo">Registration No.</label>
                                                        <!-- Field: registration_no -->
                                                        <input type="text" id="regNo" name="registration_no">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="field">
                                                        <label for="propName">Property Name <span class="req">*</span></label>
                                                        <!-- Field: property_name -->
                                                        <input type="text" id="propName" name="property_name" required>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="field">
                                                        <label for="address">Full Address <span class="req">*</span></label>
                                                        <!-- Field: address -->
                                                        <textarea id="address" name="address" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Property Details -->
                                <div class="col-12 col-lg-6">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/property-details.png" alt="Measurements">
                                            <h3>Property Details</h3>
                                        </div>
                                        <div class="section-sub">Detailed area and dimension specifications</div>

                                        <div class="section-body">
                                            <div class="row">

                                                <!-- Upload Banner Image -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="bannerImage">Upload Banner Image</label>
                                                        <div class="upload-field">
                                                            <input type="file" id="bannerImage" name="banner_image" accept="image/*" class="upload-input">
                                                            <div class="upload-box">
                                                                <div class="upload-stack">
                                                                    <!-- Arrow icon -->
                                                                    <div>
                                                                        <img src="assets/icons/upload.png" alt="" width="30">
                                                                    </div>
                                                                    <div class="upload-btn">Browse</div>
                                                                    <div class="upload-hint">drop a file here</div>
                                                                    <div class="upload-note"><span class="req">*</span>File supported .png, .jpg &amp; .webp</div>
                                                                    <div class="upload-name" id="bnrName"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Project Name -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="projectName">Project Name</label>
                                                        <input type="text" id="projectName" name="project_name">
                                                    </div>
                                                </div>

                                                <!-- Developer Name -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="developerName">Developer Name</label>
                                                        <input type="text" id="developerName" name="developer_name">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- About Project -->
                                <div class="col-12 col-lg-6">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/about-project.png" alt="Info">
                                            <h3>About Project</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">
                                            <div class="row">
                                                <!-- Title -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="pageTitle">Title</label>
                                                        <input type="text" id="pageTitle" name="title">
                                                    </div>
                                                </div>

                                                <!-- Field: about_details -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="aboutDetails">About Project – Detailed Description</label>
                                                        <textarea id="aboutDetails" name="about_details"></textarea>
                                                    </div>
                                                </div>

                                                <!-- Field: about_developer -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="aboutDeveloper">About the Developer</label>
                                                        <textarea id="aboutDeveloper" name="about_developer"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Payment Information -->
                                <div class="col-12 col-lg-6">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/payment-plan.png" alt="Info">
                                            <h3>Payment Plan</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">
                                            <div class="row">
                                                <!-- Field: starting_price -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="startingPrice">Starting Price</label>
                                                        <input type="text" id="startingPrice" name="starting_price">
                                                    </div>
                                                </div>

                                                <!-- Field: payment_plan -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="paymentPlan">Payment Plan</label>
                                                        <input type="text" id="paymentPlan" name="payment_plan">
                                                    </div>
                                                </div>

                                                <!-- Field: handover_date -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="handoverDate">Handover Date</label>
                                                        <input type="date" id="handoverDate" name="handover_date">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Area Information -->
                                <div class="col-12 col-lg-12">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/area-information.png" alt="Info">
                                            <h3>Area Information</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">

                                            <div class="row">
                                                <!-- Field: area_image -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="areaImage">Upload Area Image</label>
                                                        <div class="upload-field">
                                                            <input type="file" id="areaImage" name="area_image" accept="image/*" class="upload-input">
                                                            <div class="upload-box">
                                                                <div class="upload-stack">
                                                                    <div>
                                                                        <img src="assets/icons/upload.png" alt="" width="30">
                                                                    </div>
                                                                    <div class="upload-btn">Browse</div>
                                                                    <div class="upload-hint">drop a file here</div>
                                                                    <div class="upload-note"><span class="req">*</span>File supported .png, .jpg &amp; .webp</div>
                                                                    <div class="upload-name" id="areaName"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Field: area_title -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="areaTitle">Area Title</label>
                                                        <input type="text" id="areaTitle" name="area_title">
                                                    </div>
                                                </div>

                                                <!-- Field: area_heading -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="areaHeading">Area Heading</label>
                                                        <input type="text" id="areaHeading" name="area_heading">
                                                    </div>
                                                </div>

                                                <!-- Field: area_description -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="areaDescription">Area Description</label>
                                                        <textarea id="areaDescription" name="area_description"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </section>
                                </div>

                                <!-- Ameneties -->
                                <div class="col-12 col-lg-12">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/amenities.png" alt="Info">
                                            <h3>Ameneties</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">

                                            <!-- Field: amenities[] -->
                                            <div class="row amenity-tabs">
                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-swimming" name="amenities[]" value="swimming_pool" class="amenity-input">
                                                        <label for="am-swimming" class="amenity-btn">
                                                            <span class="amenity-text">Swimming Pool</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-gym" name="amenities[]" value="gymnasium" class="amenity-input">
                                                        <label for="am-gym" class="amenity-btn">
                                                            <span class="amenity-text">Gymnasium</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-kids" name="amenities[]" value="kids_play_area" class="amenity-input">
                                                        <label for="am-kids" class="amenity-btn">
                                                            <span class="amenity-text">Kid’s Play Area</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-jog" name="amenities[]" value="jogging_area" class="amenity-input">
                                                        <label for="am-jog" class="amenity-btn">
                                                            <span class="amenity-text">Jogging Area</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-garden" name="amenities[]" value="garden_zones" class="amenity-input">
                                                        <label for="am-garden" class="amenity-btn">
                                                            <span class="amenity-text">Garden Zones</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-sports" name="amenities[]" value="sports_courts" class="amenity-input">
                                                        <label for="am-sports" class="amenity-btn">
                                                            <span class="amenity-text">Sports Courts</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-sauna" name="amenities[]" value="sauna_steam_rooms" class="amenity-input">
                                                        <label for="am-sauna" class="amenity-btn">
                                                            <span class="amenity-text">Sauna & Steam Rooms</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-yoga" name="amenities[]" value="yoga_meditation_decks" class="amenity-input">
                                                        <label for="am-yoga" class="amenity-btn">
                                                            <span class="amenity-text">Yoga & Meditation Decks</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-bbq" name="amenities[]" value="bbq_areas" class="amenity-input">
                                                        <label for="am-bbq" class="amenity-btn">
                                                            <span class="amenity-text">BBQ Areas</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>

                                    </section>
                                </div>

                                <!-- Project Information -->
                                <div class="col-12 col-lg-12">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/information.png" alt="Info">
                                            <h3>Project Information</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">

                                            <div class="row">
                                                <!-- Field: project_title_2 -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="projectTitle2">Project Title 2</label>
                                                        <input type="text" id="projectTitle2" name="project_title_2">
                                                    </div>
                                                </div>



                                                <!-- Field: project_title_3 -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="projectTitle3">Project Title 3</label>
                                                        <input type="text" id="projectTitle3" name="project_title_3">
                                                    </div>
                                                </div>

                                                <!-- Field: price_from -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="priceFrom">Price From</label>
                                                        <input type="text" id="priceFrom" name="price_from">
                                                    </div>
                                                </div>

                                                <!-- Field: handover_date_3 -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="handoverDate3">Hand Over Date</label>
                                                        <input type="date" id="handoverDate3" name="handover_date_3">
                                                    </div>
                                                </div>

                                                <!-- Field: location_3 -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="location3">Location</label>
                                                        <input type="text" id="location3" name="location_3">
                                                    </div>
                                                </div>

                                                <!-- Field: development_time -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="developmentTime">Development Time</label>
                                                        <input type="text" id="developmentTime" name="development_time">
                                                    </div>
                                                </div>

                                                <!-- Field: Upload_area_image -->
                                                <div class="col-12 col-lg-12">
                                                    <div class="field">
                                                        <label for="projectImage2">Upload Project Image</label>
                                                        <div class="upload-field">
                                                            <input type="file" id="projectImage2" name="project_image_2" accept="image/*" class="upload-input">
                                                            <div class="upload-box sm">
                                                                <div class="upload-stack">
                                                                    <div>
                                                                        <img src="assets/icons/upload.png" alt="" width="30">
                                                                    </div>
                                                                    <div class="upload-btn">Browse</div>
                                                                    <div class="upload-hint">drop a file here</div>
                                                                    <div class="upload-note"><span class="req">*</span>File supported .png, .jpg &amp; .webp</div>
                                                                    <div class="upload-name" id="proj2Name"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Field: project_description_2 -->
                                                <div class="col-12 col-lg-12">
                                                    <div class="field">
                                                        <label for="projectDescription2">Project Description 2</label>
                                                        <textarea id="projectDescription2" name="project_description_2"></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Payment Plan -->
                                <div class="col-12 col-lg-12">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/payment-plan-method.png" alt="Info">
                                            <h3>Payment Plan</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">

                                            <div class="row">
                                                <!-- Field: down_payment -->
                                                <div class="col-12 col-lg-4">
                                                    <div class="field">
                                                        <label for="downPayment">Down Payment</label>
                                                        <input type="text" id="downPayment" name="down_payment">
                                                    </div>
                                                </div>

                                                <!-- Field: pre_handover -->
                                                <div class="col-12 col-lg-4">
                                                    <div class="field">
                                                        <label for="preHandover">Pre Handover</label>
                                                        <input type="text" id="preHandover" name="pre_handover">
                                                    </div>
                                                </div>

                                                <!-- Field: handover -->
                                                <div class="col-12 col-lg-4">
                                                    <div class="field">
                                                        <label for="handover">Handover</label>
                                                        <input type="text" id="handover" name="handover">
                                                    </div>
                                                </div>

                                                <!-- Field: transactions_image -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="transactionsImage">Upload Transactions Image</label>
                                                        <div class="upload-field">
                                                            <input type="file" id="transactionsImage" name="transactions_image" accept="image/*" class="upload-input">
                                                            <div class="upload-box">
                                                                <div class="upload-stack">
                                                                    <div>
                                                                        <img src="assets/icons/upload.png" width="30" alt="">
                                                                    </div>
                                                                    <div class="upload-btn">Browse</div>
                                                                    <div class="upload-hint">drop a file here</div>
                                                                    <div class="upload-note"><span class="req">*</span>File supported .png, .jpg &amp; .webp</div>
                                                                    <div class="upload-name" id="txnName"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Floor Plan -->
                                <div class="col-12">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/floor-plan.png" alt="Info">
                                            <h3>Floor Plan</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">

                                            <div class="row">
                                                <!-- Upload Property Images (multiple) -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="propertyImages">Upload Property Images</label>
                                                        <div class="upload-field">
                                                            <input type="file" id="propertyImages" name="property_images[]" accept="image/*" multiple class="upload-input">
                                                            <div class="upload-box">
                                                                <div class="upload-stack">
                                                                    <!-- Arrow -->
                                                                    <div>
                                                                        <img width="30" src="assets/icons/upload.png" alt="">
                                                                    </div>
                                                                    <div class="upload-btn">Browse</div>
                                                                    <div class="upload-hint">drop files here</div>
                                                                    <div class="upload-note"><span class="req">*</span>File supported .png, .jpg &amp; .webp — multiple allowed</div>
                                                                    <div class="upload-name" id="propertyImagesName"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Upload Floor Plan (image or PDF) -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="floorPlanFile1">Upload Floor Plan</label>
                                                        <div class="upload-field">
                                                            <input type="file" id="floorPlanFile1" name="floor_plan_file[]" multiple accept="image/*,application/pdf" class="upload-input">
                                                            <div class="upload-box">
                                                                <div class="upload-stack">
                                                                    <!-- Arrow -->
                                                                    <div>
                                                                        <img width="30" src="assets/icons/upload.png" alt="">
                                                                    </div>
                                                                    <div class="upload-btn">Browse</div>
                                                                    <div class="upload-hint">drop a file here</div>
                                                                    <div class="upload-note"><span class="req">*</span>File supported .png, .jpg, .webp &amp; .pdf - multiple allowed</div>
                                                                    <div class="upload-name" id="floorPlanFile1Name"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                            </div>
                            <!-- /row -->


                        </div>

                        <!-- Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn-hh outline">Reset</button>
                            <button type="submit" class="btn-hh">Submit Details</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php include 'includes/common-footer.php' ?>