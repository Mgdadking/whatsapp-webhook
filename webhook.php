<?php
// 1. استلام البيانات JSON من ultramsg
$data = json_decode(file_get_contents("php://input"), true);

// 2. استخراج رقم المرسل والرسالة
$from = $data['from'] ?? '';
$message = $data['body'] ?? '';

// 3. التأكد إن الرسالة جاية من شخص
if (!empty($from) && !empty($message)) {

    // 4. قائمة الروابط اللي حنرد بيها حسب الدور
    $numbers = [
        "https://api.whatsapp.com/send?phone=%2B201029664170",
        "https://api.whatsapp.com/send?phone=%2B201029773000",
        "https://api.whatsapp.com/send?phone=%2B201029772000",
        "https://api.whatsapp.com/send?phone=%2B201055855040",
        "https://api.whatsapp.com/send?phone=%2B201029455000",
        "https://api.whatsapp.com/send?phone=%2B201027480870",
        "https://api.whatsapp.com/send?phone=%2B201055855030"
    ];

    // 5. تحديد موقع ملف العداد
    $counterFile = "counter.txt";

    // 6. قراءة القيمة الحالية من الملف (إذا الملف موجود)
    $index = file_exists($counterFile) ? intval(file_get_contents($counterFile)) : 0;

    // 7. اختيار الرابط المناسب بناءً على العداد
    $link = $numbers[$index % count($numbers)];

    // 8. تحديث العداد +1 (عشان المرة الجاية ينتقل للرابط التالي)
    file_put_contents($counterFile, $index + 1);

    // 9. نص الرسالة اللي حترسل للزبون
    $reply = "مرحبًا 👋\nيرجى التواصل معنا على الرقم التالي:\n$link";

    // 10. بيانات الاتصال بـ ultramsg
    $instance = "YOUR_INSTANCE_ID";  // ← استبدلها
    $token = "YOUR_INSTANCE_TOKEN";  // ← استبدلها

    // 11. إرسال الرد باستخدام CURL
    $payload = json_encode([
        "to" => $from,
        "body" => $reply
    ]);

    $url = "https://api.ultramsg.com/$instance/messages/chat";
    $headers = [
        "Content-Type: application/json",
        "token: $token"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
?>
