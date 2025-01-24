<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $uploadDir = '/var/www/html/pdf/uploads';
    $filename = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];

    // Fayl nomini 50 ta belgidan ortiq bo'lmasligini ta'minlash
    $filename = substr($filename, 0, 50);
    $filename = preg_replace('/[^A-Za-z0-9\-\.]/', '_', $filename); // kengaytmalarni tozalash

    // Faylni tanlash va saqlash
    $uploadFile = $uploadDir . '/' . basename($filename);

    // Faylni yuklash
    if (move_uploaded_file($fileTmpName, $uploadFile)) {
        // Faylning kengaytmasini tekshirish
        $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($fileExt, ['docx', 'doc', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods'])) {
            echo "Faqat Office fayllari yoki Open Document fayllarini yuklash mumkin.";
            exit;
        }

        // Faylni PDF formatiga o'zgartirish (unoconv yoki libreoffice)
        $outputFile = $uploadDir . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.pdf';

        // unoconv orqali faylni PDF ga o'zgartirish
        $command = "unoconv -f pdf -o $outputFile $uploadFile";
        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            echo "Xatolik yuz berdi: " . implode("\n", $output);
        } else {
            echo "Fayl muvaffaqiyatli PDF formatiga o'zgartirildi. <a href='/pdf/uploads/" . basename($outputFile) . "'>Yangi faylni yuklab olish</a>";

            // Yangi PDF faylni ko'rsatish
            // Faylni o'chirish
            unlink($uploadFile);
        }
    } else {
        echo "Faylni yuklashda xatolik yuz berdi.";
    }
} else {
    echo "Fayl tanlanmagan.";
}
?>

<!-- HTML forma fayl tanlash va yuborish uchun -->
<form action="convert_to_pdf.php" method="POST" enctype="multipart/form-data">
    <label for="file">Faylni tanlang (Office yoki Open Document formatlari):</label>
    <input type="file" name="file" id="file" required>
    <button type="submit">Faylni PDF ga konvertatsiya qilish</button>
</form>

<!-- Asosiy sahifaga qaytish tugmasi -->
<br><a href="index.html"><button>Asosiy sahifaga qaytish</button></a>
