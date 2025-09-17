<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Report</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');

        @page {
            margin: 0 !important;
        }

        @page :first {
            margin: 0 !important;
        }

        body {
            font-family: "Roboto", sans-serif;
            font-size: 14px;
            color: #666;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        h1,
        h3 {
            color: #fff;
            margin: 0 0 10px 0;
            font-weight: 400;
        }

        p {
            margin: 15px 0;
            text-align: justify;
        }

        ul {
            margin: 0 0 10px 20px;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        /* Ensure clean page breaks */
        .page-break-before {
            page-break-before: always !important;
            clear: both !important;
        }

        .table-report {
            width: 100%;
            margin: auto;
            margin-top: 20px !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table td {
            font-size: 14px;
            padding: 5px;
            vertical-align: top;
        }

        /* Cover page - full bleed */
        .cover-page {
            width: 210mm;
            height: 297mm;
            background-size: cover;
            background-position: center;
            position: relative;
            margin: 0;
            padding: 0;
            display: block;
        }

        .cover-page h2 {
            font-size: 18px;
            color: #111;
            max-width: 500px;
            margin-bottom: 30px;
        }

        .cover-content {
            position: absolute;
            top: 25% !important;
            left: 50px;
        }

        .last-page {
            width: 210mm;
            height: 297mm;
            top: -4.5% !important;
            background-size: cover;
            background-position: center;
            position: relative;
            margin: 0px -46px;
            margin-top: -20px;
            padding: 0;
            display: block;
        }

        .topbar {
            width: 90%;
            margin: 20px auto;
        }

        .topbar p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }

        .top-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-content img {
            max-width: 180px;
        }

        .generalInformation {
            margin-bottom: 20px;
        }

        .generalInformation .numbering {
            display: inline-block;
            width: 40px;
            font-size: 16px;
            color: #005c7c;
        }

        .generalInformation .heading-text {
            display: inline-block;
            color: #005c7c;
            font-size: 16px;
        }

        .generalInformation p {
            margin-left: 40px;
            color: #666;
        }

        .table-report h1 {
            margin-top: 0px;
            font-size: 16px;
            text-align: center;
            background: #005c7c;
            padding: 5px;
            color: #fff;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .table-start td {
            border: 2px solid #eee;
            padding: 6px 10px;
            color: #666;
        }

        .table-start td.green-color {
            color: #005c7c;
            width: 35%;
            background-color: #f9f9f9;
        }

        /* Prevent orphans */
        .no-page-break {
            page-break-inside: avoid !important;
        }

        /* Images */
        .comparable-image {
            width: 100%;
            max-width: 100%;
            height: auto;
            display: block;
            margin: 10px 0;
        }

        /* Signature section */
        .signature-section {
            page-break-inside: avoid;
            margin-top: 30px;
        }

        .signature-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            margin-top: 30px;
        }

        .signature-table td {
            vertical-align: top;
            padding: 10px;
            border: none;
        }

        .signature-image {
            width: 200px;
            height: 60px;
            display: block;
            margin-bottom: 10px;
        }

        .signature-logo {
            width: 120px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
    </style>

</head>

<body>
    <?php $report = $report ?? []; ?>

    <!-- COVER PAGE -->
    <div class="cover-page" style="background-image: url('img/front-cover-green-1.png');">
        <div class="cover-content">
            <h2><?= htmlspecialchars($report['address'] ?? '123 Sample Street, City') ?></h2>
            <div style="margin-bottom: 15px;">
                <strong style="color: #d01f28; display:block; margin-bottom:5px">Prepared For:</strong>
                <span style="color: #111;">National Bank of Fujairah</span>
            </div>

            <div style="margin-bottom: 15px;">
                <strong style="color: #d01f28; display:block; margin-bottom:5px">Date Of Instructions:</strong>
                <span style="color: #111;"><?= htmlspecialchars($report['date_of_instructions'] ?? 'N/A') ?></span>
            </div>

            <div style="margin-bottom: 15px;">
                <strong style="color: #d01f28; display:block; margin-bottom:5px">Date Of Report:</strong>
                <span style="color: #111;"><?= htmlspecialchars($report['date_of_report'] ?? 'N/A') ?></span>
            </div>

            <div style="margin-bottom: 15px;">
                <strong style="color: #d01f28; display:block; margin-bottom:5px">Bank Reference Number:</strong>
                <span style="color: #111;"><?= htmlspecialchars($report['bank_reference_number'] ?? 'N/A') ?></span>
            </div>

            <div style="margin-bottom: 15px;">
                <strong style="color: #d01f28; display:block; margin-bottom:5px">Reliant Reference Number:</strong>
                <span style="color: #111;"><?= htmlspecialchars($report['reliant_reference_number'] ?? 'N/A') ?></span>
            </div>
        </div>
    </div>
    <div class="page-break-before"></div>
    <!-- DESKTOP RESIDENTIAL VALUATION REPORT -->

    <div class="table-report">
        <table
            style="width:100%; border:none; border-collapse:collapse; margin-bottom:20px; border-bottom:.5px #111 solid">
            <tr>
                <td style="width:70%; vertical-align:top; text-align:left;">
                    <p style="margin:0; font-size:12px;">
                        <strong style="color:#d01f28;">Prepared For:</strong>
                        <span>National Bank of Fujairah</span>
                    </p>
                    <p style="margin:0; font-size:12px;">
                        <strong style="color:#d01f28;">Reliant Reference Number:</strong>
                        <span><?= htmlspecialchars($report['reliant_reference_number'] ?? 'N/A') ?></span>
                    </p>
                </td>
                <td style="width:30%; text-align:right; vertical-align:top;">
                    <img src="img/logo.png" alt="Logo" style="max-width:150px; height:auto;">
                </td>
            </tr>
        </table>

        <h1>DESKTOP RESIDENTIAL VALUATION REPORT</h1>
        <table class="table-start" style="width:100%; border-collapse: collapse;">
            <tr>
                <td class="green-color">Date Of Instructions</td>
                <td><?= htmlspecialchars($report['date_of_instructions'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <td class="green-color">Reliant Reference Number</td>
                <td><?= htmlspecialchars($report['reliant_reference_number'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <td class="green-color">Bank Reference Number</td>
                <td><?= htmlspecialchars($report['bank_reference_number'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <td class="green-color">Prepared For</td>
                <td>National Bank of Fujairah</td>
            </tr>
        </table>
    </div>

    <!-- VALUATION SUMMARY -->
    <div class="table-report no-page-break">
        <h1>VALUATION SUMMARY</h1>
        <table class="table-start" style="width:100%; border-collapse: collapse;">
            <tr>
                <td class="green-color">Client Name</td>
                <td><?= htmlspecialchars($report['client_name'] ?? 'John Doe') ?></td>
            </tr>
            <tr>
                <td class="green-color">Property Type</td>
                <td><?= htmlspecialchars($report['property_type'] ?? 'Villa') ?></td>
            </tr>
            <tr>
                <td class="green-color">Property Address</td>
                <td><?= htmlspecialchars($report['address'] ?? '123 Sample Street, City') ?></td>
            </tr>
            <tr>
                <td class="green-color">Location (Coordinates)</td>
                <td><?= htmlspecialchars($report['location_coordinates'] ?? '24.4065,54.4699') ?></td>
            </tr>
            <tr>
                <td class="green-color">Property Description</td>
                <td><?= htmlspecialchars($report['property_description'] ?? '5-bedroom villa') ?></td>
            </tr>
            <tr>
                <td class="green-color">Property Occupancy</td>
                <td><?= htmlspecialchars($report['property_occupancy'] ?? 'Vacant') ?></td>
            </tr>
            <tr>
                <td class="green-color">Property Tenure</td>
                <td><?= htmlspecialchars($report['property_tenure'] ?? 'Freehold') ?></td>
            </tr>
            <tr>
                <td class="green-color">Property Status</td>
                <td><?= htmlspecialchars($report['property_status'] ?? 'Completed') ?></td>
            </tr>
            <tr>
                <td class="green-color">Developer</td>
                <td><?= htmlspecialchars($report['developer'] ?? 'Sample Developer') ?></td>
            </tr>
            <tr>
                <td class="green-color">Extent of Investigation</td>
                <td>As per instructions received from the bank we have not inspected the property internally or
                    externally</td>
            </tr>
            <tr>
                <td class="green-color">Property Overall Condition</td>
                <td>Assumed that the property is well maintained and offers standard finishes</td>
            </tr>
            <tr>
                <td class="green-color">Life</td>
                <td>Used Life – 12 years (approx.) as per INFORMATION PROVIDED. And anticipated Remaining Life – 28
                    years (approx.) The above-mentioned estimate of economic life assumes that the subject property
                    structure has been constructed in accordance with full planning permission, our estimation of life
                    also assumes that a regular planned property maintenance program will be implemented over the
                    lifetime of the property.</td>
            </tr>
            <tr>
                <td class="green-color">Floors</td>
                <td><?= htmlspecialchars($report['floors'] ?? 'Ground+1') ?></td>
            </tr>
            <tr>
                <td class="green-color">BUA (Sq. M.)</td>
                <td><?= htmlspecialchars($report['bua_sqm'] ?? '420.75') ?></td>
            </tr>
            <tr>
                <td class="green-color">BUA (Sq. Ft.)</td>
                <td><?= htmlspecialchars($report['bua_sqft'] ?? '4529.00') ?></td>
            </tr>
            <tr>
                <td class="green-color">Land Plot Size (Sq. M.)</td>
                <td><?= htmlspecialchars($report['land_plot_size_sqm'] ?? '717.21') ?></td>
            </tr>
        </table>
    </div>
    <div class="table-report no-page-break">
        <div class="table-report" style="margin-top: 50px;">
            <table
                style="width:100%; border:none; border-collapse:collapse; margin-bottom:20px; border-bottom:.5px #111 solid">
                <tr>
                    <td style="width:70%; vertical-align:top; text-align:left;">
                        <p style="margin:0; font-size:12px;">
                            <strong style="color:#d01f28;">Prepared For:</strong>
                            <span>National Bank of Fujairah</span>
                        </p>
                        <p style="margin:0; font-size:12px;">
                            <strong style="color:#d01f28;">Reliant Reference Number:</strong>
                            <span><?= htmlspecialchars($report['reliant_reference_number'] ?? 'N/A') ?></span>
                        </p>
                    </td>
                    <td style="width:30%; text-align:right; vertical-align:top;">
                        <img src="img/logo.png" alt="Logo" style="max-width:150px; height:auto;">
                    </td>
                </tr>
            </table>
            <table class="table-start" style="width:100%; border-collapse: collapse;">
                <tr>
                    <td class="green-color">Land Plot Size (Sq. Ft.)</td>
                    <td><?= htmlspecialchars($report['land_plot_size_sqft'] ?? '7720.00') ?></td>
                </tr>
                <tr>
                    <td class="green-color">Documents provided</td>
                    <td>Screen short of property details and loan agreement</td>
                </tr>
                <tr>
                    <td class="green-color">Basis of Value</td>
                    <td>Market Value</td>
                </tr>
                <tr>
                    <td class="green-color">Purpose of Valuation</td>
                    <td><?= htmlspecialchars($report['purpose_of_valuation'] ?? 'Secured lending') ?></td>
                </tr>
                <tr>
                    <td class="green-color">Mortgage Status</td>
                    <td>Assumed Mortgaged in Favor of National Bank of Fujairah</td>
                </tr>
                <tr>
                    <td class="green-color">Date of Valuation</td>
                    <td><?= htmlspecialchars($report['date_of_valuation'] ?? '2025-05-08') ?></td>
                </tr>
                <tr>
                    <td class="green-color">Capacity of Valuer</td>
                    <td><?= htmlspecialchars($report['capacity_of_valuer'] ?? 'Independent') ?></td>
                </tr>
                <tr>
                    <td class="green-color">Special Remarks or Assumptions</td>
                    <td>We have not inspected the villa internally or externally, we have assumed that the property is
                        vacant and offers standard finishes.</td>
                </tr>
                <tr>
                    <td class="green-color">Method of Valuation</td>
                    <td><?= htmlspecialchars($report['method_of_valuation'] ?? 'Sales Comparison Method') ?></td>
                </tr>
                <tr>
                    <td class="green-color">Transaction Range</td>
                    <td><?= htmlspecialchars($report['transaction_range'] ?? 'AED 1,122.00 – AED 1,555.00') ?></td>
                </tr>
                <tr>
                    <td class="green-color">Adopted Rate per Sq. ft. for the subject property</td>
                    <td><?= htmlspecialchars($report['adopted_rate_per_sqft'] ?? '1300.00') ?></td>
                </tr>
                <tr>
                    <td class="green-color">Market Value (Rounded) Subject to Valuation</td>
                    <td style="color: #d01f28; font-weight:700; font-size:16px;">
                        <?= htmlspecialchars($report['market_value_rounded'] ?? '5900000.00') ?>
                    </td>
                </tr>
                <tr>
                    <td class="green-color">Forced Sale Value (AED)</td>
                    <td style="color: #d01f28; font-weight:700; font-size:16px;">
                        <?= htmlspecialchars($report['forced_sale_value_aed'] ?? '5250000.00') ?>
                    </td>
                </tr>
                <tr>
                    <td class="green-color">Annual Rent (AED)</td>
                    <td style="color: #d01f28; font-weight:700; font-size:16px;">
                        <?= htmlspecialchars($report['annual_rent_aed'] ?? '240000.00') ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- VALUATION CONCLUSION -->
        <div class="page-break-before"></div>
        <div class="table-report">
            <table
                style="width:100%; border:none; border-collapse:collapse; margin-bottom:20px; border-bottom:.5px #111 solid">
                <tr>
                    <td style="width:70%; vertical-align:top; text-align:left;">
                        <p style="margin:0px; font-size:12px;">
                            <strong style="color:#d01f28;">Prepared For:</strong>
                            <span>National Bank of Fujairah</span>
                        </p>
                        <p style="margin:0px; font-size:12px;">
                            <strong style="color:#d01f28;">Reliant Reference Number:</strong>
                            <span><?= htmlspecialchars($report['reliant_reference_number'] ?? 'N/A') ?></span>
                        </p>
                    </td>
                    <td style="width:30%; text-align:right; vertical-align:top;">
                        <img src="img/logo.png" alt="Logo" style="max-width:150px; height:auto;">
                    </td>
                </tr>
            </table>
            <h1>VALUATION CONCLUSION</h1>
            <div class="valuation-section" style="margin-bottom:20px;">
                <div class="valuation-subsection" style="margin-bottom:15px;">
                    <h2 style="color:#005c7c; font-size:16px; margin-bottom:5px; font-weight:500">Valuation</h2>
                    <h3 style="color:#005c7c; font-size:14px; margin-bottom:5px; font-weight:500">Basis of Valuation
                    </h3>
                    <p style="color:#666; font-size:14px; margin:0;">
                        The valuation has been conducted in accordance with the RICS Valuation – Global Standards latest
                        edition (with effect from 31st January 2025) and relevant statement of International Valuations
                        Standards.
                    </p>
                </div>

                <div class="valuation-subsection" style="margin-bottom:15px;">
                    <h3 style="color:#005c7c; font-size:14px; margin-bottom:5px;">Market Value</h3>
                    <p style="color:#666; font-size:14px; margin:0;">
                        "The estimated amount for which an asset or liability should exchange on the valuation date
                        between
                        a willing buyer and a willing seller in an arm's length transaction after proper marketing where
                        the
                        parties had each acted knowledgeably, prudently and without compulsion."
                    </p>
                </div>

                <div class="valuation-subsection" style="margin-bottom:15px;">
                    <h3 style="color:#005c7c; font-size:14px; margin-bottom:5px; font-weight:500">Methodology</h3>
                    <p style="color:#666; font-size:14px; margin-bottom:5px;">
                        For the subject property, we have implemented the Sales Comparison Method of valuation as it is
                        considered most suitable for this type of asset class. Moreover, it is one of the five
                        established
                        valuation methodologies as stated in the RICS Valuation – Professional Standards (latest
                        Edition).
                    </p>
                    <p style="color:#666; font-size:14px; margin:0;">
                        We have implemented this methodology by comparing the subject property to a similar property.
                        Basically, this method utilizes the evidence of transactions and/or current asking prices of
                        similar
                        sites in the immediate vicinity and, if appropriate, applying some adjustments to the comparable
                        figures based on market research, discussion with independent agents and, in some cases,
                        developers
                        and/or construction companies. This information is then applied to the subject property with
                        adjustments if appropriate, with the final value being derived.
                    </p>
                    <p>The following is the recent comparable evidence of similar properties in the vicinity:</p>
                </div>
            </div>

            <?php if (!empty($report['comparable_image'])): ?>
                <img src="<?= htmlspecialchars($report['comparable_image']) ?>" alt="Comparable Table"
                    class="comparable-image" />
            <?php endif; ?>
        </div>

        <div class="page-break-before"></div>
        <div class="table-report">
            <table
                style="width:100%; border:none; border-collapse:collapse; margin-bottom:20px; border-bottom:.5px #111 solid">
                <tr>
                    <td style="width:70%; vertical-align:top; text-align:left;">
                        <p style="margin:0px; font-size:12px;">
                            <strong style="color:#d01f28;">Prepared For:</strong>
                            <span>National Bank of Fujairah</span>
                        </p>
                        <p style="margin:0px; font-size:12px">
                            <strong style="color:#d01f28;">Reliant Reference Number:</strong>
                            <span><?= htmlspecialchars($report['reliant_reference_number'] ?? 'N/A') ?></span>
                        </p>
                    </td>
                    <td style="width:30%; text-align:right; vertical-align:top;">
                        <img src="img/logo.png" alt="Logo" style="max-width:150px; height:auto;">
                    </td>
                </tr>
            </table>
            <p>From the above table of sales transactions, we understand that there is a demand in the vicinity for the
                subject property. The area in general appears to be well planned and maintained and there are added
                advantages of the good road network and quick links to other parts of Dubai and the contiguous Emirates.
            </p>
            <p><strong style="color: #d01f28;">For subject property</strong>: From the above table we observe that there
                is
                a positive demand in this vicinity. We have gathered 5 comparables (recent sales transactions) and
                compared them with the subject property to
                arrive at the best-suited Market Value. The range we have considered spans from
                <strong><?= htmlspecialchars($report['transaction_range'] ?? '') ?></strong>
                price per sq. ft., considering the state of repair of the property, and factors affecting the value,
                thus we
                come up with the elective rate of
                <strong><?= htmlspecialchars($report['adopted_rate_per_sqft'] ?? '') ?></strong> per sq. ft. keeping
                special
                considerations (above Table).
            </p>

            <h3 style="color:#005c7c; font-size:14px; margin-bottom:5px; font-weight:500">Opinion of Market Value</h3>
            <p>Based on information made available by the Client and to the best of our knowledge and ability and the
                prevailing rates are taken into consideration, having regard to the observations above, we are of the
                opinion that the Market value of the subject as of the date of valuation, is estimated (rounded) listed
                as below:</p>

            <table class="table-start" style="width:100%; border-collapse: collapse;">
                <tr>
                    <td class="green-color">Market Value (Rounded) Subject to Valuation</td>
                    <td style="color: #d01f28; font-weight:700; font-size:16px;">
                        <?= htmlspecialchars($report['market_value_rounded'] ?? '5900000.00') ?>
                        <?= htmlspecialchars($report['subject_to_valuation'] ?? ' UAE DIRHAMS FIVE MILLION NINE HUNDRED THOUSAND ONLY') ?>
                    </td>
                </tr>
                <tr>
                    <td class="green-color">Forced Sale Value in AED</td>
                    <td style="color: #d01f28; font-weight:700; font-size:16px;">
                        <?= htmlspecialchars($report['forced_sale_value_aed'] ?? '5250000.00') ?>
                    </td>
                </tr>
                <tr>
                    <td class="green-color">Annual Rent in AED</td>
                    <td style="color: #d01f28; font-weight:700; font-size:16px;">
                        <?= htmlspecialchars($report['annual_rent_aed'] ?? '240000.00') ?>
                    </td>
                </tr>
            </table>

            <h2 style="color:#005c7c; font-size:14px; margin-bottom:5px;">VALUATION UNCERTAINTY</h2>
            <p>Market instability is one of the main causes of valuation uncertainty; we believe that it is the
                probability that the negotiation of the sales should be considered in 6-12 months, as certain
                macroeconomic events cause a sudden and dramatic change in the markets.</p>
            <p>We sturdily advise that prior to any financial deal being entered into based upon this valuation, you
                obtain verification of the information within our valuation report and the validity of the stated
                assumptions we have taken into consideration.</p>
            <p>We advise the client that even as we value the assets reflecting current market conditions, there are
                certain risks that may or may not become uninsurable or accountable.</p>
            <p><strong>Note:</strong> As per the UAE Law on VAT effective since 1 January 2018, the subject property,
                its sale or lease, is subject to the Value Added Tax.</p>

            <h2 style="color:#005c7c; font-size:14px; margin-bottom:5px;">DISCLOSURE</h2>
            <p>This is a desktop opinion of value, based on the information provided by the bank. The information
                contained in this report is provided in good faith and no responsibility can be accepted for errors,
                omissions, or inaccuracies, which may become or subsequently become apparent because of inaccurate or
                incomplete information as may have been provided. Certain information in this report may therefore be
                subject to further verification. The valuation opinion is shared without any liability.</p>
        </div>

        <div class="page-break-before"></div>
        <div class="table-report">
            <table
                style="width:100%; border:none; border-collapse:collapse; margin-bottom:20px; border-bottom:.5px #111 solid">
                <tr>
                    <td style="width:70%; vertical-align:top; text-align:left;">
                        <p style="margin:0px; font-size:12px;">
                            <strong style="color:#d01f28;">Prepared For:</strong>
                            <span>National Bank of Fujairah</span>
                        </p>
                        <p style="margin:0px; font-size:12px;">
                            <strong style="color:#d01f28;">Reliant Reference Number:</strong>
                            <span><?= htmlspecialchars($report['reliant_reference_number'] ?? 'N/A') ?></span>
                        </p>
                    </td>
                    <td style="width:30%; text-align:right; vertical-align:top;">
                        <img src="img/logo.png" alt="Logo" style="max-width:150px; height:auto;">
                    </td>
                </tr>
            </table>
            <h2 style="color:#005c7c; font-size:14px; margin-bottom:5px;">CONCLUSION</h2>
            <p>This report is compiled based on the information received and to the best of our skill, knowledge and
                understanding. Should there be any matters contained within this report you wish to discuss, please do
                not hesitate to contact the undersigned.</p>
            <p>This report is issued without prejudice and personal liability.</p>

            <p style="margin-top:30px;">Yours Sincerely,</p>
            <p>For and on behalf of<br><strong style="display: inline-block; margin:10px 0">RELIANT REAL ESTATE
                    VALUATION SERVICES L.L.C.<br>(trading as Reliant
                    Surveyors)</strong></p>

            <div class="signature-section">
                <table class="signature-table">
                    <tr>
                        <td style="width:35%; text-align:left;">
                            <img src="img/abhinav-sharma.png" alt="Signature" class="signature-image">
                            <strong style="display:block; margin-bottom:5px;">ABHINAV SHARMA</strong>
                            Partner<br>
                            BBA (Banking & Insurance), AssocRICS, MCMI<br>
                            RICS Registered Valuer, RERA Valuer – 39898
                        </td>
                        <td style="width:30%; text-align:center;">
                            <img src="img/signature-logo.png" alt="Company Logo" class="signature-logo">
                        </td>
                        <td style="width:35%; text-align:left;">
                            <img src="img/amrita.png" alt="Signature" class="signature-image">
                            <strong style="display:block; margin-bottom:5px;">AMRITA CHANDHOK</strong>
                            Partner<br>
                            BCom Hons, MCMI, AssocRICS, MRPSA<br>
                            RICS Registered Valuer, ADRES valuer
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="table-report no-page-break">
        <div class="last-page" style="background-image: url('img/footer-cover-green.png');"></div>

</body>

</html>