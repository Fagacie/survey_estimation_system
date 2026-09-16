<?php

namespace App\Services;

use App\Models\Invoice;

class InvoiceNumberGenerator
{
    /**
     * Generate the next invoice number for the current year.
     *
     * Format: EHS/FIN/SW-INV/{YEAR}/{SEQUENCE}
     * Example: EHS/FIN/SW-INV/2026/001
     */
    public static function generate(): string
    {
        $year = date('Y');
        $prefix = "EHS/FIN/SW-INV/{$year}/";

        // Find the latest invoice for this year
        $latest = Invoice::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('invoice_number')
            ->first();

        if ($latest) {
            // Extract the sequence number from the end
            $lastSequence = (int) substr($latest->invoice_number, -3);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }

        return $prefix . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
    }
}
