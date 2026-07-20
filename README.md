# 🚀 Salla AI SEO Description Generator

أداة أوتوماتيكية متكاملة بلغة **PHP** مخصصة لمتجر **سلة (Salla)** لتوليد وتحسين أوصاف المنتجات المخصصة لمحركات البحث (SEO) باستخدام **Groq AI (Llama 3.3)**.

An automated PHP script integrated with Salla REST API that scans products, checks description lengths, and auto-generates SEO-optimized descriptions using **Groq AI** (`llama-3.3-70b-versatile`).

---

## ✨ المميزات | Features

* **ربط مباشر مع Salla API:** سحب المنتجات وتحديث الأوصاف أوتوماتيكياً.
* **فحص ذكي للـ SEO:** التخطي التلقائي للمنتجات التي تمتلك وصفاً كافياً وتحديث المنتجات القصيرة فقط.
* **سرعة فائقة (Groq AI):** الاعتماد على محرك Groq السريع والمجاني لتفادي مشكلات Rate Limits.
* **صياغة تسويقية ممتازة:** توليد أوصاف HTML منسقة تحوي فقرات تسويقية ومميزات للمنتجات موجهة للسوق السعودي.
* **حماية البيانات الحساسة:** حماية الـ Access Tokens ومفاتيح الـ API بواسطة ملف `.gitignore`.

---

## 🛠️ متطلبات التشغيل | Prerequisites

* **PHP:** v7.4 or higher
* **cURL Extension:** Enabled
* **Salla App Access Token** (مع صلاحيات `products.read` و `products.write`)
* **Groq API Key** (مجاني من [Groq Console](https://console.groq.com/))

---

## 📂 هيكل المشروع | Project Structure

```text
├── config.example.php     # نموذج لملف الإعدادات
├── config.php             # ملف الإعدادات الحقيقي (غير مرفوع)
├── SallaSEO.php           # Class الأساسي لإدارة الاتصال بـ Salla و Groq API
├── auto_write.php         # السكربت الرئيسي لتشغيل عملية التحديث أوتوماتيكياً
├── .gitignore             # منع رفع الملفات الحساسة
└── README.md              # توثيق المشروع