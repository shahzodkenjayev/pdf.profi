<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $uploadDir = '/var/www/html/pdf/uploads/';
    $filename = substr($_FILES['file']['name'], 0, 50);
    $filename = preg_replace('/[^A-Za-z0-9\-.]/', '_', $filename);
    $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $uploadFile = $uploadDir . basename($filename);

    if (mime_content_type($_FILES['file']['tmp_name']) !== 'application/pdf') {
        echo "Faqat PDF fayllarini yuklash mumkin..";
        exit;
    }

    if (!is_writable($uploadDir)) {
        echo "Yuklash papkasi yozish huquqiga ega emas.";
        exit;
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        $outputFile = $uploadDir . 'compressed_' . basename($filename);
        $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook -dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . escapeshellarg($outputFile) . " " . escapeshellarg($uploadFile);
        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            echo "PDF muvaffaqiyatli kichraytirildi. <a href='/pdf/uploads/" . basename($outputFile) . "'>Yuklab olish</a>";
        } else {
            echo "Xatolik yuz berdi: " . implode("\n", $output);
        }
    } else {
        echo "Faylni yuklashda xatolik yuz berdi.";
    }
} else {
    echo "Fayl tanlanmagan.";
}
?>
