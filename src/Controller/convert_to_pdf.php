<?php
require "pdfcrowd.php";

try {
    // Créez une instance du client API avec vos identifiants PDFCrowd
    $client = new \Pdfcrowd\HtmlToPdfClient("mariemabouda", "6eea80806557431897016595f3581371");

    // URL à convertir en PDF (vous devez passer cette URL depuis votre JavaScript)
    $url = $_POST['url'];

    // Chemin où vous souhaitez enregistrer le fichier PDF généré (temporaire dans cet exemple)
    $pdfFilePath = "temp_pdf/statistiques_reclamations.pdf";

    // Exécutez la conversion et écrivez le résultat dans un fichier
    $client->convertUrlToFile($url, $pdfFilePath);

    // Retournez le chemin du fichier PDF généré à JavaScript
    echo json_encode(array("pdfFilePath" => $pdfFilePath));
} catch (\Pdfcrowd\Error $why) {
    // Gérez les erreurs éventuelles
    error_log("Pdfcrowd Error: {$why}\n");
    echo json_encode(array("error" => "Une erreur s'est produite lors de la conversion en PDF."));
}
?>
