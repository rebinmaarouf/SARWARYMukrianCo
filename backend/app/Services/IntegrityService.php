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
                $reason = '';
                $riskLevel = 'critical';
                
                if ($isHashInvalid && $isPrevHashBroken) {
                    $reason = 'دەستکاریکردنی توند: بڕی پارە، سندوق، یان زانیارییە سەرەکییەکانی ئەم مامەڵەیە گۆڕدراون لە دەرەوەی سیستم!';
                } elseif ($isHashInvalid) {
                    $reason = 'زانیاری گۆڕدراو: ناوەڕۆکی ئەم مامەڵەیە (وەسف، بڕی پارە، یان ڕێکەوت) ڕاستەوخۆ لە دەرەوەی سیستم دەستکاری کراوە!';
                } else {
                    $reason = 'پچڕانی زنجیرە: مامەڵەی پێش ئەم دێڕە سڕدراوەتەوە یان دەستکاری کراوە کە زنجیرەی هاوسەنگی شکاندووە!';
                }

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
                    'reason' => $reason,
                    'risk_level' => $riskLevel
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
