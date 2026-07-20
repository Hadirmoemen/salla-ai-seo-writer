<?php
// webhook.php

// 1. استقبال البيانات القادمة من سلة (JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// 2. التحقق من حدث منح الصلاحيات
if (isset($data['event']) && $data['event'] === 'app.store.authorize') {
    $accessToken = $data['data']['access_token'] ?? '';
    
    if (!empty($accessToken)) {
        // حفظ التوكن في ملف نصي بسيط لمراجعته ونسخه
        file_put_contents('token.txt', $accessToken);
    }
}

// الرد على سلة بكود 200 لتأكيد الاستلام
http_response_code(200);
echo json_encode(['success' => true]);