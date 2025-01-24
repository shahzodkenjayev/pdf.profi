<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $uploadDir = '/var/www/html/pdf/uploads';
    $filename = $_FILES['file']['name'];

    // Fayl nomini 50 ta belgidan ortiq bo'lmasligini ta'minlash
    $filename = substr($filename, 0, 50);
    $filename = preg_replace('/[^A-Za-z0-9\-\.]/', '_', $filename); // kengaytmalarni ham tozalash

    // Fayl kengaytmasini olish
    $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    // Fayl turi tekshiruvi (faqat PDF fayllarini qabul qilish)
    if ($_FILES['file']['type'] != 'application/pdf') {
        echo "Faqat PDF fayllarini yuklash mumkin.";
        exit;
    }

    // Fayl kengaytmasini .pdf ga o'zgartirish
    if ($fileExt != 'pdf') {
        $filename = preg_replace('/\.[^.]+$/', '', $filename); // Kengaytmani olib tashlash
        $filename .= '.pdf'; // Yangi kengaytmada .pdf qo'shish
    }

    // Yuklash papkasiga yozish huquqlarini tekshirish
    if (!is_writable($uploadDir)) {
        echo "Yuklash papkasi yozish huquqiga ega emas.";
        exit;
    }

    $uploadFile = $uploadDir . basename($filename);

    // Faylni yuklash
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        // Kichraytirish
        $outputFile = $uploadDir . 'compressed_' . basename($filename);
        $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$outputFile $uploadFile";
        
        // Komandani bajarish
        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        // Natijani tekshirish
        if ($resultCode != 0) {
            echo "Xatolik yuz berdi: " . implode("\n", $output);
        } else {
            // Natijani ko'rsatish
            echo "PDF fayl muvaffaqiyatli kichraytirildi. <a href='/pdf/uploads/" . basename($outputFile) . "'>Yangi faylni yuklab olish</a>";

            // Faylni o'chirish
            if (file_exists($uploadFile)) {
                unlink($uploadFile);
            }
            if (file_exists($outputFile)) {
                unlink($outputFile);
            }

            echo "<br>Fayl o'chirildi.";
        }
    } else {
        echo "Faylni yuklashda xatolik yuz berdi.";
    }
} else {
    echo "Fayl tanlanmagan.";
}
?>

<!-- HTML forma fayl tanlash va yuborish uchun -->
<form action="compress_pdf.php" method="POST" enctype="multipart/form-data">
    <label for="file">PDF faylni tanlang:</label>
    <input type="file" name="file" id="file" accept="application/pdf" required>
    <button type="submit">Faylni yuklash va kichraytirish</button>
</form>

<!-- Asosiy sahifaga qaytish tugmasi -->
<br><a href="index.html"><button>Asosiy sahifaga qaytish</button></a>
