<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $uploadDir = '/var/www/html/pdf/uploads';
    $filename = $_FILES['file']['name'];

    // Fayl nomini 50 ta belgidan ortiq bo'lmasligini ta'minlash
    $filename = substr($filename, 0, 50); // 50 ta belgidan ortiq nomni kesish

    // Maxsus belgilarni va bo'shliqlarni tozalash
    $filename = preg_replace('/[^A-Za-z0-9\-]/', '_', $filename); // faqat raqam, harf, va tirnoq belgilari qoladi

    // Fayl kengaytmasini olish
    $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    // Fayl kengaytmasini .pdf ga o'zgartirish
    if ($fileExt != 'pdf') {
        $filename = preg_replace('/\.[^.]+$/', '', $filename); // Kengaytmani olib tashlash
        $filename .= '.pdf'; // Yangi kengaytmada .pdf qo'shish
    }

    $uploadFile = $uploadDir . basename($filename);

    // Faylni yuklash
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        // Kichraytirish
        $outputFile = $uploadDir . 'compressed_' . basename($filename);
        $command = "gswin64c -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$outputFile $uploadFile";
        
        // Komandani bajarish
        exec($command);

        // Natijani ko'rsatish
        echo "PDF fayl muvaffaqiyatli kichraytirildi. <a href='/pdf/uploads/" . basename($outputFile) . "'>Yangi faylni yuklab olish</a>";

        // Faylni 1 soatdan keyin o'chirish
        $timeToDelete = 60 * 60; // 1 soat (60 daqiqa * 60 soniya)

        // Faylni o'chirish uchun kutish
        sleep($timeToDelete);

        // Faylni o'chirish
        unlink($uploadFile);
        unlink($outputFile);

        echo "<br>Fayl 1 soat o'tgach o'chirildi.";
    } else {
        echo "Faylni yuklashda xatolik yuz berdi.";
    }
} else {
    echo "Fayl tanlanmagan.";
}
?>
<!-- Asosiy sahifaga qaytish tugmasi -->
<br><a href="index.html"><button>Asosiy sahifaga qaytish</button></a>
