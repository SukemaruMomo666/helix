<?php
$qrisStatic = '00020101021126570011ID.DANA.WWW011893600915382779171902098277917190303UMI51440014ID.CO.QRIS.WWW0215ID10253728215000303UMI5204490053033605802ID5910Toko Nurul6013Kab. Karawang61054137363044AC9';
$amount = "50000";

$qrisBase = substr($qrisStatic, 0, -4);
$pos6304 = strrpos($qrisStatic, '6304');
if ($pos6304 !== false) {
    $qrisWithoutCrc = substr($qrisStatic, 0, $pos6304);
} else {
    $qrisWithoutCrc = $qrisBase; // Fallback
}

$amountStr = (string)$amount;
$amountLen = str_pad(strlen($amountStr), 2, '0', STR_PAD_LEFT);
$tag54 = "54{$amountLen}{$amountStr}";

$newQrisBase = $qrisWithoutCrc . $tag54 . "6304";

$crc = 0xFFFF;
for ($i = 0; $i < strlen($newQrisBase); $i++) {
    $x = (($crc >> 8) ^ ord($newQrisBase[$i])) & 0xFF;
    $x ^= $x >> 4;
    $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ ($x)) & 0xFFFF;
}
$crcHex = strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));

$dynamicQris = $newQrisBase . $crcHex;

echo "Static QRIS: " . $qrisStatic . "\n";
echo "Dynamic QRIS: " . $dynamicQris . "\n";
