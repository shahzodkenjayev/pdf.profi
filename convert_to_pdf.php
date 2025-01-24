<?php
$type = $_GET['type']; // URL parametridan turini olish (jpg, png, word)
?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF ni <?php echo ucfirst($type); ?> ga aylantirish</title>
</head>
<body>
    <h1>PDF ni <?php echo ucfirst($type); ?> ga aylantirish</h1>

    <?php if ($type == 'jpg') { ?>
        <form action="convert_from_pdf.php" method="post" enctype="multipart/form-data">
            <label for="pdf_to_jpg">PDF faylini JPG ga aylantirish:</label>
            <input type="file" name="file" id="pdf_to_jpg" accept=".pdf" required>
            <button type="submit" name="action" value="pdf_to_jpg">PDF ni JPG ga aylantirish</button>
        </form>
    <?php } elseif ($type == 'png') { ?>
        <form action="convert_from_pdf.php" method="post" enctype="multipart/form-data">
            <label for="pdf_to_png">PDF faylini PNG ga aylantirish:</label>
            <input type="file" name="file" id="pdf_to_png" accept=".pdf" required>
            <button type="submit" name="action" value="pdf_to_png">PDF ni PNG ga aylantirish</button>
        </form>
    <?php } elseif ($type == 'word') { ?>
        <form action="convert_from_pdf.php" method="post" enctype="multipart/form-data">
            <label for="pdf_to_word">PDF faylini Word ga aylantirish:</label>
            <input type="file" name="file" id="pdf_to_word" accept=".pdf" required>
            <button type="submit" name="action" value="pdf_to_word">PDF ni Word ga aylantirish</button>
        </form>
    <?php } ?>

    <p><a href="index.html">Asosiy sahifaga qaytish</a></p>
</body>
</html>
