<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Client;
use App\Services\InvoiceWordService;
use App\Services\AgreementWordService;

echo "Running Word generation test...\n";

$client = Client::first();
if (! $client) {
    echo "No client found — creating sample client...\n";
    $client = Client::create([
        'payment_date' => date('Y-m-d'),
        'client_name' => 'Test Client',
        'mobile' => '9999999999',
        'email' => 'test@example.com',
        'city' => 'Udaipur',
        'state' => 'Rajasthan',
        'gross_amount' => 1000.00,
        'net_amount' => 900.00,
        'amount_type' => 'New Enrollment',
        'segment' => 'Test Segment',
        'plan' => 'Basic',
        'service_start' => date('Y-m-d'),
        'service_end' => date('Y-m-d', strtotime('+1 year')),
    ]);
    echo "Sample client created with ID: {$client->id}\n";
} else {
    echo "Using existing client ID: {$client->id}\n";
}

$invoiceService = $app->make(InvoiceWordService::class);
$agreementService = $app->make(AgreementWordService::class);

try {
    $invoicePath = $invoiceService->generate($client);
    echo "Invoice generated: {$invoicePath}\n";
} catch (Throwable $e) {
    echo "Invoice generation failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

try {
    $agreementPath = $agreementService->generate($client);
    echo "Agreement generated: {$agreementPath}\n";
} catch (Throwable $e) {
    echo "Agreement generation failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "Done.\n";
