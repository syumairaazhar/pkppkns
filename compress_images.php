<?php
function compressImage($source, $quality = 75) {
    $info = getimagesize($source);

    if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg') {
        $image = imagecreatefromjpeg($source);
        imagejpeg($image, $source, $quality);
    } 
    elseif ($info['mime'] == 'image/png') {
        // Convert PNG to JPG to save more space
        $image = imagecreatefrompng($source);
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        $white = imagecolorallocate($bg, 255, 255, 255);
        imagefilledrectangle($bg, 0, 0, imagesx($image), imagesy($image), $white);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagejpeg($bg, $source, $quality);
    }
}

function compressFolderImages($folder) {
    $images = glob($folder . "*.{jpg,jpeg,png,JPG,JPEG,PNG}", GLOB_BRACE);

    foreach ($images as $img) {
        compressImage($img, 70); 
    }

    echo "Compression completed for: $folder";
}

// Example usage:
if (isset($_GET['path'])) {
    $folder = rtrim($_GET['path'], '/') . '/';
    compressFolderImages($folder);
} else {
    echo "Add ?path=img/2023/event-folder-name/ to compress";
}
?>
