<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Agreement - Apex Capital Research</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.6;
            padding: 2cm;
            max-width: 21cm;
            margin: 0 auto;
            background: #fff;
        }
        h1 {
            text-align: center;
            font-size: 18pt;
            margin-bottom: 20px;
            text-decoration: underline;
        }
        h2 {
            font-size: 12pt;
            margin: 20px 0 10px 0;
        }
        p {
            margin: 10px 0;
            text-align: justify;
        }
        .intro {
            margin-bottom: 20px;
        }
        .parties {
            margin: 15px 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .info-table th,
        .info-table td {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: left;
        }
        .info-table th {
            background-color: #f5f5f5;
            width: 40%;
        }
        ul {
            margin: 10px 0 10px 30px;
        }
        li {
            margin: 5px 0;
        }
        .fee-details {
            margin: 15px 0;
            padding: 15px;
            border: 1px solid #ccc;
            background: #fafafa;
        }
        .fee-details p {
            margin: 5px 0;
        }
        .grievance-levels {
            margin: 15px 0;
        }
        .grievance-level {
            margin: 10px 0 10px 20px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
        .blank {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 150px;
        }
        .page-break {
            page-break-before: always;
        }
        @media print {
            body {
                padding: 0;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <h1>CLIENT AGREEMENT</h1>

    <div class="intro">
        <p>This Agreement is made on this <strong>{{ \Carbon\Carbon::parse($client->payment_date)->format('d M, Y') }}</strong></p>
    </div>

    <div class="parties">
        <p><strong>By and Between:</strong></p>
        <p><strong>KASHISH JOSHI</strong>, a SEBI-registered Research Analyst (Registration No. INH000017240), having its principal place of business at Udaipur (hereinafter referred to as the "Research Analyst" or "RA"),</p>
        <p><strong>AND</strong></p>
        <p><strong>{{ $client->client_name }}</strong>, residing at {{ $client->city }}, {{ $client->state }} (hereinafter referred to as the "Client").</p>
        <p>The "RA" and "Client" may individually be referred to as a "Party" and collectively as the "Parties".</p>
    </div>

    <h2>1. Objective</h2>
    <p>The objective of this Agreement is to set out the terms and conditions for the provision of research services by the RA to the Client in accordance with SEBI (Research Analyst) Regulations, 2014, amendments thereto and related circulars.</p>

    <h2>2. SEBI Registration and Contact Details</h2>
    <table class="info-table">
        <tr>
            <th>Particulars</th>
            <th>Details</th>
        </tr>
        <tr>
            <td>SEBI Registration Number</td>
            <td>INH000017240</td>
        </tr>
        <tr>
            <td>Registration Valid Until</td>
            <td>Perpetual</td>
        </tr>
        <tr>
            <td>Principal Officer</td>
            <td>Kashish Joshi</td>
        </tr>
        <tr>
            <td>Contact Email</td>
            <td>info@apexcapitalresearch.com</td>
        </tr>
        <tr>
            <td>Phone</td>
            <td>+919171718453</td>
        </tr>
        <tr>
            <td>Grievance Officer</td>
            <td>+917023570304</td>
        </tr>
    </table>

    <h2>3. Services Offered</h2>
    <p>The RA agrees to provide the Client with the following research-related services on a non-exclusive, recommendatory basis:</p>
    <ul>
        <li>Preparation and dissemination of research reports and recommendations pertaining to specific securities and/or market sectors;</li>
        <li>Analysis based on technical and/or fundamental methodologies;</li>
        <li>General commentary and insights on financial markets, trends, and developments;</li>
        <li>Model portfolios, thematic research reports, and other illustrative investment strategies (where applicable).</li>
    </ul>
    <p>It is expressly clarified that the services offered by the RA are limited to research and recommendation. The RA does not undertake, and shall not be deemed to offer, investment advice, portfolio management, or execution-related services in any form.</p>
    <p><strong>The RA shall never ask for the client's login credentials and OTPs for the client's Trading Account, Demat Account, and Bank Account. Never share such information with anyone including RA.</strong></p>
    <p>The Client does not by way of this agreement permit the RA to execute trades on his behalf. The Client also acknowledges that all recommendations are to be acted upon at their own discretion and risk.</p>

    <h2>4. Client Onboarding</h2>
    <p>The RA shall onboard clients only after proper due diligence and ensuring appropriateness of the services.</p>
    <p>KYC and risk acknowledgement must be completed prior to rendering services.</p>

    <div class="page-break"></div>

    <h2>5. Fee Structure</h2>
    <p>The RA fees charged for this engagement is a subscription-based fee or pay-per-report fee.</p>
    <div class="fee-details">
        <p><strong>Fee details:</strong></p>
        <p>Type: {{ $client->plan }}</p>
        <p>Amount: ₹{{ number_format($client->gross_amount,2) }} (exclusive of taxes and statutory charges)</p>
        <p>
            Duration:
            {{ \Carbon\Carbon::parse($client->service_start)->format('d M, Y') }}
            to
            {{ \Carbon\Carbon::parse($client->service_end)->format('d M, Y') }}
        </p>
    </div>
    <p>The fee charged by RA to the client will be subject to the maximum of amount prescribed by SEBI/ Research Analyst Administration and Supervisory Body (RAASB) from time to time (applicable only for Individual and HUF Clients).</p>
    <p>Fees to RA may be paid by the client through any of the specified modes like cheque, online bank transfer, UPI, etc. Cash payment is not allowed. Optionally the client can make payments through Centralized Fee Collection Mechanism (CeFCoM) managed by BSE Limited (i.e. currently recognized RAASB).</p>
    <p>In case of pre-mature termination of the RA services by either the client or the RA, the client shall be entitled to seek refund of proportionate fees only for unexpired period.</p>
    <p>The Fees agreed between the RA and the Client is not performance-based or variable in nature.</p>

    <h2>6. Terms of Service</h2>
    <p>Research services are non-exclusive and not tailored to individual client objectives unless otherwise specified.</p>
    <p>Recommendations are general in nature and clients are expected to exercise independent judgment.</p>
    <p>The Client shall not redistribute or reproduce research content without written consent from the RA.</p>
    <p>The RA undertakes to abide by the applicable regulations/ circulars/ directions specified by SEBI and RAASB from time to time in relation to disclosure and mitigation of any actual or potential conflict of interest. The RA will endeavour to promptly inform the client of any conflict of interest that may affect the services being rendered to the client.</p>
    <p>Any assured/guaranteed/fixed returns schemes or any other schemes of similar nature are prohibited by law. The RA confirms that it does not offer any scheme of this nature to the client.</p>
    <p>The Client understands that the RA cannot guarantee returns, profits, accuracy, or risk-free investments from the use of the RA's research services. All opinions, projections, estimates of the RA are based on the analysis of available data under certain assumptions as of the date of preparation/publication of research report.</p>
    <p>Any investment made based on recommendations in research reports are subject to market risks, and recommendations do not provide any assurance of returns. There is no recourse to claim any losses incurred on the investments made based on the recommendations in the research report. Any reliance placed on the research report provided by the RA shall be as per the client's own judgement and assessment of the conclusions contained in the research report.</p>
    <p>The SEBI registration, Enlistment with RAASB, and NISM certification do not guarantee the performance of the RA or assure any returns to the client.</p>
    <p>Client is required to keep contact details, including email id and mobile number/s updated with the RA at all times.</p>

    <div class="page-break"></div>

    <h2>7. Client Obligations</h2>
    <ul>
        <li>To understand the risks involved in investing in securities.</li>
        <li>To act on their own discretion while executing any trade.</li>
        <li>To read the disclaimers and disclosures included in the research reports.</li>
    </ul>

    <h2>8. Disclosures by RA</h2>
    <p>As mandated by SEBI:</p>
    <p>All relevant disclosures, including ownership of securities, personal trading activity, and conflicts of interest, will be made in research reports.</p>
    <p>The RA and its employees shall not trade contrary to their published recommendations for 30 days.</p>

    <h2>9. Grievance Redressal</h2>
    <p>In case of a grievance the client should first contact the RA using the details on its website or following contact details:</p>
    <div class="grievance-levels">
        <div class="grievance-level">
            <p><strong>Level 1:</strong></p>
            <p>Email ID: compliance@apexcapitalresearch.com</p>
            <p>Contact No.: 7023570304</p>
        </div>
        <div class="grievance-level">
            <p><strong>Level 2:</strong></p>
            <p>Email ID: info@apexcapitalresearch.com</p>
            <p>Contact No.: 9171718453</p>
        </div>
        <div class="grievance-level">
            <p><strong>Level 3:</strong></p>
            <p>Email ID: kashishjoshi49@gmail.com</p>
            <p>Contact No.: 9079096751</p>
        </div>
    </div>
    <p>If the resolution is unsatisfactory or the grievance remains unresolved over a period of 30 days the client can also lodge grievances through SEBI's SCORES platform at <strong>www.scores.sebi.gov.in</strong>.</p>
    <p>The client may also consider the Online Dispute Resolution (ODR) through the Smart ODR portal at <strong>https://smartodr.in</strong>.</p>

    <h2>10. Term and Termination</h2>
    <p>This Agreement is valid for a period unless terminated earlier by either party with 30 days' written notice.</p>
    <p>Upon termination, the RA shall cease to offer research services to the client, but will retain records as per regulations.</p>

    <div class="page-break"></div>

    <h2>11. Disclaimer</h2>
    <p>The Research Analyst is not acting as a Portfolio Manager, Investment Adviser, or Distributor.</p>
    <p>Investment in securities is subject to market risk. Past performance is not a guarantee of future returns.</p>
    <p>Recommendations are based on publicly available information and/or proprietary analysis.</p>
    <p>The RA shall not be liable for any loss arising from reliance on research, except in cases of gross negligence or willful misconduct.</p>
    <p>Clients are advised to make independent decisions and/or consult their financial advisers before acting on any recommendation.</p>
    <p>Registration granted by SEBI, and certification from NISM in no way guarantee performance of the intermediary or provide any assurance of returns to investors.</p>

    <h2>12. Jurisdiction and Governing Law</h2>
    <p>This agreement shall be governed by the laws of India. All disputes arising out of this Agreement shall be subject to the exclusive jurisdiction of courts in India.</p>

    <p style="margin-top: 30px;"><strong>IN WITNESS WHEREOF</strong>, the Parties hereto have signed this Agreement on the day, month, and year first written above.</p>

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>CLIENT</strong></p>
            <div class="signature-line">
                <p>Signature: _________________________</p>
                <p>Name: {{ $client->client_name }}</p>
            <p>Date: {{ now()->format('d M, Y') }}</p>
            <p>Place: {{ $client->city }}</p>
            </div>
        </div>
        <div class="signature-box">
            <p><strong>RESEARCH ANALYST</strong></p>
            <div class="signature-line">
                <p>Signature: _________________________</p>
                <p>Name: Kashish Joshi</p>
                <p>Date: {{ now()->format('d M, Y') }}</p>
                <p>Place: Udaipur</p>
            </div>
        </div>
    </div>
</body>
</html>
