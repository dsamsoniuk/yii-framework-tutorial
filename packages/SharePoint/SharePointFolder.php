<?php

namespace app\components\SharePoint;

class SharePointFolder {
    
    public $spApi;
    /**
     * @param InterfaceSharePoint $spApi
     */
    public function __construct(InterfaceSharePoint $spApi) {
        $this->spApi = $spApi;
    }
    /**
     * Stworz folder
     */
    public function create(string $folderName, string $destinyFolder = 'Shared documents'):bool {

        try {
            $client     = $this->spApi->getClient();
            $rootFolder = $client->getWeb()->getFolderByServerRelativeUrl($destinyFolder);
            $newFolder  = $rootFolder->getFolders()->add($folderName)->executeQuery();
            // print($newFolder->getServerRelativeUrl());
        } catch (\Exception $e) {
            $this->spApi->addError($e->getMessage());
            return false;
        }

        return true;
    }
    /** 
     * Pobierz liste folderów wg folderu w SharePoint
     */
    public function getList($folder = "Shared documents"):array {

        $list = [];
        try {
            $client     = $this->spApi->getClient();
            $parentpath = $client->getWeb()->getFolderByServerRelativeUrl($folder);
            $folders    = $parentpath->getFolders();
            $client->load($folders);
            $client->executeQuery();

            foreach ($folders->getData() as $folder) {
                $list[] = $folder->getProperty("ServerRelativeUrl");
            }

        } catch (\Exception $e) {
            $this->spApi->addError($e->getMessage());
            return [];
        }
        return $list;
    }
    /** 
     * Pobierz folder na podstawie nazwy z SharePoint
     */
    public function get($folder = "Shared documents")
    {
        $result = null;
        try {
            $client = $this->spApi->getClient();
            $result = $client->getWeb()->getFolderByServerRelativeUrl($folder)->get()->executeQuery();
        } catch (\Exception $e) {
            $this->spApi->addError($e->getMessage());
            return null;
        }
        return $result;
    }
}