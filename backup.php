<!-- List Card -->
<div class="row">
    <div class="col-12">
        <div class="dl-card">
            <!-- Section Title strip -->
            <div class="dl-strip">
                <div class="dl-strip-title">Saved Documents</div>
                <div class="dl-strip-sub">2 documents found</div>
            </div>

            <!-- Table Head -->
            <div class="dl-row dl-headrow">
                <div class="dl-col col-property">Property Details</div>
                <div class="dl-col col-area">Area Information</div>
                <div class="dl-col col-owner">Owner</div>
                <div class="dl-col col-created">Created</div>
                <div class="dl-col col-status">Status</div>
                <div class="dl-col col-actions">Actions</div>
            </div>

            <!-- Row 1 -->
            <div class="dl-row">
                <div class="dl-col col-property">
                    <div class="prop-title">Green Valley Apartments</div>
                    <div class="prop-meta">ID: PROP-001</div>
                    <div class="prop-meta prop-loc">
                        <img src="assets/icons/location.png" alt="">
                        123 Main Street, Sector 15, New Mumbai
                    </div>
                </div>

                <div class="dl-col col-area">
                    <span class="pill pill-soft">residential</span>
                    <div class="area-size">2500 sq ft</div>
                </div>

                <div class="dl-col col-owner">
                    <div class="owner-name">John Smith</div>
                </div>

                <div class="dl-col col-created">
                    <div class="date-wrap">
                        <img src="assets/icons/calendar.png" alt="">
                        <span>9/10/2024</span>
                    </div>
                </div>

                <div class="dl-col col-status">
                    <span class="status active">active</span>
                </div>

                <div class="dl-col col-actions">
                    <button class="act-btn" aria-label="View">
                        <img src="assets/icons/edit.png" alt="">
                    </button>
                    <button class="act-btn" aria-label="View">
                        <img src="assets/icons/eye.png" alt="">
                    </button>
                    <button class="act-btn" aria-label="Download">
                        <img src="assets/icons/download.png" alt="">
                    </button>
                    <button class="act-btn danger" aria-label="Delete">
                        <img src="assets/icons/trash.png" alt="">
                    </button>
                </div>
            </div>
            <!-- Row 1 -->
            <div class="dl-row">
                <div class="dl-col col-property">
                    <div class="prop-title">Green Valley Apartments</div>
                    <div class="prop-meta">ID: PROP-001</div>
                    <div class="prop-meta prop-loc">
                        <img src="assets/icons/location.png" alt="">
                        123 Main Street, Sector 15, New Mumbai
                    </div>
                </div>

                <div class="dl-col col-area">
                    <span class="pill pill-soft">residential</span>
                    <div class="area-size">2500 sq ft</div>
                </div>

                <div class="dl-col col-owner">
                    <div class="owner-name">John Smith</div>
                </div>

                <div class="dl-col col-created">
                    <div class="date-wrap">
                        <img src="assets/icons/calendar.png" alt="">
                        <span>9/10/2024</span>
                    </div>
                </div>

                <div class="dl-col col-status">
                    <span class="status active">active</span>
                </div>

                <div class="dl-col col-actions">
                    <button class="act-btn" aria-label="View">
                        <img src="assets/icons/edit.png" alt="">
                    </button>
                    <button class="act-btn" aria-label="View">
                        <img src="assets/icons/eye.png" alt="">
                    </button>
                    <button class="act-btn" aria-label="Download">
                        <img src="assets/icons/download.png" alt="">
                    </button>
                    <button class="act-btn danger" aria-label="Delete">
                        <img src="assets/icons/trash.png" alt="">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ---------- Equal column widths + gaps (CSS Grid) ---------- */
    .hh-docs .dl-card {
        --col-gap: 16px;
    }

    .hh-docs .dl-row,
    .hh-docs .dl-headrow {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        /* 6 equal columns */
        align-items: center;
        column-gap: var(--col-gap);
        padding: 14px 16px;
        border-bottom: 1px solid var(--hh-line);
    }

    .hh-docs .dl-row:last-child {
        border-bottom: 0;
    }

    .hh-docs .dl-col {
        padding: 4px 6px;
        min-width: 0;
    }

    /* Head row visuals */
    .hh-docs .dl-headrow {
        background: #f8fbfa;
        font-weight: 700;
        color: #3b3f44;
    }

    /* Property detail styles */
    .hh-docs .prop-title {
        font-weight: 800;
        color: #111;
    }

    .hh-docs .prop-meta {
        color: #6b7280;
        font-size: 13px;
        margin-top: 4px;
    }

    .hh-docs .prop-loc {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .hh-docs .prop-loc img {
        width: 14px;
        height: 14px;
        opacity: .7;
    }

    /* Area info */
    .hh-docs .pill {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid var(--hh-primary);
        color: var(--hh-primary);
        font-weight: 700;
        font-size: 12px;
        background: #fff;
    }

    .hh-docs .pill-soft {
        background: #f0f7f5;
    }

    .hh-docs .area-size {
        margin-top: 8px;
        color: #111;
    }

    /* Owner / date */
    .hh-docs .owner-name {
        font-weight: 700;
        color: #111;
    }

    .hh-docs .date-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #111;
    }

    .hh-docs .date-wrap img {
        width: 16px;
        height: 16px;
        opacity: .75;
    }

    /* Status */
    .hh-docs .status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        font-weight: 800;
        font-size: 12px;
    }

    .hh-docs .status.active {
        background: #e3f5ef;
        color: var(--hh-primary);
    }

    /* Actions */
    .hh-docs .col-actions {
        justify-self: end;
        display: flex;
        gap: 8px;
    }

    .hh-docs .act-btn {
        border: 1px solid var(--hh-line);
        background: #fff;
        border-radius: 10px;
        padding: 8px 10px;
        cursor: pointer;
        transition: transform .12s ease, border-color .18s ease, background .18s ease;
    }

    .hh-docs .act-btn:hover {
        transform: translateY(-1px);
        border-color: var(--hh-primary);
        background: #f5faf8;
    }

    .hh-docs .act-btn img {
        width: 16px;
        height: 16px;
    }

    .hh-docs .act-btn.danger:hover {
        background: #fff0f0;
        border-color: #e24545;
    }

    /* ---------- Responsive stacking ---------- */
    @media (max-width: 991.98px) {
        .hh-docs .dl-headrow {
            display: none;
        }

        .hh-docs .dl-row {
            grid-template-columns: 1fr 1fr 1fr;
            /* 3 equal columns */
            row-gap: 10px;
        }

        .hh-docs .col-property {
            grid-column: 1 / -1;
        }

        /* full width on top */
        .hh-docs .col-actions {
            grid-column: 1 / -1;
            justify-self: start;
        }
    }

    @media (max-width: 575.98px) {
        .hh-docs .dl-row {
            grid-template-columns: 1fr;
        }

        /* single column */
        .hh-docs .col-area,
        .hh-docs .col-owner,
        .hh-docs .col-created,
        .hh-docs .col-status,
        .hh-docs .col-actions {
            grid-column: 1 / -1;
        }
    }
</style>


<!-- Project Information 3  -->
<div class="col-12 col-lg-6">
    <section class="section">
        <div class="section-head">
            <img src="assets/icons/information.png" alt="Info">
            <h3>Project Information 3</h3>
        </div>
        <div class="section-sub">Primary property details and identification</div>

        <div class="section-body">

            <div class="row">

                <!-- Field: project_title_3 -->
                <div class="col-12">
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

            </div>


        </div>
    </section>
</div>