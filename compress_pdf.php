<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    // Faylni tekshirish
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo "Fayl tanlanmagan yoki xatolik yuz berdi.";
        exit;
    }

    $uploadDir = '/var/www/html/pdf/uploads/';
    $filename = $_FILES['file']['name'];

    // Fayl nomini to'g'irlash (uzunlikni cheklash)
    $filename = substr($filename, 0, 50);
    $filename = preg_replace('/[^A-Za-z0-9\-.]/', '_', $filename); // Fayl nomidan maxsus belgilarni olib tashlash

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
        $outputFile = $uploadDir . 'cd_' . basename($filename);
        $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook -dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . escapeshellarg($outputFile) . " " . escapeshellarg($uploadFile);

        // Komandani bajarish
        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        // Natijani tekshirish
        if ($resultCode != 0) {
            echo "Xatolik yuz berdi: " . implode("\n", $output);
        } else {
            // Natijani ko'rsatish
            echo "PDF fayl muvaffaqiyatli kichraytirildi. <a href='/uploads/" . basename($outputFile) . "'>Yangi faylni yuklab olish</a>";
        }
    } else {
        echo "Faylni yuklashda xatolik yuz berdi.";
    }
} else {
    echo "Fayl tanlanmagan yoki xatolik yuz berdi.";
}
?>

<!-- HTML forma fayl tanlash va yuborish uchun -->
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://profiuniversity.uz/site/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konvertatsiya qilish</title>
</head>
<body>
    <a href="https://profiuniversity.uz/" class="logo">
        <img class="logo_first" src="logo.svg" alt="">
        <img class="logo_second" src="logo_white.svg" alt="">
    </a>

<div class="button-container">
<form id="uploadForm" action="compress_pdf.php" method="POST" enctype="multipart/form-data">
    <label for="file">PDF faylni tanlang:</label>
    
    <input type="file" name="file" id="file" accept="application/pdf" required>
    <button class = "button8" type="submit">Faylni yuklash va kichraytirish</button>
</form>

<!-- Asosiy sahifaga qaytish tugmasi -->
<br><a href="index.html"><button class = "button9" >Asosiy sahifaga qaytish</button></a>
</div>
<script>
    const uploadForm = document.getElementById('uploadForm');
    uploadForm.addEventListener('submit', function(e) {
        // Fayl tanlanganini tekshirish
        if (document.getElementById('file').files.length === 0) {
            alert('Iltimos, faylni tanlang!');
            e.preventDefault(); // Forma yuborilmasligi uchun to'xtatish
        }
    });
</script>
