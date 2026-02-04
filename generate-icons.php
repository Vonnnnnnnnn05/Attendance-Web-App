<?php
/**
 * Generate PWA Icons
 * This script generates all required icon sizes for the PWA
 */

// Icon sizes required for PWA
$sizes = [72, 96, 128, 144, 152, 192, 384, 512];

// Output directory
$outputDir = __DIR__ . '/assets/images/';

// Create output directory if it doesn't exist
if (!file_exists($outputDir)) {
    mkdir($outputDir, 0755, true);
}

echo "Generating PWA Icons...\n\n";

foreach ($sizes as $size) {
    $filename = "icon-{$size}x{$size}.png";
    $filepath = $outputDir . $filename;
    
    // Create image
    $img = imagecreatetruecolor($size, $size);
    
    // Allocate colors
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    $gray = imagecolorallocate($img, 51, 51, 51);
    
    // Fill background with white
    imagefill($img, 0, 0, $white);
    
    // Calculate scale
    $scale = $size / 512;
    $centerX = $size / 2;
    $centerY = $size / 2;
    
    // Draw graduation cap (simplified)
    // Cap polygon (top diamond shape)
    $capPoints = [
        $centerX, $centerY - (80 * $scale),  // top point
        $centerX + (120 * $scale), $centerY - (20 * $scale),  // right point
        $centerX, $centerY + (20 * $scale),   // bottom point
        $centerX - (120 * $scale), $centerY - (20 * $scale)   // left point
    ];
    imagefilledpolygon($img, $capPoints, 4, $black);
    
    // Draw book rectangle
    $bookX = $centerX - (50 * $scale);
    $bookY = $centerY + (50 * $scale);
    $bookWidth = 100 * $scale;
    $bookHeight = 60 * $scale;
    imagefilledrectangle($img, $bookX, $bookY, $bookX + $bookWidth, $bookY + $bookHeight, $black);
    
    // Draw book spine line
    imageline($img, $bookX, $bookY, $bookX, $bookY + $bookHeight, $white);
    imageline($img, $centerX, $bookY, $centerX, $bookY + $bookHeight, $white);
    
    // Add text "SMS" at bottom
    $fontSize = max(12, $size / 12);
    $text = "SMS";
    $fontFile = null; // Use default GD font
    
    // Calculate text position
    $textY = $centerY + (180 * $scale);
    
    // For larger icons, try to use a TrueType font if available
    if ($size >= 128 && function_exists('imagettftext')) {
        // Try to find a system font
        $possibleFonts = [
            'C:/Windows/Fonts/arial.ttf',
            'C:/Windows/Fonts/calibri.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/System/Library/Fonts/Helvetica.ttc'
        ];
        
        foreach ($possibleFonts as $font) {
            if (file_exists($font)) {
                $fontFile = $font;
                break;
            }
        }
        
        if ($fontFile) {
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
            $textWidth = abs($bbox[4] - $bbox[0]);
            $textX = $centerX - ($textWidth / 2);
            imagettftext($img, $fontSize, 0, $textX, $textY, $black, $fontFile, $text);
        }
    }
    
    // If no TrueType font available or small size, use default GD font
    if (!$fontFile || $size < 128) {
        $gdFont = 5; // Largest built-in font
        $textWidth = imagefontwidth($gdFont) * strlen($text);
        $textX = $centerX - ($textWidth / 2);
        imagestring($img, $gdFont, $textX, $textY - 10, $text, $black);
    }
    
    // Save PNG
    imagepng($img, $filepath, 9);
    imagedestroy($img);
    
    echo "✓ Generated: $filename (" . $size . "x" . $size . ")\n";
}

echo "\n✅ All icons generated successfully!\n";
echo "Icons saved to: $outputDir\n";
?>
