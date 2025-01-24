<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $uploadDir = '/var/www/html/pdf/uploads';
    $filename = $_FILES['file']['name'];

    // Fayl nomini 50 ta belgidan ortiq bo'lmasligini ta'minlash
    $filename = substr($filename, 0, 50); // 50 ta belgidan ortiq nomni kesish

    // Maxsus belgilarni va bo'shliqlarni tozalash
    $filename = preg_replace('/[^A-Za-z0-9\-_.]/', '_', $filename); // faqat raqam, harf, va tirnoq belgilari qoladi

    // Yuklanadigan fayl manzilini to'g'ri belgilash
    $uploadFile = $uploadDir . basename($filename);

    // Faylni yuklash
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {

        // PDF dan JPG ga aylantirish
        if ($_POST['action'] == 'pdf_to_jpg') {
            $outputFile = $uploadDir . basename($filename, '.pdf') . '.jpg';
            $command = "magick convert $uploadFile $outputFile";  // ImageMagickni ishlatish

            // Komandani bajarish
            exec($command);

            // Natijani ko'rsatish
            echo "PDF fayl muvaffaqiyatli JPG ga aylantirildi. <a href='/pdf/uploads/" . basename($outputFile) . "'>JPG faylni yuklab olish</a>";
        }

        // PDF dan PNG ga aylantirish
        if ($_POST['action'] == 'pdf_to_png') {
            $outputFile = $uploadDir . basename($filename, '.pdf') . '.png';
            $command = "magick convert $uploadFile $outputFile";  // ImageMagickni ishlatish

            // Komandani bajarish
            exec($command);

            // Natijani ko'rsatish
            echo "PDF fayl muvaffaqiyatli PNG ga aylantirildi. <a href='/pdf/uploads/" . basename($outputFile) . "'>PNG faylni yuklab olish</a>";
        }

        // PDF dan Word ga aylantirish
        if ($_POST['action'] == 'pdf_to_word') {
            $outputFile = $uploadDir . basename($filename, '.pdf') . '.docx';
            $command = "unoconv -f docx $uploadFile";  // Unoconvni ishlatish

            // Komandani bajarish
            exec($command);

            // Natijani ko'rsatish
            echo "PDF fayl muvaffaqiyatli Word ga aylantirildi. <a href='/pdf/uploads/" . basename($outputFile) . "'>Word faylni yuklab olish</a>";
        }

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
