<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CloseoutBalancesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $date;
    public $vaults;
    public $ownerName;

    public function __construct($date, $vaults, $ownerName = 'بەڕێز')
    {
        $this->date = $date;
        $this->vaults = $vaults;
        $this->ownerName = $ownerName;
    }

    public function build()
    {
        return $this->subject('📊 ڕاپۆرتی کۆتایی ڕۆژ - باڵانسی سندوقەکان (' . $this->date . ')')
                    ->html($this->renderHtmlContent());
    }

    private function renderHtmlContent()
    {
        $rows = '';
        foreach ($this->vaults as $v) {
            $formattedBalance = number_format($v->balance, 2);
            $rows .= "
            <tr style='border-bottom: 1px solid #f1f5f9;'>
                <td style='padding: 12px; font-weight: bold; color: #1e293b; text-align: right;'>{$v->name}</td>
                <td style='padding: 12px; font-weight: bold; color: #64748b; text-align: right;'>{$v->currency_code}</td>
                <td style='padding: 12px; font-weight: 900; color: #0f172a; text-align: left;'>{$formattedBalance}</td>
            </tr>";
        }

        return "
        <div dir='rtl' style=\"font-family: 'Inter', system-ui, sans-serif; background-color: #f8fafc; padding: 40px 20px; line-height: 1.6; text-align: right;\">
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #e2e8f0;'>
                <!-- Header -->
                <div style='background: linear-gradient(135deg, #050505, #1e293b); padding: 40px 30px; text-align: center; color: #ffffff;'>
                    <h1 style='margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -0.5px;'>ڕاپۆرتی کۆتایی ڕۆژ</h1>
                    <p style='margin: 5px 0 0 0; font-size: 13px; color: #94a3b8; font-weight: bold;'>کۆمپانیای سەروەری موکریان / لقی سەرەکی</p>
                </div>

                <!-- Body -->
                <div style='padding: 40px 30px;'>
                    <p style='font-size: 16px; color: #334155; margin-bottom: 25px;'>سڵاو <strong>{$this->ownerName}</strong> گیان،</p>
                    <p style='font-size: 14px; color: #475569; margin-bottom: 30px;'>لە خوارەوە ڕاپۆرتی فەرمی و دەقیقی باڵانسی کۆتایی ڕۆژی سندوقەکان دەخەینەڕوو بۆ ئەمڕۆ بەرواری <strong>{$this->date}</strong>:</p>

                    <!-- Balances Table -->
                    <table style='width: 100%; border-collapse: collapse; margin-bottom: 30px; text-align: right;'>
                        <thead>
                            <tr style='background-color: #f1f5f9; border-bottom: 2px solid #cbd5e1;'>
                                <th style='padding: 12px; font-size: 12px; font-weight: 900; color: #475569; text-align: right;'>سندوق</th>
                                <th style='padding: 12px; font-size: 12px; font-weight: 900; color: #475569; text-align: right;'>دراو</th>
                                <th style='padding: 12px; font-size: 12px; font-weight: 900; color: #475569; text-align: left;'>باڵانس</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$rows}
                        </tbody>
                    </table>

                    <div style='background-color: #f8fafc; border-right: 4px solid #10b981; padding: 20px; border-radius: 12px; margin-bottom: 30px;'>
                        <p style='margin: 0; font-size: 12px; color: #475569; font-weight: bold;'>✨ سەرجەم باڵانسەکان بە تەواوی ئەرشیف کراون و هاوسەنگی داتابەیس کریپتۆگرافییانە پارێزراوە.</p>
                    </div>

                    <p style='font-size: 11px; color: #94a3b8; text-align: center; margin: 40px 0 0 0; border-top: 1px solid #f1f5f9; padding-top: 20px;'>هەموو مافەکان پارێزراوە بۆ کۆمپانیای سەروەری موکریان © " . date('Y') . "</p>
                </div>
            </div>
        </div>";
    }
}
