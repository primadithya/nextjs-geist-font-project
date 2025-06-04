<?php
require_once 'config.php';

// Require composer autoload
require_once 'vendor/autoload.php';

// Reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// Get student ID from URL
$id = $_GET['id'] ?? null;

if (!$id) {
    die("No student ID provided");
}

try {
    // Fetch student data
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? AND status_kelulusan = 'LULUS'");
    $stmt->execute([$id]);
    $student = $stmt->fetch();

    if (!$student) {
        die("Student not found or not eligible for certificate");
    }

    // Initialize dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);

    $dompdf = new Dompdf($options);

    // Certificate HTML content
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Graduation Certificate</title>
        <style>
            body {
                font-family: "Times New Roman", Times, serif;
                padding: 40px;
                color: #000;
            }
            .certificate {
                border: 2px solid #000;
                padding: 20px;
                text-align: center;
            }
            .header {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 30px;
            }
            .school-name {
                font-size: 28px;
                font-weight: bold;
                margin: 20px 0;
            }
            .content {
                font-size: 16px;
                line-height: 1.6;
                margin: 30px 0;
            }
            .student-name {
                font-size: 24px;
                font-weight: bold;
                color: #000080;
                margin: 20px 0;
            }
            .footer {
                margin-top: 50px;
                text-align: right;
            }
            .signature-line {
                width: 200px;
                border-top: 1px solid #000;
                margin-left: auto;
                margin-right: 50px;
                margin-top: 50px;
            }
        </style>
    </head>
    <body>
        <div class="certificate">
            <div class="header">CERTIFICATE OF GRADUATION</div>
            
            <div class="school-name">
                ' . htmlspecialchars($student['asal_sekolah']) . '
            </div>
            
            <div class="content">
                This is to certify that
                
                <div class="student-name">
                    ' . htmlspecialchars($student['nama_siswa']) . '
                </div>
                
                with Registration Number: ' . htmlspecialchars($student['no_pendaftaran']) . '<br>
                has successfully completed all the requirements<br>
                and is hereby declared as<br><br>
                
                <strong style="font-size: 20px; color: #006400;">GRADUATED</strong>
            </div>
            
            <div class="footer">
                <div>Issued on: ' . date('F d, Y') . '</div>
                <div class="signature-line"></div>
                <div>School Principal</div>
            </div>
        </div>
    </body>
    </html>';

    // Load HTML content
    $dompdf->loadHtml($html);

    // Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF
    $dompdf->render();

    // Generate file name
    $filename = 'Certificate_' . $student['no_pendaftaran'] . '.pdf';

    // Output PDF to browser
    $dompdf->stream($filename, array('Attachment' => false));
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
