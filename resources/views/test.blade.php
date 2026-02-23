<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice - Apex Capital Research</title>
    <style>
        @page {
            margin: 40px 40px 40px 40px;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #000;
            background: #fff;
            padding: 0;
            margin: 0;
        }

        .invoice-container {
            width: 98%;
            margin: 0 auto;
            border: 2px solid #000;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        td, th {
            padding: 4px 6px;
            vertical-align: top;
        }

        /* ===== HEADER SECTION ===== */
        .header-table td {
            border-bottom: 2px solid #000;
        }

        .company-section {
            width: 30%;
            border-right: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .company-name {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .company-logo {
            width: 150px;
            max-width: 150px;
        }

        .address-section {
            width: 45%;
            border-right: 1px solid #000;
            padding: 6px 8px;
            font-size: 9px;
            line-height: 1.4;
        }

        .original-section {
            width: 25%;
            padding: 6px 8px;
            font-size: 9.5px;
        }

        .original-section .border-bottom {
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            font-weight: bold;
        }

        /* ===== GSTIN ROW ===== */
        .gstin-main-row td {
            padding: 5px 8px;
            border-bottom: 1px solid #000;
        }

        /* ===== TAX INVOICE TITLE ===== */
        .invoice-title {
            text-align: center;
            padding: 6px 0;
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 1px;
        }

        /* ===== INVOICE DETAILS ===== */
        .invoice-details td {
            padding: 3px 8px;
            border-bottom: 1px solid #ccc;
        }

        .invoice-details .solid-border td {
            border-bottom: 1px solid #000;
        }

        /* ===== BILL TO PARTY ===== */
        .bill-to-party td {
            padding: 3px 8px;
        }

        .bill-to-title {
            font-weight: bold;
            font-size: 11px;
            padding: 5px 8px !important;
            border-bottom: 1px solid #000 !important;
            background-color: #f5f5f5;
        }

        .bill-to-party .detail-row td {
            border-bottom: 1px solid #ddd;
        }

        .bill-to-party .last-row td {
            border-bottom: 2px solid #000;
        }

        /* ===== PRODUCT TABLE ===== */
        .product-table {
            font-size: 9.5px;
        }

        .product-table th {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            font-weight: bold;
            background-color: #f0f0f0;
            font-size: 9px;
        }

        .product-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            font-size: 9px;
        }

        .product-table .col-sno { width: 5%; }
        .product-table .col-desc { width: 22%; }
        .product-table .col-sac { width: 10%; }
        .product-table .col-duration { width: 19%; }
        .product-table .col-amount { width: 12%; }
        .product-table .col-taxable { width: 16%; }
        .product-table .col-total { width: 16%; }

        /* ===== TAX SUMMARY ===== */
        .tax-summary td {
            padding: 4px 6px;
            font-size: 9.5px;
        }

        .tax-summary .summary-row td {
            border-bottom: 1px solid #ccc;
        }

        .tax-summary .total-row td {
            border-bottom: 2px solid #000;
            font-weight: bold;
        }

        .tax-label {
            text-align: right;
            font-weight: bold;
        }

        .tax-value {
            text-align: right;
        }

        /* ===== AMOUNT IN WORDS ===== */
        .amount-words {
            padding: 5px 8px;
            border-bottom: 1px solid #000;
            font-size: 9.5px;
            font-style: italic;
        }

        /* ===== TERMS & CONDITIONS ===== */
        .terms-table td {
            border-top: 2px solid #000;
            vertical-align: top;
        }

        .terms-section {
            width: 50%;
            padding: 8px;
            border-right: 1px solid #000;
            font-size: 9px;
            line-height: 1.5;
        }

        .terms-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .terms-section p {
            margin-bottom: 2px;
        }

        /* ===== GST / SIGNATURE SECTION ===== */
        .gst-section {
            width: 50%;
            padding: 8px;
            font-size: 9.5px;
            line-height: 1.5;
        }

        .gst-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .signature-area {
            margin-top: 25px;
            text-align: right;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 150px;
            display: inline-block;
            margin-top: 30px;
            padding-top: 3px;
            text-align: center;
            font-size: 9px;
        }

        /* ===== FOOTER ===== */
        .footer {
            text-align: center;
            padding: 10px 0;
            border-top: 2px solid #000;
        }

        .footer-logo {
            width: 140px;
            max-width: 140px;
        }

        /* ===== SEPARATOR LINE ===== */
        .separator {
            border-bottom: 1px solid #000;
            height: 1px;
        }

        .separator-thick {
            border-bottom: 2px solid #000;
            height: 1px;
        }

        .bold {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bg-light {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="invoice-container">

        <!-- ===== HEADER ROW ===== -->
        <table class="header-table">
            <tr>
                <td class="company-section">
                    <p class="company-name">Apex Capital Research</p>
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('logo.jpg'))) }}" alt="Apex Capital Research" class="company-logo">
                </td>

                <td class="address-section">
                    <p style="font-weight: bold; font-size: 10px; margin-bottom: 3px;">Address</p>
                    <p>Co Habitus Co-working Space desk no. H05,</p>
                    <p>Rajlaxmi Prime Building, 5a., Nakoda Complex</p>
                    <p>Hiran Magri, Sector 4, Main Road Near HDFC</p>
                    <p>Bank., UDAIPUR, RAJASTHAN, 313002</p>
                    <br>
                    <p>E-Mail: <a href="mailto:compliance@apexcapitalresearch.com">compliance@apexcapitalresearch.com</a></p>
                    <p>Phone: <a href="tel:7023570340">7023570340</a></p>
                    <br>
                    <p style="font-weight: bold;">SEBI REG. NO – INH000017240</p>
                    <p>(RA Kashish Joshi)</p>
                </td>

                <td class="original-section">
                    <div class="border-bottom">
                        Original for Recipient
                    </div>
                </td>
            </tr>
        </table>

        <!-- ===== GSTIN ROW ===== -->
        <table>
            <tr class="gstin-main-row">
                <td style="width: 55%; border-right: 1px solid #000;"></td>
                <td>
                    <span style="font-weight: bold;">GSTIN:</span>&nbsp;&nbsp;08BLSPJ0470P2ZE
                </td>
            </tr>
        </table>

        <!-- ===== TAX INVOICE TITLE ===== -->
        <div class="invoice-title">
            Tax Invoice
        </div>

        <!-- ===== INVOICE DETAILS ===== -->
        <table class="invoice-details">
            <tr>
                <td style="width: 50%; border-right: 1px solid #ccc;">
                    <span class="bold">Invoice No:</span> {{ 'ACR' . $client->id }}
                </td>
                <td>
                    <span class="bold">Invoice Date:</span> {{ $client->payment_date ? date('d M, Y', strtotime($client->payment_date)) : date('d M, Y') }}
                </td>
            </tr>
            <tr>
                <td style="border-right: 1px solid #ccc;">
                    <span class="bold">Reverse Charge (Y/N):</span> N
                </td>
                <td>
                    <span class="bold">State:</span> Rajasthan &nbsp;&nbsp; <span class="bold">Code:</span> 08
                </td>
            </tr>
            <tr class="solid-border">
                <td colspan="2">
                    <span class="bold">SAC CODE:</span>
                </td>
            </tr>
        </table>

        <!-- ===== BILL TO PARTY ===== -->
        <table class="bill-to-party">
            <tr>
                <td colspan="4" class="bill-to-title">Bill to Party :</td>
            </tr>
            <tr class="detail-row">
                <td style="width: 15%;"><span class="bold">Client Name:</span></td>
                <td style="width: 35%;">{{ $client->client_name ?? '' }}</td>
                <td style="width: 15%;"><span class="bold">Mobile No:</span></td>
                <td style="width: 35%;">{{ $client->mobile ?? '' }}</td>
            </tr>
            <tr class="detail-row">
                <td><span class="bold">Address:</span></td>
                <td colspan="3">{{ $client->city ?? '' }}{{ $client->state ? ', ' . $client->state : '' }}</td>
            </tr>
            <tr class="detail-row">
               
                <td><span class="bold">GSTIN:</span></td>
                <td>1</td>
            </tr>
            <tr class="last-row">
                <td><span class="bold">State:</span></td>
                <td>{{ $client->state ?? 'Rajasthan' }}</td>
                <td><span class="bold">Code:</span></td>
                <td></td>
            </tr>
        </table>

        <!-- ===== PRODUCT TABLE ===== -->
        <table class="product-table">
            <thead>
                <tr>
                    <th class="col-sno">S.No.</th>
                    <th class="col-desc">Product Description</th>
                    <th class="col-sac">SAC Code</th>
                    <th class="col-duration">Duration</th>
                    <th class="col-amount">Amount</th>
                    <th class="col-taxable">Taxable Value</th>
                    <th class="col-total">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td style="text-align: left;">{{ $client->segment ?? 'Nifty/ Bank Nifty - Index Option' }}</td>
                    <td></td>
                    <td style="font-size: 8px;">
                        {{ $client->service_start ? date('d-m-Y', strtotime($client->service_start)) : '' }}
                        <br>to<br>
                        {{ $client->service_end ? date('d-m-Y', strtotime($client->service_end)) : '' }}
                    </td>
                    <td>Rs. {{ number_format($client->net_amount ?? 0, 0) }}/-</td>
                    <td>Rs. {{ number_format(($client->gross_amount ?? 0) - ($client->net_amount ?? 0), 0) }}/-</td>
                    <td style="font-weight;">Rs. {{ number_format($client->gross_amount ?? 0, 0) }}/-</td>
                </tr>
            </tbody>
        </table>

        <!-- ===== TAX SUMMARY ===== -->
        <table class="tax-summary">
            <tr class="summary-row">
                <td style="width: 50%;"></td>
                <td style="width: 15%;"></td>
                <td style="width: 20%;" class="tax-label">Add: CGST</td>
                <td style="width: 15%;" class="tax-value">NA</td>
            </tr>
            <tr class="summary-row">
                <td></td>
                <td class="bold">Rec. Pmt. Details</td>
                <td class="tax-label">Add: SGST</td>
                <td class="tax-value">NA</td>
            </tr>
            <tr class="summary-row">
                <td></td>
                <td></td>
                <td class="tax-label">Add: IGST</td>
                <td class="tax-value bold">Rs. {{ number_format(($client->gross_amount ?? 0) - ($client->net_amount ?? 0), 0) }}/-</td>
            </tr>
            <tr class="summary-row">
                <td></td>
                <td></td>
                <td class="tax-label">Total GST</td>
                <td class="tax-value bold">Rs. {{ number_format(($client->gross_amount ?? 0) - ($client->net_amount ?? 0), 0) }}/-</td>
            </tr>
            <tr class="total-row">
                <td></td>
                <td></td>
                <td class="tax-label" style="font-size: 10px;">Total Amount<br>after Tax:</td>
                <td class="tax-value" style="font-size: 10px;">Rs. {{ number_format($client->gross_amount ?? 0, 0) }}/-</td>
            </tr>
        </table>

        <!-- ===== AMOUNT IN WORDS ===== -->
        @php
            $grossAmount = (int) ($client->gross_amount ?? 0);
            // Simple number to words function - check if already exists
            if (!function_exists('numberToWordsIndian')) {
                function numberToWordsIndian($num) {
                    if ($num == 0) return 'Zero';
                    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
                             'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                             'Seventeen', 'Eighteen', 'Nineteen'];
                    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
                    
                    if ($num < 20) return $ones[$num];
                    if ($num < 100) return $tens[intval($num / 10)] . ($num % 10 ? ' ' . $ones[$num % 10] : '');
                    if ($num < 1000) return $ones[intval($num / 100)] . ' Hundred' . ($num % 100 ? ' ' . numberToWordsIndian($num % 100) : '');
                    if ($num < 100000) return numberToWordsIndian(intval($num / 1000)) . ' Thousand' . ($num % 1000 ? ' ' . numberToWordsIndian($num % 1000) : '');
                    if ($num < 10000000) return numberToWordsIndian(intval($num / 100000)) . ' Lakh' . ($num % 100000 ? ' ' . numberToWordsIndian($num % 100000) : '');
                    return numberToWordsIndian(intval($num / 10000000)) . ' Crore' . ($num % 10000000 ? ' ' . numberToWordsIndian($num % 10000000) : '');
                }
            }
            $amountInWords = numberToWordsIndian($grossAmount);
        @endphp
        <div class="amount-words">
            <span class="bold">Amount in Words:</span> Rupees {{ $amountInWords }} Only
        </div>

        <!-- ===== TERMS & CONDITIONS + GST SECTION ===== -->
        <table class="terms-table">
            <tr>
                <td class="terms-section">
                    <p class="terms-title">Terms & Conditions</p>
                    <p>• This is a Computer Generated Invoice, No Signature & Stamp Required.</p>
                    <p>• Investment / Trading in Market is Subject to Market Risk.</p>
                    <p>• IF YOU HAVE ANY QUESTIONS CONCERNING THIS INVOICE:</p>
                    <p>&nbsp;&nbsp;E-Mail: <a href="mailto:compliance@apexcapitalresearch.com">compliance@apexcapitalresearch.com</a></p>
                    <p>&nbsp;&nbsp;Phone: <a href="tel:7023570340">7023570340</a></p>
                    <br>
                    <p style="font-weight: bold; text-decoration: underline;">DISCLAIMER:</p>
                    <p>MAKE SURE YOU HAVE GONE THROUGH THE DISCLAIMER, PRIVACY  </p>
                    <p>POLICY, TERMS AND CONDITIONS AND REFUND POLICY ON</p>
                    <p><a href="https://apexcapitalresearch.com" target="_blank">https://apexcapitalresearch.com</a> BEFORE PROCEEDING FOR SUBSCRIPTION OR MAKING PAYMENT</p>
                </td>

                <td class="gst-section">
                    <p class="gst-title">GST Reverse Charge</p>
                    <p>Certified that the particulars given above are true and correct.</p>

                    <div class="signature-area">
                        <p style="margin-top: 8px; font-weight: bold;">For Apex Capital Research</p>
                        <br><br><br>
                        <div class="signature-line">
                            Authorised Signatory
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- ===== FOOTER ===== -->
        <div class="footer">
            <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('logo.jpg'))) }}" alt="Apex Capital Research" class="footer-logo">
        </div>
    </div>
</body>
</html>
