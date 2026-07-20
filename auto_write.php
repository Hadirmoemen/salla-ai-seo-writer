<?php
// auto_write.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// لضمان استمرار السكربت وعدم التوقف لو المنتجات كثيرة
set_time_limit(0);

require_once __DIR__ . '/SallaSEO.php';
$config = require __DIR__ . '/config.php';

$seoService = new SallaSEO($config);

echo "--- Starting AI SEO Auto-Writer ---<br><br>";

$products = $seoService->getProducts();

// 1. التحقق من وصول منتجات من Salla API
if (empty($products) || !is_array($products)) {
    echo "<strong style='color:red;'>⚠️ لم يتم الجلب: القائمة فارغة أو حدث خطأ أثناء الاتصال بـ Salla API.</strong><br>";
    echo "--- AI SEO Process Completed ---<br>";
    exit;
}

echo "✅ تم سحب <strong>" . count($products) . "</strong> منتج من المتجر.<br><br>";

$updatedCount = 0;

foreach ($products as $product) {
    $id   = $product['id'] ?? null;
    $name = $product['name'] ?? 'منتج بدون اسم';
    $desc = strip_tags($product['description'] ?? '');

    echo "🔍 فحص المنتج: <strong>{$name}</strong> (طول الوصف الحالي: " . mb_strlen($desc) . " حرف)<br>";

    // إيجاد المنتجات التي وصفها أقل من 50 حرف فقط
    if (mb_strlen($desc) < 200) {
        echo "&nbsp;&nbsp;⚡ جاري توليد وصف SEO بالذكاء الاصطناعي...<br>";

        // 1. توليد النص بالذكاء الاصطناعي
        $aiDescription = $seoService->generateSeoDescription($name);

        if (!empty($aiDescription)) {
            // 2. تحديث المنتج في سلة
            $updated = $seoService->updateProductDescription($id, $aiDescription);

            if ($updated) {
                echo "&nbsp;&nbsp;<span style='color:green;'>SUCCESS: تم تحديث المنتج بنجاح!</span><br><br>";
                $updatedCount++;
            } else {
                echo "&nbsp;&nbsp;<span style='color:red;'>FAILED: تعذر تحديث المنتج على سلة.</span><br><br>";
            }
        } else {
            echo "&nbsp;&nbsp;<span style='color:orange;'>WARNING: لم يتم كتابة وصف بواسطة Gemini.</span><br><br>";
        }

       // إذا رجع 429 (Rate Limit) ينتظر 10 ثواني ويجرب تاني تلقائياً
if ($httpCode === 429) {
    sleep(10);
    // إعادة الطلب مرة أخرى
    $response = curl_exec($ch);
    $data = json_decode($response, true);
}
    } else {
        echo "&nbsp;&nbsp;⏭️ تم تخطي المنتج لأن وصفه الحالي مكتمل وسليم.<br><br>";
    }
}

echo "--------------------------------------------------<br>";
echo "🎉 <strong>النتيجة النهائية:</strong> تم تحديث <strong>{$updatedCount}</strong> منتج بنجاح.<br>";
echo "--- AI SEO Process Completed ---<br>";