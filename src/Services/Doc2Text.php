<?php

namespace Application\Services;

/**
 * Class Doc2Text
 *
 * @see https://melcutepenguin.medium.com/php-doc-or-docx-word-file-to-txt-extract-content-example-91c8f6b80a7b
 *
 * @package Application\Services
 * @author Jérémy GUERIBA
 */
class Doc2Text
{
    /**
     * @var string
     */
    private string $filename;

    /**
     * @param $filePath
     */
    public function __construct($filePath)
    {
        $this->filename = $filePath;
    }

    /**
     * @return string
     */
    private function readDoc(): string
    {
        $fileHandle = fopen($this->filename, "r");
        $line = @fread($fileHandle, filesize($this->filename));
        $lines = explode(chr(0x0D),$line);
        $text = "";

        foreach($lines as $line) {
            $pos = strpos($line, chr(0x00));

            if (!(($pos !== FALSE) || (strlen($line)==0))) {
                $text .= $line." ";
            }
        }

        $text = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$text);

        return $text;
    }

    /**
     * @return false|string
     */
    private function readDocx()
    {
        $stripedContent = '';
        $content = '';

        $zip = new \ZipArchive();
        if ($zip->open($this->filename) === true) {
            if (($index = $zip->locateName('word/document.xml')) !== false) {
                $content .= $zip->getFromIndex($index);
                $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
                $content = str_replace('</w:r></w:p>', "\r\n", $content);
                $stripedContent = strip_tags($content);
            }
            $zip->close();
        } else {
            return false;
        }

        return $stripedContent;
    }

//    /**
//     * @return false|string
//     */
//    private function readDocx()
//    {
//        $stripedContent = '';
//        $content = '';
//        $zip = zip_open($this->filename);
//
//        if (!$zip || is_numeric($zip)) {
//            return false;
//        }
//
//        while ($zipEntry = zip_read($zip)) {
//            if (zip_entry_open($zip, $zipEntry) == FALSE) continue;
//            if (zip_entry_name($zipEntry) != "word/document.xml") continue;
//            $content .= zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));
//            zip_entry_close($zipEntry);
//        }
//
//        zip_close($zip);
//        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
//        $content = str_replace('</w:r></w:p>', "\r\n", $content);
//        $stripedContent = strip_tags($content);
//
//        return $stripedContent;
//    }

    /**
     * @return string
     */
    public function convertToText()
    {
        if(isset($this->filename) && !file_exists($this->filename)) {
            return "File Not exists";
        }
        $fileArray = pathinfo($this->filename);
        $file_ext  = $fileArray['extension'];
        if($file_ext == "doc" || $file_ext == "docx")
        {
            if($file_ext == "doc") {
                return $this->readDoc();
            } else {
                return $this->readDocx();
            }
        } else {
            return "Invalid File Type";
        }
    }
}