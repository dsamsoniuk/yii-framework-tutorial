<?php

namespace app\components\SharePoint;

use app\modules\log\models\Log;

class SharePointFile extends SharePointApi {
    public $spApi;
    /**
     * @param InterfaceSharePoint $spApi
     */
    public function __construct(InterfaceSharePoint $spApi) {
        $this->spApi = $spApi;
    }
    /**
     * Wyslij plik 
     */
    public function upload(string $filePath, string $destinyFolder = 'Shared documents/'): bool{
        try {

            if (file_exists($filePath) == false) {
                throw new SharePointException('upload - Plik nie istnieje');
            }

            $client         = $this->spApi->getClient();
            $targetFolder   = $client->getWeb()->getFolderByServerRelativeUrl($destinyFolder);
            $uploadFile     = $targetFolder->uploadFile(basename($filePath),file_get_contents($filePath));
            $client->executeQuery();
            // print "File {$uploadFile->getServerRelativeUrl()} has been uploaded\r\n";

        } catch (\Exception $e) {
            $this->spApi->addError($e->getMessage());
            return false;
        }

        return true;
    }
    
}