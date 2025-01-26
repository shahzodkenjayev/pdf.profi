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

    // Fayl turi tekshiruvi (faqat DOC yoki DOCX fayllarini qabul qilish)
    if ($_FILES['file']['type'] != 'application/msword' && $_FILES['file']['type'] != 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        echo "Faqat DOC yoki DOCX fayllarini yuklash mumkin.";
        exit;
    }

    // Fayl nomini PDF kengaytmasi bilan o'zgartirish
    $filename = preg_replace('/\.[^.]+$/', '', $filename); // Kengaytmani olib tashlash
    $outputFile = $uploadDir . basename($filename) . '.pdf';

    // Yuklash papkasiga yozish huquqlarini tekshirish
    if (!is_writable($uploadDir)) {
        echo "Yuklash papkasi yozish huquqiga ega emas.";
        exit;
    }

    // Faylni yuklash
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . basename($filename))) {
        // Unoconv yordamida Word faylini PDF ga o'zgartirish
        $command = "unoconv -f pdf " . escapeshellarg($uploadDir . basename($filename));
        
        // Komandani bajarish
        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        // Natijani tekshirish
        if ($resultCode != 0) {
            echo "Xatolik yuz berdi: " . implode("\n", $output);
        } else {
            // Natijani ko'rsatish
            echo "Word fayli muvaffaqiyatli PDF ga aylantirildi. <a href='/uploads/" . basename($outputFile) . "'>Yangi faylni yuklab olish</a>";
        }
    } else {
        echo "Faylni yuklashda xatolik yuz berdi.";
    }
} else {
    echo "Fayl tanlanmagan yoki xatolik yuz berdi.";
}
?>

<!-- HTML forma fayl tanlash va yuborish uchun -->
<form id="uploadForm" action="convert_word_to_pdf.php" method="POST" enctype="multipart/form-data">
    <label for="file">Word faylni tanlang:</label>
    <input type="file" name="file" id="file" accept=".doc,.docx" required>
    <button type="submit" class="btn btn-primary">Faylni yuklash va PDF ga aylantirish</button>
</form>

<!-- Asosiy sahifaga qaytish tugmasi -->
<br><a href="index.html"><button class="btn btn-secondary">Asosiy sahifaga qaytish</button></a>

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
