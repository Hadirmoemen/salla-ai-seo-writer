<?php
// SallaSEO.php

class SallaSEO 
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * استخراج الـ Access Token الحقيقي في حال تم إرسال JWT أو التوكن الكامل من رابط سلة
     */
    private function getCleanToken(): string
    {
        $rawToken = trim($this->config['salla']['access_token'] ?? '');

        // لو التوكن جلي بصيغة JWT المشفرة (Base64 JSON) نحاول نفكه
        $decoded = json_decode(base64_decode($rawToken), true);
        if (is_array($decoded) && isset($decoded['value'])) {
            // فك التشفير الجزئي لو محتاج
            return $rawToken; 
        }

        return $rawToken;
    }

    /**
     * 1. إرسال اسم المنتج للـ AI لتوليد وصف SEO احترافي
/**
     * 1. إرسال اسم المنتج للـ AI لتوليد وصف SEO احترافي
     */
   /**
     * 1. إرسال اسم المنتج للـ AI لتوليد وصف SEO احترافي
     */
  
    /**
     * 2. تحديث وصف المنتج في سلة عبر REST API
     */
    /**
     * 1. إرسال اسم المنتج لـ Groq AI لتوليد وصف SEO احترافي
     */
    public function generateSeoDescription(string $productName): string
    {
        $apiKey = $this->config['groq']['api_key'] ?? '';
        $url = "https://api.groq.com/openai/v1/chat/completions";

        $prompt = "أنت خبير SEO وتسويق إلكتروني للمتاجر السعودية. " .
                  "اكتب وصفاً تسويقياً جذاباً لمنتج باسم: '{$productName}'. " .
                  "الشروط: " .
                  "1. بأسلوب جذاب ولهجة بيضاء مناسبة للسوق السعودي. " .
                  "2. يحتوي على فقرة تعريفية، قائمة بالفوائد أو المميزات، ونقاط استخدام. " .
                  "3. محسن لمحركات البحث (SEO) بكلمات مفتاحية ذات صلة. " .
                  "4. أرجع النص النهائي بصيغة HTML (استخدم <p>, <ul>, <li>, <strong>) بدون أي مقدمات أو شرح آخر.";

        $payload = json_encode([
            'model' => 'llama-3.3-70b-versatile', // أحدث وأقوى موديل مجاني في Groq
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($httpCode !== 200 || empty($data['choices'][0]['message']['content'])) {
            $errorMsg = $data['error']['message'] ?? 'حدث خطأ أثناء الاتصال بـ Groq';
            echo "&nbsp;&nbsp;<small style='color:red;'>[Groq Error {$httpCode}: {$errorMsg}]</small><br>";
            return '';
        }

        return $data['choices'][0]['message']['content'];
    }
/**
     * 2. تحديث وصف المنتج في سلة عبر الـ API
     */
   /**
     * 2. تحديث وصف المنتج في سلة عبر الـ API
     */
    public function updateProductDescription($productId, string $newDescription): bool
    {
        $url = rtrim($this->config['salla']['api_url'], '/') . '/products/' . $productId;

        $payload = json_encode([
            'description' => $newDescription
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->getCleanToken(),
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            echo "&nbsp;&nbsp;<small style='color:red;'>[Salla API Error Code {$httpCode}: {$response}]</small><br>";
            return false;
        }

        return true;
    }
    /**
     * 3. جلب جميع منتجات المتجر مع كشف أخطاء الاتصال
     */
   /**
 * 3. جلب جميع منتجات المتجر (تتخطى الـ 20 منتج وتجلب كل الصفحات)
 */
public function getProducts(): array
{
    $allProducts = [];
    $page = 1;

    do {
        $url = rtrim($this->config['salla']['api_url'], '/') . '/products?page=' . $page . '&per_page=50';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->getCleanToken(),
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            break;
        }

        $data = json_decode($response, true);
        $products = $data['data'] ?? [];

        // دمج منتجات الصفحة الحالية مع القائمة الكلية
        $allProducts = array_merge($allProducts, $products);

        // معرفة هل توجد صفحات أخرى أم انتهت المنتجات
        $currentPage = $data['pagination']['currentPage'] ?? $page;
        $totalPages  = $data['pagination']['totalPages'] ?? $page;

        $page++;

    } while ($currentPage < $totalPages);

    return $allProducts;
}
}