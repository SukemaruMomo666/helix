<?php
date_default_timezone_set('Asia/Jakarta');

$items = [
    ['name' => 'Akar kelapa (1kg)', 'price' => 50000],
    ['name' => 'Kembang goyang (isi 100)', 'price' => 50000],
    ['name' => 'Kiripik pisang (1kg)', 'price' => 50000],
    ['name' => 'Opak (isi 100)', 'price' => 50000],
    ['name' => 'Pungpa (1kg)', 'price' => 50000],
    ['name' => 'rengginang (isi 100)', 'price' => 80000],
    ['name' => 'Sistik Asli (1kg)', 'price' => 50000],
];

$receiptsHtml = '';

for ($day = 1; $day <= 20; $day++) {
    // Determine how many buyers today (0 to 3, mostly 1)
    $randBuyer = rand(1, 100);
    if ($randBuyer <= 10) {
        $numBuyers = 0; // 10% chance no buyer
    } elseif ($randBuyer <= 70) {
        $numBuyers = 1; // 60% chance 1 buyer
    } elseif ($randBuyer <= 90) {
        $numBuyers = 2; // 20% chance 2 buyers
    } else {
        $numBuyers = 3; // 10% chance 3 buyers
    }

    for ($b = 0; $b < $numBuyers; $b++) {
        // Generate random time for the transaction
        $hour = rand(8, 21);
        $minute = rand(0, 59);
        $dateStr = sprintf("2026-08-%02d %02d:%02d:00", $day, $hour, $minute);
        $dateObj = new DateTime($dateStr);
        $formattedDate = $dateObj->format('d/m/Y H:i');
        
        $invoiceCode = "POS-OPA-" . $dateObj->format('dmyHis') . rand(10, 99);

        // Determine how many items this buyer buys (1 to 3 items)
        $numItems = rand(1, 3);
        $boughtItems = [];
        $total = 0;
        
        // Pick random items
        $shuffled = $items;
        shuffle($shuffled);
        
        for ($i = 0; $i < $numItems; $i++) {
            $qty = rand(1, 2); // usually 1 or 2 of the same item
            $item = $shuffled[$i];
            $subtotal = $item['price'] * $qty;
            $total += $subtotal;
            $boughtItems[] = [
                'name' => $item['name'],
                'price' => $item['price'],
                'qty' => $qty,
                'subtotal' => $subtotal
            ];
        }

        // Hitung tunai & kembalian
        $tunai = $total;
        if (rand(1, 100) > 50) { // 50% chance of paying more than total
            $remainder = $total % 50000;
            if ($remainder > 0) {
                $tunai = $total + (50000 - $remainder); 
            } else {
                $tunai = $total + 50000;
            }
            if (rand(1, 100) > 70 && $total <= 50000) {
                $tunai = 100000;
            }
        }
        $kembali = $tunai - $total;

        // Generate receipt HTML
        $itemsHtml = '';
        foreach ($boughtItems as $bi) {
            $itemsHtml .= '
        <tr>
            <td colspan="2" class="font-bold">'.$bi['name'].'</td>
        </tr>
        <tr>
            <td width="60%">'.$bi['qty'].' x '.number_format($bi['price'], 0, ',', '.').'</td>
            <td width="40%" class="text-right">'.number_format($bi['subtotal'], 0, ',', '.').'</td>
        </tr>';
        }

        $receiptHtml = '
    <div class="receipt">
        <div class="divider" style="border-top: 2px solid #000; margin-bottom: 5px;"></div>
        <div class="text-center">
            <div class="store-name" style="font-size: 16px;">SADAWARNA SMART CENTER</div>
            <div class="store-info" style="font-size: 11px;">
                Pusat Penjualan UMKM Desa Sadawarna<br>
                Kec. Cibogo, Kab. Subang
            </div>
        </div>
        <div class="divider" style="border-top: 1px solid #000; margin-top: 5px;"></div>
        
        <div class="text-center" style="margin-top: 10px;">
            <div class="store-name" style="font-size: 14px;">OPAK MAK IYOS</div>
            <div class="store-info">
                Dusun Dukuh Sadawarna<br>
                Telp: 81318218169
            </div>
        </div>
        
        <div class="divider"></div>
        
        <div class="invoice-info">
            <table style="width: 100%;">
                <tr>
                    <td width="30%">No. Struk</td>
                    <td width="5%">:</td>
                    <td>'.$invoiceCode.'</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td>'.$formattedDate.'</td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td>:</td>
                    <td>Prabu Alam Tian Try Suherman</td>
                </tr>
            </table>
        </div>
        
        <div class="divider"></div>
        
        <table class="table-items">'.$itemsHtml.'
        </table>
        
        <div class="divider"></div>
        
        <table class="table-items">
            <tr>
                <td class="font-bold">TOTAL TAGIHAN</td>
                <td class="text-right font-bold">Rp '.number_format($total, 0, ',', '.').'</td>
            </tr>
            <tr>
                <td>TUNAI</td>
                <td class="text-right">Rp '.number_format($tunai, 0, ',', '.').'</td>
            </tr>
            <tr style="font-size: 12px;">
                <td class="font-bold">KEMBALIAN</td>
                <td class="text-right font-bold">Rp '.number_format($kembali, 0, ',', '.').'</td>
            </tr>
        </table>

        <div class="divider"></div>
        
        <div class="text-center" style="margin-top: 15px; font-size: 9px; line-height: 1.4;">
            *** TERIMA KASIH ***<br>
            Barang yang sudah dibeli tidak dapat<br>
            ditukar atau dikembalikan.<br>
            <br>
            Powered by Helix
        </div>
    </div>';
        $receiptsHtml .= $receiptHtml;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kumpulan Struk 1-20 Agustus</title>
    <style>
        @page { margin: 0; size: 80mm auto; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            font-weight: 600; /* Ditebalkan agar lebih jelas di printer thermal */
            color: #000;
            margin: 0;
            padding: 0;
            background: #e0e0e0; /* Gray background outside */
            box-sizing: border-box;
        }
        .container {
            width: 70mm;
            margin: 0 auto;
            background: #fff;
        }
        .receipt {
            padding: 5px;
            padding-bottom: 20px;
            background: #fff;
            border-bottom: 2px dashed #999;
            page-break-after: always;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .table-items { width: 100%; border-collapse: collapse; margin: 5px 0; }
        .table-items td { padding: 2px 0; vertical-align: top; }
        .store-name { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .store-info { font-size: 10px; margin-bottom: 5px; line-height: 1.2; }
        .invoice-info { font-size: 10px; margin-bottom: 5px; }
        
        @media print {
            body { 
                width: 70mm; 
                margin: 0; 
                padding: 0; 
                background: #fff;
            }
            .container {
                width: 100%;
                margin: 0;
            }
            .no-print { display: none; }
            .receipt { 
                page-break-after: always; 
                border-bottom: none;
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <?= $receiptsHtml ?>
    </div>
</body>
</html>
