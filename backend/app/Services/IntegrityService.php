<?php

namespace App\Services;

use App\Models\JournalEntry;

class IntegrityService
{
    public static function verifyChain()
    {
        $entries = JournalEntry::withoutGlobalScopes()
            ->orderBy('id', 'asc')
            ->get();

        $violations = [];
        $previousHash = "0000000000000000000000000000000000000000000000000000000000000000";

        foreach ($entries as $entry) {
            $calculatedHash = JournalEntry::calculateHashFor($entry, $entry->previous_hash);

            $isHashInvalid = $calculatedHash !== $entry->hash;
            $isPrevHashBroken = $entry->previous_hash !== $previousHash;

            if ($isHashInvalid || $isPrevHashBroken) {
                $violations[] = [
                    'id' => $entry->id,
                    'date' => $entry->date instanceof \Carbon\Carbon ? $entry->date->toDateString() : (string)$entry->date,
                    'description' => $entry->description,
                    'debit' => (float)$entry->debit,
                    'credit' => (float)$entry->credit,
                    'stored_hash' => $entry->hash,
                    'calculated_hash' => $calculatedHash,
                    'stored_previous_hash' => $entry->previous_hash,
                    'expected_previous_hash' => $previousHash,
                    'reason' => $isHashInvalid ? 'خۆدی داتاکان گۆڕدراون (Row Values Modified)' : 'زنجیرەی هاشەکە پچڕاوە (Chain Link Broken)'
                ];
            }

            // Move the chain forward using the stored hash
            $previousHash = $entry->hash;
        }

        return [
            'status' => empty($violations) ? 'secure' : 'tampered',
            'scanned_rows' => count($entries),
            'violations' => $violations
        ];
    }
}
